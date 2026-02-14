<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventFinanceRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class EventFinanceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Get finance request for an event
     */
    public function index(Request $request, $eventId): JsonResponse
    {
        $event = Event::findOrFail($eventId);
        
        // Check permissions
        if (!$request->user()->can('view', $event)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $financeRequest = $event->financeRequest;

        if (!$financeRequest) {
            return response()->json([
                'success' => true,
                'data' => null,
                'message' => 'No finance request found for this event'
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => $financeRequest
        ]);
    }

    /**
     * Create or update finance request for an event
     */
    public function store(Request $request, $eventId): JsonResponse
    {
        $event = Event::findOrFail($eventId);
        
        // Check permissions
        if (!$request->user()->can('update', $event)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'logistics_total' => 'required|numeric|min:0',
            'equipment_total' => 'required|numeric|min:0',
            'other_total' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
        ]);

        $validated['event_id'] = $eventId;
        $validated['grand_total'] = $validated['logistics_total'] + 
                                  ($validated['equipment_total'] ?? 0) + 
                                  ($validated['other_total'] ?? 0);
        $validated['submitted_by'] = Auth::id();
        $validated['status'] = 'pending';

        $financeRequest = EventFinanceRequest::updateOrCreate(
            ['event_id' => $eventId],
            $validated
        );

        return response()->json([
            'success' => true,
            'message' => 'Finance request submitted successfully',
            'data' => $financeRequest
        ], 201);
    }

    /**
     * Update finance request status (for admins)
     */
    public function updateStatus(Request $request, $eventId, $financeRequestId): JsonResponse
    {
        $event = Event::findOrFail($eventId);
        $financeRequest = $event->financeRequest()->findOrFail($financeRequestId);
        
        // Check permissions - only admins can approve/reject
        if (!$request->user()->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'status' => ['required', Rule::in(['pending', 'approved', 'rejected'])],
            'notes' => 'nullable|string|max:1000',
        ]);

        $financeRequest->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Finance request status updated successfully',
            'data' => $financeRequest
        ]);
    }

    /**
     * Get all pending finance requests (for admins)
     */
    public function pendingRequests(Request $request): JsonResponse
    {
        // Only admins can view all pending requests
        if (!$request->user()->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $pendingRequests = EventFinanceRequest::with('event.requestedBy')
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $pendingRequests
        ]);
    }

    /**
     * Get finance requests for user's events
     */
    public function myRequests(Request $request): JsonResponse
    {
        $requests = EventFinanceRequest::with('event')
            ->whereHas('event', function($query) use ($request) {
                $query->where('requested_by', $request->user()->id);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $requests
        ]);
    }

    /**
     * Get finance dashboard stats
     */
    public function dashboard(Request $request): JsonResponse
    {
        // Only admins can view dashboard stats
        if (!$request->user()->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $stats = [
            'total_requests' => EventFinanceRequest::count(),
            'pending_requests' => EventFinanceRequest::where('status', 'pending')->count(),
            'approved_requests' => EventFinanceRequest::where('status', 'approved')->count(),
            'rejected_requests' => EventFinanceRequest::where('status', 'rejected')->count(),
            'total_grand_total' => EventFinanceRequest::sum('grand_total'),
            'formatted_total_grand_total' => '₱' . number_format(EventFinanceRequest::sum('grand_total'), 2),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }
}
