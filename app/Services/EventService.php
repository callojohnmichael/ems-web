<?php

namespace App\Services;

use App\Models\Event;
use App\Models\User;
use App\Notifications\EventRescheduledEmailNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EventService
{
    public function __construct(
        private InAppNotificationService $inAppNotificationService
    ) {}

    /**
     * -----------------------------------------------------------
     * DASHBOARD DATA METHODS
     * -----------------------------------------------------------
     */

    public function getPendingEvents()
    {
        return Event::where('status', Event::STATUS_PENDING_APPROVAL)
            ->with('requestedBy')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getUpcomingEvents()
    {
        return Event::whereIn('status', ['approved', 'published'])
            ->where('start_at', '>=', Carbon::now())
            ->orderBy('start_at', 'asc')
            ->get();
    }

    public function getUserEvents(User $user)
    {
        return Event::query()
            ->where('requested_by', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getPublishedEvents()
    {
        return Event::query()
            ->where('status', Event::STATUS_PUBLISHED)
            ->where('start_at', '>=', Carbon::now())
            ->with('requestedBy')
            ->orderBy('start_at', 'asc')
            ->get();
    }

    public function getTotalEventsCount(): int
    {
        return Event::count();
    }

    /**
     * -----------------------------------------------------------
     * EVENT ACTION METHODS
     * -----------------------------------------------------------
     */

    public function updateEvent(Event $event, array $data, User $user): Event
    {
        return DB::transaction(function () use ($event, $data, $user) {

            $event->update([
                'title'       => $data['title'] ?? $event->title,
                'description' => $data['description'] ?? $event->description,
                'start_at'    => $data['start_at'] ?? $event->start_at,
                'end_at'      => $data['end_at'] ?? $event->end_at,
                'venue_id'    => $data['venue_id'] ?? $event->venue_id,
            ]);

            // ---------------------------
            // Logistics (resources)
            // ---------------------------
            if (isset($data['resources']) && is_array($data['resources'])) {

                $event->resourceAllocations()->delete();

                foreach ($data['resources'] as $resourceId => $qty) {
                    $qty = (int) $qty;

                    if ($qty > 0) {
                        $event->resourceAllocations()->create([
                            'resource_id' => $resourceId,
                            'quantity'    => $qty,
                        ]);
                    }
                }
            }

            // ---------------------------
            // Committee (participants)
            // ---------------------------
            if (isset($data['committee']) && is_array($data['committee'])) {

                $event->participants()->where('type', 'committee')->delete();

                foreach ($data['committee'] as $member) {
                    if (!empty($member['employee_id'])) {
                        $event->participants()->create([
                            'employee_id' => $member['employee_id'],
                            'role'        => $member['role'] ?? null,
                            'type'        => 'committee',
                        ]);
                    }
                }
            }

            // ---------------------------
            // Finance (budget items)
            // ---------------------------
            if (isset($data['budget_items']) && is_array($data['budget_items'])) {

                $event->budget()->delete();

                foreach ($data['budget_items'] as $item) {
                    if (!empty($item['description'])) {
                        $event->budget()->create([
                            'description'      => $item['description'],
                            'estimated_amount' => $item['amount'] ?? 0,
                            'status'           => 'pending_finance_approval',
                        ]);
                    }
                }
            }

            // ---------------------------
            // Timeline history
            // ---------------------------
            $event->histories()->create([
                'user_id' => $user->id,
                'action'  => 'Event Updated',
                'note'    => 'Event details, logistics, committee, and finance were updated.',
            ]);

            Log::info('Event updated (full update)', [
                'event_id' => $event->id,
                'user_id'  => $user->id,
                'title'    => $event->title,
            ]);

            return $event;
        });
    }

    public function approveEvent(Event $event, User $admin): Event
    {
        return DB::transaction(function () use ($event, $admin) {

            $event->update([
                'status' => 'approved',
            ]);

            $event->histories()->create([
                'user_id' => $admin->id,
                'action'  => 'Event Approved',
                'note'    => 'Admin manually approved the event.',
            ]);

            Log::info('Event manually approved', [
                'event_id' => $event->id,
                'admin_id' => $admin->id,
                'title'    => $event->title,
            ]);

            if ($event->requested_by) {
                $requester = User::query()->find($event->requested_by);

                if ($requester) {
                    $this->inAppNotificationService->notifyUsers(
                        users: [$requester],
                        title: 'Event request approved',
                        message: "Your event \"{$event->title}\" has been approved.",
                        url: route('events.show', $event),
                        category: 'activity',
                        meta: [
                            'event_id' => $event->id,
                            'status' => $event->status,
                        ],
                        excludeUserId: $admin->id,
                    );
                }
            }

            return $event;
        });
    }

    public function rejectEvent(Event $event, User $admin, ?string $reason = null): Event
    {
        return DB::transaction(function () use ($event, $admin, $reason) {

            $event->update([
                'status' => 'rejected',
            ]);

            $event->histories()->create([
                'user_id' => $admin->id,
                'action'  => 'Event Rejected',
                'note'    => $reason
                    ? "Rejected reason: {$reason}"
                    : "Event was rejected.",
            ]);

            Log::info('Event rejected', [
                'event_id' => $event->id,
                'admin_id' => $admin->id,
                'title'    => $event->title,
                'reason'   => $reason,
            ]);

            if ($event->requested_by) {
                $requester = User::query()->find($event->requested_by);

                if ($requester) {
                    $message = "Your event \"{$event->title}\" was rejected.";

                    if ($reason) {
                        $message .= " Reason: {$reason}";
                    }

                    $this->inAppNotificationService->notifyUsers(
                        users: [$requester],
                        title: 'Event request rejected',
                        message: $message,
                        url: route('events.show', $event),
                        category: 'activity',
                        meta: [
                            'event_id' => $event->id,
                            'status' => $event->status,
                        ],
                        excludeUserId: $admin->id,
                    );
                }
            }

            return $event;
        });
    }

    /**
     * Notify the event requester (in-app + email) when the event has been rescheduled.
     * Call after an update that changed start_at or end_at. Event should be refreshed.
     */
    public function notifyRequesterRescheduled(Event $event, User $actingUser): void
    {
        if (! $event->requested_by || $event->requested_by === $actingUser->id) {
            return;
        }

        $requester = User::query()->find($event->requested_by);
        if (! $requester) {
            return;
        }

        $start = $event->start_at instanceof Carbon
            ? $event->start_at->format('F j, Y g:i A')
            : Carbon::parse($event->start_at)->format('F j, Y g:i A');
        $end = $event->end_at instanceof Carbon
            ? $event->end_at->format('g:i A')
            : Carbon::parse($event->end_at)->format('g:i A');
        $message = "Your event \"{$event->title}\" has been rescheduled to {$start} – {$end}.";
        $url = route('events.show', $event);
        $title = 'Event rescheduled';

        $this->inAppNotificationService->notifyUsers(
            users: [$requester],
            title: $title,
            message: $message,
            url: $url,
            category: 'activity',
            meta: [
                'event_id' => $event->id,
                'status' => $event->status,
            ],
            excludeUserId: $actingUser->id,
        );

        $requester->notify(new EventRescheduledEmailNotification(
            subject: $title,
            message: $message,
            url: $url,
        ));
    }

    public function publishEvent(Event $event, User $admin): Event
    {
        return DB::transaction(function () use ($event, $admin) {

            if ($event->status !== 'approved') {
                throw new \InvalidArgumentException('Only approved events can be published.');
            }

            $event->update([
                'status' => 'published',
            ]);

            $event->histories()->create([
                'user_id' => $admin->id,
                'action'  => 'Event Published',
                'note'    => 'Event is now live and visible to non-admin users.',
            ]);

            Log::info('Event published', [
                'event_id' => $event->id,
                'admin_id' => $admin->id,
                'title'    => $event->title,
            ]);

            $recipients = User::query()->where('id', '!=', $admin->id)->get();

            $this->inAppNotificationService->notifyUsers(
                users: $recipients,
                title: 'New event published',
                message: "\"{$event->title}\" is now published.",
                url: route('events.show', $event),
                category: 'activity',
                meta: [
                    'event_id' => $event->id,
                    'status' => $event->status,
                ],
                excludeUserId: $admin->id,
            );

            return $event;
        });
    }

    public function approveGate(Event $event, string $gate, User $user): Event
    {
        $column = "is_{$gate}_approved";

        if (!in_array($column, [
            'is_venue_approved',
            'is_logistics_approved',
            'is_finance_approved'
        ])) {
            throw new \InvalidArgumentException("Invalid gate: {$gate}");
        }

        return DB::transaction(function () use ($event, $gate, $column, $user) {

            $event->update([$column => true]);

            $event->histories()->create([
                'user_id' => $user->id,
                'action'  => ucfirst($gate) . ' Approved',
                'note'    => "The {$gate} department has cleared their portion of the request.",
            ]);

            $event->refresh();

            if (
                $event->is_venue_approved &&
                $event->is_logistics_approved &&
                $event->is_finance_approved
            ) {
                $event->update(['status' => 'approved']);

                $event->histories()->create([
                    'user_id' => $user->id,
                    'action'  => 'Full Approval',
                    'note'    => 'All departmental gates cleared. Ready to publish.',
                ]);
            }

            return $event;
        });
    }

    /**
     * -----------------------------------------------------------
     * CALENDAR METHODS (FullCalendar format)
     * -----------------------------------------------------------
     */

    /**
     * Admin calendar: show all events
     */
    public function getAllEventsForCalendar()
    {
        $events = Event::query()
            ->select('id', 'title', 'start_at', 'end_at', 'status')
            ->orderBy('start_at')
            ->get();

        return $this->formatEventsForCalendar($events);
    }

    /**
     * User calendar: show user's own events + published events
     */
    public function getUserEventsForCalendar(User $user)
    {
        $events = Event::query()
            ->select('id', 'title', 'start_at', 'end_at', 'status', 'requested_by')
            ->where(function ($q) use ($user) {
                $q->where('requested_by', $user->id)
                  ->orWhere('status', 'published');
            })
            ->orderBy('start_at')
            ->get();

        return $this->formatEventsForCalendar($events);
    }

    /**
     * Multimedia/other staff calendar: show only published events
     */
    public function getPublishedEventsForCalendar()
    {
        $events = Event::query()
            ->select('id', 'title', 'start_at', 'end_at', 'status')
            ->where('status', 'published')
            ->orderBy('start_at')
            ->get();

        return $this->formatEventsForCalendar($events);
    }

    /**
     * Shared formatter for FullCalendar
     */
    private function formatEventsForCalendar($events)
    {
        $colors = [
            Event::STATUS_PENDING_APPROVAL => '#f39c12', // Orange
            'approved'         => '#00c0ef', // Aqua
            'published'        => '#00a65a', // Green
            'rejected'         => '#dd4b39', // Red
        ];

        return $events->map(function ($event) use ($colors) {

            return [
                'id'     => $event->id,
                'title'  => $event->title,
                'start'  => $event->start_at?->toIso8601String(),
                'end'    => $event->end_at?->toIso8601String(),
                'status' => $event->status,
                'color'  => $colors[$event->status] ?? '#3c8dbc',

                // FIXED: correct route
                'url'    => route('events.show', $event->id),
            ];
        })->values();
    }
}
