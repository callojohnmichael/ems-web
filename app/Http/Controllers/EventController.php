<?php

namespace App\Http\Controllers;

use App\Http\Requests\EventFormRequest;
use App\Models\Event;
use App\Models\Venue;
use App\Services\EventService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class EventController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        private EventService $eventService
    ) {}

    /**
     * Calendar index with enforced role-based visibility.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        if ($user->isAdmin()) {
            // Admin: everything EXCEPT rejected + deleted
            $events = Event::with(['requestedBy', 'venue'])
                ->whereNotIn('status', ['rejected', 'deleted'])
                ->get();
        } else {
            // User / Multimedia: published only
            $events = Event::with(['requestedBy', 'venue'])
                ->where('status', 'published')
                ->get();
        }

        return view('events.index', compact('events'));
    }

    public function create(): View
    {
        $venues = Venue::orderBy('name')->get();
        return view('events.create', compact('venues'));
    }

    public function store(EventFormRequest $request): RedirectResponse
    {
        $this->eventService->createEventRequest(
            $request->validated(),
            $request->user()
        );

        return redirect()
            ->route('events.index')
            ->with('success', 'Event request submitted and pending approval.');
    }

    public function show(Event $event): View
    {
        $this->authorize('view', $event);
        $event->load(['requestedBy', 'venue']);

        return view('events.show', compact('event'));
    }

    public function edit(Event $event): View
    {
        $this->authorize('update', $event);

        $venues = Venue::orderBy('name')->get();
        return view('events.edit', compact('event', 'venues'));
    }

    public function update(EventFormRequest $request, Event $event): RedirectResponse
    {
        $this->authorize('update', $event);

        $this->eventService->updateEvent(
            $event,
            $request->validated(),
            $request->user()
        );

        return redirect()
            ->route('events.index')
            ->with('success', 'Event updated successfully.');
    }

    public function approve(Event $event): RedirectResponse
    {
        $this->authorize('approve', $event);

        $this->eventService->approveEvent($event, Auth::user());

        return redirect()
            ->route('events.index')
            ->with('success', 'Event approved.');
    }

    public function reject(Request $request, Event $event): RedirectResponse
    {
        $this->authorize('reject', $event);

        $request->validate([
            'reason' => ['nullable', 'string', 'max:500'],
        ]);

        $this->eventService->rejectEvent(
            $event,
            $request->user(),
            $request->input('reason')
        );

        return redirect()
            ->route('events.index')
            ->with('success', 'Event rejected.');
    }

    public function publish(Event $event): RedirectResponse
    {
        $this->authorize('publish', $event);

        $this->eventService->publishEvent($event, Auth::user());

        return redirect()
            ->route('events.index')
            ->with('success', 'Event published.');
    }

    public function destroy(Event $event): RedirectResponse
    {
        $this->authorize('delete', $event);

        // Prefer soft delete if enabled
        if (method_exists($event, 'delete')) {
            $event->update(['status' => 'deleted']);
            $event->delete();
        }

        return redirect()
            ->route('events.index')
            ->with('success', 'Event deleted.');
    }

    /**
     * Admin-only bulk upload with venue_id enforcement.
     * CSV format:
     * title,start_at,end_at,description,venue_id
     */
    public function bulkUpload(Request $request): RedirectResponse
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        $request->validate([
            'file' => 'required|mimes:csv,txt|max:10240',
        ]);

        $file = $request->file('file');
        $handle = fopen($file->getRealPath(), 'r');

        if (!$handle) {
            return back()->withErrors('Unable to read uploaded file.');
        }

        // Skip header
        fgetcsv($handle);

        $count = 0;

        while (($row = fgetcsv($handle)) !== false) {
            // Stop processing when venue reference section starts
            if (isset($row[0]) && trim($row[0]) === 'VENUE_REFERENCE') {
                break;
            }

            // Required columns
            if (count($row) < 5) {
                continue;
            }

            [$title, $start, $end, $description, $venueId] = $row;

            // Enforce venue existence
            if (!Venue::where('id', $venueId)->exists()) {
                continue;
            }

            Event::create([
                'title'       => $title,
                'start_at'    => $start,
                'end_at'      => $end,
                'description' => $description,
                'venue_id'    => $venueId,
                'status'      => 'pending_approval',
                'user_id'     => Auth::id(),
            ]);

            $count++;
        }

        fclose($handle);

        return redirect()
            ->back()
            ->with('success', "{$count} events uploaded and set to pending approval.");
    }
}
