<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Services\EventService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CalendarController extends Controller
{
    public function __construct(
        private EventService $eventService
    ) {}

    public function index(Request $request): View
    {
        $user = $request->user();

        /**
         * IMPORTANT:
         * Your Blade requires $events to be Eloquent models
         * because it calls:
         * - $event->status
         * - $event->venue
         * - $event->participants
         * - $event->resourceAllocations
         * - $event->budget
         * - $event->start_at->format()
         */

        if ($user->isAdmin()) {
            $eventIds = collect($this->eventService->getAllEventsForCalendar())
                ->map(fn($e) => is_array($e) ? ($e['id'] ?? null) : ($e->id ?? null))
                ->filter()
                ->unique()
                ->values();
        } elseif ($user->isUser()) {
            $eventIds = collect($this->eventService->getUserEventsForCalendar($user))
                ->map(fn($e) => is_array($e) ? ($e['id'] ?? null) : ($e->id ?? null))
                ->filter()
                ->unique()
                ->values();
        } else {
            $eventIds = collect($this->eventService->getPublishedEventsForCalendar())
                ->map(fn($e) => is_array($e) ? ($e['id'] ?? null) : ($e->id ?? null))
                ->filter()
                ->unique()
                ->values();
        }

        // Load FULL Eloquent models so your blade will work without changing anything
        $events = Event::query()
            ->with([
                'venue',
                'participants',
                'resourceAllocations.resource',
                'budget',
            ])
            ->whereIn('id', $eventIds)
            ->orderBy('start_at')
            ->get();

        return view('calendar.index', compact('events'));
    }
}
