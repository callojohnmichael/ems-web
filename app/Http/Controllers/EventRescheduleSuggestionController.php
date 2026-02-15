<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventRescheduleSuggestion;
use App\Models\User;
use App\Models\Venue;
use App\Notifications\RescheduleSuggestionEmailNotification;
use Carbon\Carbon;
use App\Services\EventService;
use App\Services\InAppNotificationService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class EventRescheduleSuggestionController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        private EventService $eventService,
        private InAppNotificationService $inAppNotificationService
    ) {}

    public function create(Event $event): View
    {
        $this->authorize('suggestReschedule', $event);

        return view('events.reschedule-suggestion-form', compact('event'));
    }

    public function store(Request $request, Event $event): RedirectResponse
    {
        $this->authorize('suggestReschedule', $event);

        $validated = $request->validate([
            'suggested_start_at' => ['required', 'date', 'after:now'],
            'suggested_end_at' => ['required', 'date', 'after:suggested_start_at'],
            'reason' => ['nullable', 'string', 'max:1000'],
        ]);

        $event->load(['venue', 'venueBookings']);
        $venueId = (int) $event->venue_id;
        $locationIds = $event->venueBookings->pluck('venue_location_id')->unique()->filter()->values()->all();

        if ($venueId && ! empty($locationIds)) {
            $startAt = Carbon::parse($validated['suggested_start_at'])->setTimezone(config('app.timezone'))->format('Y-m-d H:i:s');
            $endAt = Carbon::parse($validated['suggested_end_at'])->setTimezone(config('app.timezone'))->format('Y-m-d H:i:s');
            $hasConflict = Venue::hasLocationBookingConflict(
                $venueId,
                $locationIds,
                $startAt,
                $endAt,
                $event->id
            );
            if ($hasConflict) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'suggested_start_at' => 'One or more selected venue locations are already booked for the suggested date and time. Please choose different dates.',
                    ]);
            }
        }

        $suggestion = $event->rescheduleSuggestions()->create([
            'suggested_start_at' => $validated['suggested_start_at'],
            'suggested_end_at' => $validated['suggested_end_at'],
            'reason' => $validated['reason'] ?? null,
            'requested_by' => Auth::id(),
            'status' => EventRescheduleSuggestion::STATUS_PENDING,
        ]);

        $userName = Auth::user()->name;
        $start = \Carbon\Carbon::parse($suggestion->suggested_start_at)->format('F j, Y g:i A');
        $end = \Carbon\Carbon::parse($suggestion->suggested_end_at)->format('g:i A');
        $message = "{$userName} suggested rescheduling \"{$event->title}\" to {$start} – {$end}.";
        $url = route('events.show', $event);
        $title = 'Reschedule suggestion submitted';

        $admins = $this->inAppNotificationService->adminUsers();
        $this->inAppNotificationService->notifyUsers(
            users: $admins,
            title: $title,
            message: $message,
            url: $url,
            category: 'activity',
            meta: ['event_id' => $event->id, 'suggestion_id' => $suggestion->id],
            excludeUserId: Auth::id(),
        );
        $admins->reject(fn (User $u) => $u->id === Auth::id())->each(
            fn (User $u) => $u->notify(new RescheduleSuggestionEmailNotification(
                subject: $title,
                message: $message,
                url: $url,
            ))
        );

        return redirect()
            ->route('events.show', $event)
            ->with('toast', 'Your reschedule suggestion has been submitted. Admins will be notified.');
    }

    public function accept(EventRescheduleSuggestion $suggestion): RedirectResponse
    {
        $this->authorize('update', $suggestion->event);

        if (! $suggestion->isPending()) {
            return back()->withErrors(['suggestion' => 'This suggestion has already been processed.']);
        }

        $event = $suggestion->event;
        $event->load(['venue', 'venueBookings']);
        $venueId = (int) $event->venue_id;
        $locationIds = $event->venueBookings->pluck('venue_location_id')->unique()->filter()->values()->all();

        if ($venueId && ! empty($locationIds)) {
            $startAt = Carbon::parse($suggestion->suggested_start_at)->format('Y-m-d H:i:s');
            $endAt = Carbon::parse($suggestion->suggested_end_at)->format('Y-m-d H:i:s');
            $hasConflict = Venue::hasLocationBookingConflict(
                $venueId,
                $locationIds,
                $startAt,
                $endAt,
                $event->id
            );
            if ($hasConflict) {
                return back()->withErrors([
                    'suggestion' => 'One or more venue locations are already booked for the suggested dates. Cannot accept.',
                ]);
            }
        }

        DB::transaction(function () use ($suggestion, $event) {
            $event->update([
                'start_at' => $suggestion->suggested_start_at,
                'end_at' => $suggestion->suggested_end_at,
            ]);
            $event->venueBookings()->update([
                'start_at' => $suggestion->suggested_start_at,
                'end_at' => $suggestion->suggested_end_at,
            ]);
            $suggestion->update(['status' => EventRescheduleSuggestion::STATUS_ACCEPTED]);
            $event->histories()->create([
                'user_id' => Auth::id(),
                'action' => 'Event Rescheduled',
                'note' => 'Dates updated from an accepted reschedule suggestion.',
            ]);
        });

        $event->refresh();
        $this->eventService->notifyRequesterRescheduled($event, Auth::user());

        return redirect()
            ->route('events.show', $event)
            ->with('success', 'Reschedule suggestion accepted. The requester has been notified.');
    }

    public function decline(EventRescheduleSuggestion $suggestion): RedirectResponse
    {
        $this->authorize('update', $suggestion->event);

        if (! $suggestion->isPending()) {
            return back()->withErrors(['suggestion' => 'This suggestion has already been processed.']);
        }

        $suggestion->update(['status' => EventRescheduleSuggestion::STATUS_DECLINED]);

        return back()->with('success', 'Reschedule suggestion declined.');
    }
}
