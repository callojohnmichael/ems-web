<?php

namespace App\Policies;

use App\Models\Event;
use App\Models\User;

class EventPolicy
{
    /**
     * Determine whether the user can view any events.
     */
    public function viewAny(User $user): bool
    {
        return auth()->check(); // All authenticated users can view events
    }

    /**
     * Determine whether the user can view a specific event.
     */
    public function view(User $user, Event $event): bool
    {
        // Owners can view their own events
        if ($user->id === $event->requested_by) {
            return true;
        }

        // Admins can view all events
        if ($user->isAdmin()) {
            return true;
        }

        // Multimedia staff can view only published events
        if ($user->isMultimediaStaff() && $event->status === 'published') {
            return true;
        }

        // Regular users can view only published events
        if ($user->isUser() && $event->status === 'published') {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can create events.
     */
    public function create(User $user): bool
    {
        return $user->isUser(); // Only normal users can request new events
    }

    /**
     * Determine whether the user can update events.
     */
    public function update(User $user, Event $event): bool
    {
        return $user->isAdmin(); // Only admins can update events
    }

    /**
     * Determine whether the user can delete events.
     */
    public function delete(User $user, Event $event): bool
    {
        return $user->isAdmin(); // Only admins can delete events
    }

    /**
     * Determine whether the user can approve events.
     */
    public function approve(User $user, Event $event): bool
    {
        return $user->isAdmin() && $event->status === 'pending_approvals';
    }

    /**
     * Determine whether the user can reject events.
     */
    public function reject(User $user, Event $event): bool
    {
        return $user->isAdmin() && $event->status === 'pending_approvals';
    }

    /**
     * Determine whether the user can publish events.
     */
    public function publish(User $user, Event $event): bool
    {
        return $user->isAdmin() && $event->status === 'approved';
    }

    /**
     * Determine whether the user can cancel events.
     */
    public function cancel(User $user, Event $event): bool
    {
        return $user->isAdmin() && in_array($event->status, ['approved', 'published']);
    }

    /**
     * Determine whether the user can mark events as complete.
     */
    public function complete(User $user, Event $event): bool
    {
        return $user->isAdmin() &&
               $event->status === 'published' &&
               $event->end_at < now();
    }

    /**
     * Optional: Gate-specific approvals (venue, logistics, finance)
     * Only admins can approve gates.
     */
    public function approveGate(User $user, Event $event, string $gate): bool
    {
        $validGates = ['venue', 'logistics', 'finance'];
        return $user->isAdmin() && in_array($gate, $validGates) && $event->status === 'pending_approvals';
    }
}
