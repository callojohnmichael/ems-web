<?php

namespace App\Http\Controllers;

use App\Models\Venue;
use App\Services\EventService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CalendarController extends Controller
{
    public function __construct(
        private EventService $eventService
    ) {}

    public function index(Request $request): View
    {
        $user = $request->user();
        $venues = Venue::query()->orderBy('name')->get();
        $eventsByVenue = [];

        foreach ($venues as $venue) {
            $eventsByVenue[$venue->id] = $this->eventService->getEventsForCalendarByVenue($venue->id, $user);
        }

        // Main calendar: all events (role-based), same as original
        $mainCalendarEvents = $user->isAdmin()
            ? $this->eventService->getAllEventsForCalendar()
            : ($user->isUser()
                ? $this->eventService->getUserEventsForCalendar($user)
                : $this->eventService->getPublishedEventsForCalendar());

        $calendarPayload = [
            'mainCalendarEvents' => $mainCalendarEvents,
            'venues' => $venues->map(fn ($v) => ['id' => $v->id, 'name' => $v->name])->values()->all(),
            'eventsByVenue' => $eventsByVenue,
        ];

        return view('calendar.index', [
            'venues' => $venues,
            'eventsByVenue' => $eventsByVenue,
            'mainCalendarEvents' => $mainCalendarEvents,
            'calendarPayload' => $calendarPayload,
        ]);
    }

    public function export(Request $request): StreamedResponse
    {
        $request->validate([
            'format' => 'required|in:csv',
            'venue' => 'nullable|integer|exists:venues,id',
        ]);

        $user = $request->user();
        $venueId = $request->has('venue') ? (int) $request->get('venue') : null;
        $events = $this->eventService->getEventsForCalendarExport($user, $venueId);

        $venueName = $venueId
            ? Venue::find($venueId)->name
            : 'all-venues';
        $slug = \Illuminate\Support\Str::slug($venueName);
        $filename = "calendar-{$slug}-" . now()->format('Y-m-d-His') . '.csv';

        return response()->streamDownload(function () use ($events) {
            $handle = fopen('php://output', 'w');
            // UTF-8 BOM for Excel
            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, ['Title', 'Start', 'End', 'Status', 'Venue']);

            foreach ($events as $event) {
                fputcsv($handle, [
                    $event->title,
                    $event->start_at?->format('Y-m-d H:i'),
                    $event->end_at?->format('Y-m-d H:i'),
                    $event->status,
                    $event->venue?->name ?? '',
                ]);
            }

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}
