<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Participant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ParticipantController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display participants grouped by Event with Committee details.
     */
    public function index(Request $request): View
    {
        $user = auth()->user();
        $isAdmin = $user->isAdmin();

        // 1. Fetch Events (Scoped to owner if not admin)
        // We load participants with their user roles and employee details
        $eventsQuery = Event::with(['participants.user.roles', 'participants.employee']);

        if (!$isAdmin) {
            $eventsQuery->where('user_id', $user->id);
        }

        $events = $eventsQuery->orderBy('start_at', 'desc')->paginate(10);

        // 2. Statistics for the Header
        // Shows totals for all events the user has access to
        $statsQuery = Participant::query();
        if (!$isAdmin) {
            $statsQuery->whereHas('event', fn($q) => $q->where('user_id', $user->id));
        }

        $totalParticipants = $statsQuery->count();
        $totalRegistered = $statsQuery->where('status', 'confirmed')->count();
        $canManageParticipants = $isAdmin || $user->hasPermissionTo('manage participants');

        return view('admin.participants.index', compact(
            'events',
            'totalParticipants',
            'totalRegistered',
            'canManageParticipants'
        ));
    }

    /**
     * Show form to add participant - Restricted to Published events.
     */
    public function create(Event $event): View|RedirectResponse
    {
        $this->authorize('update', $event);

        // Strict Check: Only published events can accept new participants (Admin override)
        if ($event->status !== 'published' && !auth()->user()->isAdmin()) {
            return back()->withErrors('Registration is closed. This event is not currently "Published".');
        }

        $users = User::with('roles')->get(); 
        return view('admin.participants.create', compact('event', 'users'));
    }

    /**
     * Store participant with Employee and Type info.
     */
    public function store(Request $request, Event $event): RedirectResponse
    {
        $this->authorize('update', $event);

        if ($event->status !== 'published' && !auth()->user()->isAdmin()) {
            return back()->withErrors('Cannot add participants to an unpublished event.');
        }

        $validated = $request->validate([
            'user_id'     => 'nullable|exists:users,id',
            'employee_id' => 'nullable|exists:employees,id',
            'name'        => 'required|string|max:255',
            'email'       => 'required|email|max:255',
            'phone'       => 'nullable|string|max:32',
            'role'        => 'nullable|string|max:255', 
            'type'        => 'required|string|in:participant,committee',
            'status'      => 'required|in:pending,confirmed,attended,absent',
        ]);

        $event->participants()->create($validated);

        return redirect()
            ->route('admin.participants.index')
            ->with('success', "{$validated['name']} added successfully.");
    }

    /**
     * Show specific participant details.
     */
    public function show(Event $event, Participant $participant): View
    {
        if ($participant->event_id !== $event->id) abort(404);
        
        // Ownership check: Can only view if Admin or Event Owner
        if (!auth()->user()->isAdmin() && $event->user_id !== auth()->id()) {
            abort(403);
        }

        $participant->load(['user.roles', 'employee', 'attendances']);
        return view('admin.participants.show', compact('event', 'participant'));
    }

    /**
     * Edit participant - Restricted to Published events.
     */
    public function edit(Event $event, Participant $participant): View|RedirectResponse
    {
        if ($participant->event_id !== $event->id) abort(404);
        $this->authorize('update', $event);

        if ($event->status !== 'published' && !auth()->user()->isAdmin()) {
            return back()->withErrors('Modifications are locked while the event is not Published.');
        }

        return view('admin.participants.edit', compact('event', 'participant'));
    }

    /**
     * Update participant record.
     */
    public function update(Request $request, Event $event, Participant $participant): RedirectResponse
    {
        if ($participant->event_id !== $event->id) abort(404);
        $this->authorize('update', $event);

        if ($event->status !== 'published' && !auth()->user()->isAdmin()) {
            return back()->withErrors('Updates are locked for this event status.');
        }

        $validated = $request->validate([
            'name'   => 'required|string|max:255',
            'email'  => 'required|email|max:255',
            'status' => 'required|in:pending,confirmed,attended,absent',
            'role'   => 'nullable|string|max:255',
            'type'   => 'required|string|in:participant,committee',
        ]);

        $participant->update($validated);

        return redirect()
            ->route('admin.participants.index')
            ->with('success', 'Participant updated.');
    }

    /**
     * Remove participant.
     */
    public function destroy(Event $event, Participant $participant): RedirectResponse
    {
        $this->authorize('update', $event);
        if ($participant->event_id !== $event->id) abort(404);

        $participant->delete();
        return back()->with('success', 'Participant removed.');
    }

    /**
     * Export logic (Groups Roles and Types).
     */
    public function export(Event $event)
    {
        $this->authorize('update', $event);

        $participants = $event->participants()->with('user.roles')->get();
        $filename = "participants-{$event->slug}-" . now()->format('Ymd') . ".csv";

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=$filename",
        ];

        $callback = function () use ($participants) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Name', 'Email', 'Type', 'Specific Role', 'Status', 'Committee Tags']);

            foreach ($participants as $p) {
                fputcsv($file, [
                    $p->name,
                    $p->email,
                    ucfirst($p->type),
                    $p->role ?? 'N/A',
                    $p->status,
                    $p->user ? $p->user->roles->pluck('name')->implode(', ') : 'None'
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}