<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Participant;
use App\Models\User;
use App\Models\Employee;
use App\Notifications\ParticipantTicketNotification;
use App\Services\EventCheckInService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ParticipantController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        private readonly EventCheckInService $checkInService
    ) {}

    /* =======================================================
       LIST EVENTS + PARTICIPANTS
    ======================================================= */
    public function index(Event $event = null): View
    {
        $user = auth()->user();
        $isAdmin = $user->isAdmin();
        // If an event was provided (route: admin.events.participants.index), show full event details
        if ($event) {
            // ensure event is visible to non-admin owners
            if (!$isAdmin && $event->requested_by !== $user->id) {
                abort(403);
            }

            $event->load(['participants.user.roles', 'participants.employee', 'venue', 'custodianRequests', 'financeRequest', 'logisticsItems']);

            $committees = $event->participants->where('type', 'committee');
            $standardParticipants = $event->participants->where('type', 'participant');

            $participantCount = $event->participants->count();
            $attendedCount = $event->participants->where('status', 'attended')->count();
            $absentCount = $event->participants->where('status', 'absent')->count();

            $employees = Employee::orderBy('last_name')->orderBy('first_name')->get();
            $users = User::orderBy('name')->get();
            $existingEmployeeIds = $event->participants()->whereNotNull('employee_id')->pluck('employee_id')->unique()->all();
            $existingUserIds = $event->participants()->whereNotNull('user_id')->pluck('user_id')->unique()->all();

            return view('admin.participants.event', compact(
                'event',
                'committees',
                'standardParticipants',
                'participantCount',
                'attendedCount',
                'absentCount',
                'employees',
                'users',
                'existingEmployeeIds',
                'existingUserIds'
            ));
        }

        $events = Event::query()
            ->with(['participants.user.roles', 'participants.employee'])
            ->when(!$isAdmin, fn ($q) =>
                $q->where('requested_by', $user->id)
            )
            // Only show published events on the participants index
            ->where('status', 'published')
            ->orderByDesc('start_at')
            ->paginate(10);

        $statsQuery = Participant::query()
            ->when(!$isAdmin, fn ($q) =>
                $q->whereHas('event', fn ($e) =>
                    $e->where('requested_by', $user->id)
                )
            );

        // Stat counts should only consider participants on published events
        $statsQuery = (clone $statsQuery)->whereHas('event', fn($e) => $e->where('status', 'published'));

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

        $user = auth()->user();
        $isAdmin = $user->isAdmin();

        // If event is not published and the user is not an admin,
        // only allow adding committee members (enforced on store as well).
        $onlyCommittee = $event->status !== 'published' && !$isAdmin;

        $existingEmployeeIds = $event->participants()->whereNotNull('employee_id')->pluck('employee_id')->unique()->all();
        $existingUserIds = $event->participants()->whereNotNull('user_id')->pluck('user_id')->unique()->all();

        return view('admin.participants.create', [
            'event' => $event,
            'employees' => Employee::orderBy('last_name')->orderBy('first_name')->get(),
            'users' => User::orderBy('name')->get(),
            'onlyCommittee' => $onlyCommittee,
            'existingEmployeeIds' => $existingEmployeeIds,
            'existingUserIds' => $existingUserIds,
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

        // If the event is not published, only allow adding committee members
        // unless the current user is an admin. This enforces: can only add
        // non-committee participants when the event is published.
        $currentUser = auth()->user();
        $isAdmin = $currentUser->isAdmin();

        if ($event->status !== 'published' && !$isAdmin) {
            // If request indicates a non-committee participant, reject.
            if (($validated['type'] ?? '') !== 'committee') {
                return back()->withErrors('Event is not published. Only committee members can be added at this time.')->withInput();
            }
        }

        /**
         * 🔐 FORCE EVENT ID
         * Never trust request input for foreign keys
         */
        $validated['event_id'] = $event->id;

        /**
         * ✅ Manual participants allowed
         * (user_id & employee_id can be NULL)
         */
        $participant = Participant::create($validated);
        $participant = $this->checkInService->ensureParticipantCredentials($participant);

        if (! empty($participant->display_email) && $participant->display_email !== 'N/A') {
            $ticketUrl = $this->checkInService->buildTicketUrl($participant);

            \Notification::route('mail', $participant->display_email)
                ->notify(new ParticipantTicketNotification($event, $participant, $ticketUrl));
        }

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

        if (!auth()->user()->isAdmin() && $event->requested_by !== auth()->id()) {
            abort(403);
        }

        $participant = $this->checkInService->ensureParticipantCredentials($participant);
        $participant->load(['user.roles', 'employee', 'attendances', 'checkedInBy']);

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
