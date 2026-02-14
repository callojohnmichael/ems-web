<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Venue;
use App\Models\Participant;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class EventController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Get all events (with filtering and search)
     */
    public function index(Request $request): JsonResponse
    {
        $query = Event::with([
            'requestedBy',
            'venue',
            'participants',
            'logisticsItems',
            'budget',
            'financeRequest',
            'custodianRequests'
        ])->latest();

        // Role-based filtering
        $user = $request->user();
        if ($user->isAdmin()) {
            $query->whereNotIn('status', ['deleted']);
        } else {
            $query->where(function ($q) use ($user) {
                $q->where('requested_by', $user->id)
                    ->whereNotIn('status', ['deleted'])
                    ->orWhere('status', 'published');
            });
        }

        // Search filter
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%')
                  ->orWhereHas('venue', function($subQ) use ($search) {
                      $subQ->where('name', 'like', '%' . $search . '%');
                  });
            });
        }

        // Status filter
        if ($request->has('status')) {
            $query->where('status', $request->get('status'));
        }

        // Date range filter
        if ($request->has('start_date')) {
            $query->where('start_at', '>=', $request->get('start_date'));
        }
        if ($request->has('end_date')) {
            $query->where('end_at', '<=', $request->get('end_date'));
        }

        $events = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $events
        ]);
    }

    /**
     * Get a specific event
     */
    public function show(Request $request, $id): JsonResponse
    {
        $event = Event::with([
            'requestedBy',
            'venue',
            'participants.employee',
            'logisticsItems.resource',
            'budget',
            'financeRequest',
            'custodianRequests.custodianMaterial'
        ])->findOrFail($id);

        // Check permissions
        if (!$request->user()->can('view', $event)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $event
        ]);
    }

    /**
     * Create a new event
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'start_at' => 'required|date|after:now',
            'end_at' => 'required|date|after:start_at',
            'venue_id' => 'required|exists:venues,id',
            'number_of_participants' => 'required|integer|min:1',
            'notes' => 'nullable|string|max:1000',
        ]);

        $validated['requested_by'] = Auth::id();
        $validated['status'] = 'pending_approvals';

        $event = Event::create($validated);

        // Load relationships
        $event->load(['requestedBy', 'venue']);

        return response()->json([
            'success' => true,
            'message' => 'Event created successfully',
            'data' => $event
        ], 201);
    }

    /**
     * Update an event
     */
    public function update(Request $request, $id): JsonResponse
    {
        $event = Event::findOrFail($id);

        // Check permissions
        if (!$request->user()->can('update', $event)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'start_at' => 'sometimes|date|after:now',
            'end_at' => 'sometimes|date|after:start_at',
            'venue_id' => 'sometimes|exists:venues,id',
            'number_of_participants' => 'sometimes|integer|min:1',
            'notes' => 'nullable|string|max:1000',
        ]);

        $event->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Event updated successfully',
            'data' => $event->load(['requestedBy', 'venue'])
        ]);
    }

    /**
     * Delete an event
     */
    public function destroy(Request $request, $id): JsonResponse
    {
        $event = Event::findOrFail($id);

        // Check permissions
        if (!$request->user()->can('delete', $event)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $event->update(['status' => 'deleted']);

        return response()->json([
            'success' => true,
            'message' => 'Event deleted successfully'
        ]);
    }

    /**
     * Update event status (for admins)
     */
    public function updateStatus(Request $request, $id): JsonResponse
    {
        $event = Event::findOrFail($id);

        // Only admins can update status
        if (!$request->user()->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'status' => ['required', Rule::in(['pending_approvals', 'approved', 'published', 'cancelled', 'completed'])],
            'notes' => 'nullable|string|max:1000',
        ]);

        $event->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Event status updated successfully',
            'data' => $event
        ]);
    }

    /**
     * Get my events (for current user)
     */
    public function myEvents(Request $request): JsonResponse
    {
        $events = Event::with(['venue', 'financeRequest', 'custodianRequests'])
            ->where('requested_by', $request->user()->id)
            ->whereNotIn('status', ['deleted'])
            ->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $events
        ]);
    }

    /**
     * Get event statistics
     */
    public function statistics(Request $request): JsonResponse
    {
        // Only admins can view statistics
        if (!$request->user()->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $stats = [
            'total_events' => Event::whereNotIn('status', ['deleted'])->count(),
            'pending_events' => Event::where('status', 'pending_approvals')->count(),
            'approved_events' => Event::where('status', 'approved')->count(),
            'published_events' => Event::where('status', 'published')->count(),
            'completed_events' => Event::where('status', 'completed')->count(),
            'cancelled_events' => Event::where('status', 'cancelled')->count(),
            'upcoming_events' => Event::where('start_at', '>', now())
                ->whereNotIn('status', ['deleted', 'cancelled'])
                ->count(),
            'ongoing_events' => Event::where('start_at', '<=', now())
                ->where('end_at', '>=', now())
                ->where('status', 'published')
                ->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Get available venues
     */
    public function venues(): JsonResponse
    {
        $venues = Venue::with('locations')->orderBy('name')->get();

        return response()->json([
            'success' => true,
            'data' => $venues
        ]);
    }

    /**
     * Add participant to event
     */
    public function addParticipant(Request $request, $eventId): JsonResponse
    {
        $event = Event::findOrFail($eventId);

        // Check permissions
        if (!$request->user()->can('update', $event)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'type' => ['required', Rule::in(['participant', 'committee', 'speaker'])],
            'role' => 'nullable|string|max:255',
        ]);

        $validated['event_id'] = $eventId;
        $validated['status'] = 'confirmed';

        $participant = Participant::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Participant added successfully',
            'data' => $participant->load('employee')
        ], 201);
    }

    /**
     * Remove participant from event
     */
    public function removeParticipant(Request $request, $eventId, $participantId): JsonResponse
    {
        $event = Event::findOrFail($eventId);
        $participant = $event->participants()->findOrFail($participantId);

        // Check permissions
        if (!$request->user()->can('update', $event)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $participant->delete();

        return response()->json([
            'success' => true,
            'message' => 'Participant removed successfully'
        ]);
    }
}
