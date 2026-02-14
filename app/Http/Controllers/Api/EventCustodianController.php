<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventCustodianRequest;
use App\Models\CustodianMaterial;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class EventCustodianController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Get custodian requests for an event
     */
    public function index(Request $request, $eventId): JsonResponse
    {
        $event = Event::findOrFail($eventId);
        
        // Check permissions
        if (!$request->user()->can('view', $event)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $custodianRequests = $event->custodianRequests()->with('custodianMaterial')->get();

        return response()->json([
            'success' => true,
            'data' => $custodianRequests
        ]);
    }

    /**
     * Create a new custodian request for an event
     */
    public function store(Request $request, $eventId): JsonResponse
    {
        $event = Event::findOrFail($eventId);
        
        // Check permissions
        if (!$request->user()->can('update', $event)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'custodian_material_id' => 'required|exists:custodian_materials,id',
            'quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string|max:1000',
        ]);

        $validated['event_id'] = $eventId;
        $validated['status'] = 'pending';

        $custodianRequest = EventCustodianRequest::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Custodian request created successfully',
            'data' => $custodianRequest->load('custodianMaterial')
        ], 201);
    }

    /**
     * Update a custodian request
     */
    public function update(Request $request, $eventId, $custodianRequestId): JsonResponse
    {
        $event = Event::findOrFail($eventId);
        $custodianRequest = $event->custodianRequests()->findOrFail($custodianRequestId);
        
        // Check permissions
        if (!$request->user()->can('update', $event)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'quantity' => 'sometimes|integer|min:1',
            'notes' => 'nullable|string|max:1000',
        ]);

        $custodianRequest->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Custodian request updated successfully',
            'data' => $custodianRequest->load('custodianMaterial')
        ]);
    }

    /**
     * Update custodian request status (for custodians/admins)
     */
    public function updateStatus(Request $request, $eventId, $custodianRequestId): JsonResponse
    {
        $event = Event::findOrFail($eventId);
        $custodianRequest = $event->custodianRequests()->findOrFail($custodianRequestId);
        
        // Check permissions - only admins or custodians can approve/reject
        if (!$request->user()->isAdmin() && !$request->user()->hasRole('custodian')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'status' => ['required', Rule::in(['pending', 'approved', 'rejected'])],
            'notes' => 'nullable|string|max:1000',
        ]);

        $custodianRequest->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Custodian request status updated successfully',
            'data' => $custodianRequest->load('custodianMaterial')
        ]);
    }

    /**
     * Delete a custodian request
     */
    public function destroy(Request $request, $eventId, $custodianRequestId): JsonResponse
    {
        $event = Event::findOrFail($eventId);
        $custodianRequest = $event->custodianRequests()->findOrFail($custodianRequestId);
        
        // Check permissions
        if (!$request->user()->can('update', $event)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $custodianRequest->delete();

        return response()->json([
            'success' => true,
            'message' => 'Custodian request deleted successfully'
        ]);
    }

    /**
     * Get all available custodian materials
     */
    public function materials(): JsonResponse
    {
        $materials = CustodianMaterial::orderBy('name')->get();

        return response()->json([
            'success' => true,
            'data' => $materials
        ]);
    }

    /**
     * Get all pending custodian requests (for custodians/admins)
     */
    public function pendingRequests(Request $request): JsonResponse
    {
        // Only admins or custodians can view all pending requests
        if (!$request->user()->isAdmin() && !$request->user()->hasRole('custodian')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $pendingRequests = EventCustodianRequest::with(['event.requestedBy', 'custodianMaterial'])
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $pendingRequests
        ]);
    }

    /**
     * Get custodian requests for user's events
     */
    public function myRequests(Request $request): JsonResponse
    {
        $requests = EventCustodianRequest::with(['event', 'custodianMaterial'])
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
     * Get custodian dashboard stats
     */
    public function dashboard(Request $request): JsonResponse
    {
        // Only admins or custodians can view dashboard stats
        if (!$request->user()->isAdmin() && !$request->user()->hasRole('custodian')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $stats = [
            'total_requests' => EventCustodianRequest::count(),
            'pending_requests' => EventCustodianRequest::where('status', 'pending')->count(),
            'approved_requests' => EventCustodianRequest::where('status', 'approved')->count(),
            'rejected_requests' => EventCustodianRequest::where('status', 'rejected')->count(),
            'total_materials' => CustodianMaterial::count(),
            'low_stock_materials' => CustodianMaterial::where('stock', '<=', 10)->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Get custodian summary for an event
     */
    public function summary(Request $request, $eventId): JsonResponse
    {
        $event = Event::findOrFail($eventId);
        
        // Check permissions
        if (!$request->user()->can('view', $event)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $custodianRequests = $event->custodianRequests;
        $totalItems = $custodianRequests->count();
        $approvedItems = $custodianRequests->where('status', 'approved')->count();
        $pendingItems = $custodianRequests->where('status', 'pending')->count();

        return response()->json([
            'success' => true,
            'data' => [
                'total_items' => $totalItems,
                'approved_items' => $approvedItems,
                'pending_items' => $pendingItems,
                'items' => $custodianRequests->load('custodianMaterial')
            ]
        ]);
    }
}
