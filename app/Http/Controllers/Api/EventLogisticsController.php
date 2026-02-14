<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventLogisticsItem;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class EventLogisticsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Get logistics items for an event
     */
    public function index(Request $request, $eventId): JsonResponse
    {
        $event = Event::findOrFail($eventId);
        
        // Check permissions
        if (!$request->user()->can('view', $event)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $logistics = $event->logisticsItems()->with('resource')->get();

        return response()->json([
            'success' => true,
            'data' => $logistics
        ]);
    }

    /**
     * Create a new logistics item for an event
     */
    public function store(Request $request, $eventId): JsonResponse
    {
        $event = Event::findOrFail($eventId);
        
        // Check permissions
        if (!$request->user()->can('update', $event)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'resource_id' => 'required|exists:resources,id',
            'description' => 'required|string|max:255',
            'quantity' => 'required|integer|min:1',
            'unit_price' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
        ]);

        $validated['event_id'] = $eventId;
        $validated['subtotal'] = $validated['quantity'] * $validated['unit_price'];

        $logistics = EventLogisticsItem::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Logistics item created successfully',
            'data' => $logistics->load('resource')
        ], 201);
    }

    /**
     * Update a logistics item
     */
    public function update(Request $request, $eventId, $logisticsId): JsonResponse
    {
        $event = Event::findOrFail($eventId);
        $logistics = $event->logisticsItems()->findOrFail($logisticsId);
        
        // Check permissions
        if (!$request->user()->can('update', $event)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'resource_id' => 'sometimes|exists:resources,id',
            'description' => 'sometimes|string|max:255',
            'quantity' => 'sometimes|integer|min:1',
            'unit_price' => 'sometimes|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
        ]);

        if (isset($validated['quantity']) || isset($validated['unit_price'])) {
            $quantity = $validated['quantity'] ?? $logistics->quantity;
            $unitPrice = $validated['unit_price'] ?? $logistics->unit_price;
            $validated['subtotal'] = $quantity * $unitPrice;
        }

        $logistics->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Logistics item updated successfully',
            'data' => $logistics->load('resource')
        ]);
    }

    /**
     * Delete a logistics item
     */
    public function destroy(Request $request, $eventId, $logisticsId): JsonResponse
    {
        $event = Event::findOrFail($eventId);
        $logistics = $event->logisticsItems()->findOrFail($logisticsId);
        
        // Check permissions
        if (!$request->user()->can('update', $event)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $logistics->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logistics item deleted successfully'
        ]);
    }

    /**
     * Get all available resources
     */
    public function resources(): JsonResponse
    {
        $resources = \App\Models\Resource::orderBy('name')->get();

        return response()->json([
            'success' => true,
            'data' => $resources
        ]);
    }

    /**
     * Get logistics summary for an event
     */
    public function summary(Request $request, $eventId): JsonResponse
    {
        $event = Event::findOrFail($eventId);
        
        // Check permissions
        if (!$request->user()->can('view', $event)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $logistics = $event->logisticsItems;
        $totalItems = $logistics->count();
        $totalCost = $logistics->sum('subtotal');

        return response()->json([
            'success' => true,
            'data' => [
                'total_items' => $totalItems,
                'total_cost' => $totalCost,
                'formatted_total_cost' => '₱' . number_format($totalCost, 2),
                'items' => $logistics->load('resource')
            ]
        ]);
    }
}
