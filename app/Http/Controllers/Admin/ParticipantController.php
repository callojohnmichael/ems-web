<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Participant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ParticipantController extends Controller
{
    /**
     * Display a listing of all participants (optionally filtered by event).
     */
    public function index(Event $event = null)
    {
        $user = auth()->user();
        $canManageParticipants = $user->isAdmin() || $user->hasPermissionTo('manage participants');

        if ($event) {
            // Show participants for a specific event
            $participants = $event->participants()->paginate(15);
            return view('admin.participants.index', [
                'event' => $event,
                'participants' => $participants,
                'canManageParticipants' => $canManageParticipants,
            ]);
        }

        // Show all participants across all events
        $participants = Participant::with('event', 'user')->paginate(15);
        return view('admin.participants.index', [
            'participants' => $participants,
            'canManageParticipants' => $canManageParticipants,
        ]);
    }

    /**
     * Show the form for creating a new participant.
     */
    public function create(Event $event)
    {
        $this->authorize('update', $event);
        
        if ($event->status !== 'published') {
            return back()->withErrors('Participants can only be added to published events.');
        }
        
        $users = User::all();
        $canManageParticipants = auth()->user()->isAdmin() || auth()->user()->hasPermissionTo('manage participants');

        return view('admin.participants.create', [
            'event' => $event,
            'users' => $users,
            'canManageParticipants' => $canManageParticipants,
        ]);
    }

    /**
     * Store a newly created participant in storage.
     */
    public function store(Request $request, Event $event)
    {
        $this->authorize('update', $event);

        if ($event->status !== 'published') {
            return back()->withErrors('Participants can only be added to published events.');
        }

        $validated = $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:32',
            'role' => 'nullable|string|max:255',
            'type' => 'nullable|string|max:255',
        ]);

        $participant = $event->participants()->create($validated);

        return redirect()
            ->route('events.participants.index', $event)
            ->with('success', "Participant '{$validated['name']}' has been added to the event.");
    }

    /**
     * Display the specified participant.
     */
    public function show(Event $event, Participant $participant)
    {
        // Ensure participant belongs to event
        if ($participant->event_id !== $event->id) {
            abort(404);
        }

        $canManageParticipants = auth()->user()->isAdmin() || auth()->user()->hasPermissionTo('manage participants');

        return view('admin.participants.show', [
            'event' => $event,
            'participant' => $participant,
            'canManageParticipants' => $canManageParticipants,
        ]);
    }

    /**
     * Show the form for editing the specified participant.
     */
    public function edit(Event $event, Participant $participant)
    {
        // Ensure participant belongs to event
        if ($participant->event_id !== $event->id) {
            abort(404);
        }

        $this->authorize('update', $event);

        if ($event->status !== 'published') {
            return back()->withErrors('Participants can only be edited for published events.');
        }

        $users = User::all();
        $canManageParticipants = auth()->user()->isAdmin() || auth()->user()->hasPermissionTo('manage participants');

        return view('admin.participants.edit', [
            'event' => $event,
            'participant' => $participant,
            'users' => $users,
            'canManageParticipants' => $canManageParticipants,
        ]);
    }

    /**
     * Update the specified participant in storage.
     */
    public function update(Request $request, Event $event, Participant $participant)
    {
        // Ensure participant belongs to event
        if ($participant->event_id !== $event->id) {
            abort(404);
        }

        $this->authorize('update', $event);

        if ($event->status !== 'published') {
            return back()->withErrors('Participants can only be edited for published events.');
        }

        $validated = $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:32',
            'role' => 'nullable|string|max:255',
            'type' => 'nullable|string|max:255',
            'status' => 'nullable|string|in:pending,confirmed,attended,absent',
        ]);

        $participant->update($validated);

        return redirect()
            ->route('events.participants.show', [$event, $participant])
            ->with('success', "Participant has been updated successfully.");
    }

    /**
     * Remove the specified participant from storage.
     */
    public function destroy(Event $event, Participant $participant)
    {
        // Ensure participant belongs to event
        if ($participant->event_id !== $event->id) {
            abort(404);
        }

        $this->authorize('update', $event);

        $name = $participant->name;
        $participant->delete();

        return redirect()
            ->route('events.participants.index', $event)
            ->with('success', "Participant '{$name}' has been removed from the event.");
    }

    /**
     * Bulk update participant statuses.
     */
    public function bulkUpdateStatus(Request $request, Event $event)
    {
        $this->authorize('update', $event);

        $validated = $request->validate([
            'participant_ids' => 'required|array',
            'participant_ids.*' => 'integer|exists:participants,id',
            'status' => 'required|string|in:pending,confirmed,attended,absent',
        ]);

        $updated = $event->participants()
            ->whereIn('id', $validated['participant_ids'])
            ->update(['status' => $validated['status']]);

        return redirect()
            ->route('events.participants.index', $event)
            ->with('success', "{$updated} participants' status has been updated.");
    }

    /**
     * Export participants to CSV.
     */
    public function export(Event $event)
    {
        $this->authorize('update', $event);

        $participants = $event->participants()->get();

        $filename = "participants-{$event->id}-" . now()->format('Y-m-d-His') . ".csv";
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=$filename",
        ];

        $callback = function () use ($participants) {
            $file = fopen('php://output', 'w');
            
            // Headers
            fputcsv($file, ['Name', 'Email', 'Phone', 'Role', 'Type', 'Status', 'Registered At']);

            // Data
            foreach ($participants as $participant) {
                fputcsv($file, [
                    $participant->name,
                    $participant->email,
                    $participant->phone,
                    $participant->role,
                    $participant->type,
                    $participant->status,
                    $participant->registered_at,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
