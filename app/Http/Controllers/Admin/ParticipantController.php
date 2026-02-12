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

    /* =======================================================
       LIST EVENTS + PARTICIPANTS
    ======================================================= */
    public function index(): View
    {
        $user = auth()->user();
        $isAdmin = $user->isAdmin();

        $events = Event::query()
            ->with(['participants.user.roles', 'participants.employee'])
            ->when(!$isAdmin, fn ($q) =>
                $q->where('user_id', $user->id)
            )
            ->orderByDesc('start_at')
            ->paginate(10);

        $statsQuery = Participant::query()
            ->when(!$isAdmin, fn ($q) =>
                $q->whereHas('event', fn ($e) =>
                    $e->where('user_id', $user->id)
                )
            );

        return view('admin.participants.index', [
            'events'                => $events,
            'totalParticipants'     => $statsQuery->count(),
            'totalRegistered'       => (clone $statsQuery)->where('status', 'confirmed')->count(),
            'canManageParticipants' => $isAdmin || $user->can('manage participants'),
        ]);
    }

    /* =======================================================
       CREATE
    ======================================================= */
    public function create(Event $event): View|RedirectResponse
    {
        $this->authorize('update', $event);

        if ($event->status !== 'published' && !auth()->user()->isAdmin()) {
            return back()->withErrors('Event must be published to add participants.');
        }

        return view('admin.participants.create', [
            'event' => $event,
            'users' => User::with('roles')->orderBy('name')->get(),
        ]);
    }

    /* =======================================================
       STORE
    ======================================================= */
    public function store(Request $request, Event $event): RedirectResponse
    {
        $this->authorize('update', $event);

        $validated = $request->validate([
            'user_id'     => 'nullable|exists:users,id',
            'employee_id' => 'nullable|exists:employees,id',
            'name'        => 'nullable|string|max:255',
            'email'       => 'nullable|email|max:255',
            'phone'       => 'nullable|string|max:32',
            'role'        => 'nullable|string|max:255',
            'type'        => 'required|in:participant,committee',
            'status'      => 'required|in:pending,confirmed,attended,absent',
        ]);

        /**
         * 🔐 FORCE EVENT ID
         * Never trust request input for foreign keys
         */
        $validated['event_id'] = $event->id;

        /**
         * ✅ Manual participants allowed
         * (user_id & employee_id can be NULL)
         */
        Participant::create($validated);

        return redirect()
            ->route('admin.participants.index')
            ->with('success', "Participant added to {$event->title}.");
    }

    /* =======================================================
       SHOW
    ======================================================= */
    public function show(Event $event, Participant $participant): View
    {
        abort_if($participant->event_id !== $event->id, 404);

        if (!auth()->user()->isAdmin() && $event->user_id !== auth()->id()) {
            abort(403);
        }

        $participant->load(['user.roles', 'employee', 'attendances']);

        return view('admin.participants.show', compact('event', 'participant'));
    }

    /* =======================================================
       EDIT
    ======================================================= */
    public function edit(Event $event, Participant $participant): View
    {
        abort_if($participant->event_id !== $event->id, 404);
        $this->authorize('update', $event);

        return view('admin.participants.edit', compact('event', 'participant'));
    }

    /* =======================================================
       UPDATE
    ======================================================= */
    public function update(Request $request, Event $event, Participant $participant): RedirectResponse
    {
        abort_if($participant->event_id !== $event->id, 404);
        $this->authorize('update', $event);

        $validated = $request->validate([
            'name'   => 'nullable|string|max:255',
            'email'  => 'nullable|email|max:255',
            'status' => 'required|in:pending,confirmed,attended,absent',
            'role'   => 'nullable|string|max:255',
            'type'   => 'required|in:participant,committee',
        ]);

        $participant->update($validated);

        return redirect()
            ->route('admin.participants.index')
            ->with('success', 'Participant updated successfully.');
    }

    /* =======================================================
       DELETE
    ======================================================= */
    public function destroy(Event $event, Participant $participant): RedirectResponse
    {
        abort_if($participant->event_id !== $event->id, 404);
        $this->authorize('update', $event);

        $participant->delete();

        return back()->with('success', 'Participant removed.');
    }

    /* =======================================================
       EXPORT CSV
    ======================================================= */
    public function export(Event $event)
    {
        $this->authorize('update', $event);

        $participants = $event->participants()
            ->with(['user.roles', 'employee'])
            ->orderBy('created_at')
            ->get();

        $filename = "participants-{$event->slug}-" . now()->format('Y-m-d') . ".csv";

        return response()->stream(function () use ($participants) {
            $file = fopen('php://output', 'w');

            fputcsv($file, [
                'Name',
                'Email',
                'Type',
                'Role',
                'Status',
                'System Roles',
            ]);

            foreach ($participants as $p) {
                fputcsv($file, [
                    $p->display_name,
                    $p->display_email,
                    ucfirst($p->type),
                    $p->role ?? 'N/A',
                    ucfirst($p->status),
                    $p->user
                        ? $p->user->roles->pluck('name')->implode(', ')
                        : '—',
                ]);
            }

            fclose($file);
        }, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename={$filename}",
        ]);
    }
}
