<?php

namespace App\Http\Controllers;

use App\Http\Requests\EventFormRequest;
use App\Models\Event;
use App\Models\Venue;
use App\Models\Resource;
use App\Models\Employee;
use App\Models\Budget;
use App\Models\EventHistory;

// NEW (Custodian)
use App\Models\CustodianMaterial;
use App\Models\EventCustodianRequest;

// NEW (Finance)
use App\Models\EventFinanceRequest;

use App\Services\EventService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class EventController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        private EventService $eventService
    ) {}

    /**
     * Calendar index with enforced role-based visibility and latest first.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        if ($user->isAdmin()) {
            $events = Event::with(['requestedBy', 'venue'])
                ->whereNotIn('status', ['rejected', 'deleted'])
                ->latest()
                ->get();
        } else {
            $events = Event::with(['requestedBy', 'venue'])
                ->where('status', 'published')
                ->latest()
                ->get();
        }

        return view('events.index', compact('events'));
    }

    /**
     * Include Logistics, Committee, Finance, and Custodian data.
     */
    public function create(): View
    {
        $venues = Venue::orderBy('name')->get();
        $resources = Resource::orderBy('name')->get();
        $employees = Employee::orderBy('last_name')->get();

        // NEW: custodian materials
        $custodianMaterials = CustodianMaterial::orderBy('name')->get();

        return view('events.create', compact(
            'venues',
            'resources',
            'employees',
            'custodianMaterials'
        ));
    }

    /**
     * Comprehensive Store with Availability Check and Multi-Gate Approval.
     */
    public function store(EventFormRequest $request): RedirectResponse
    {
        // 1. Check Venue Availability
        $isTaken = Event::where('venue_id', $request->venue_id)
            ->whereNotIn('status', ['rejected', 'cancelled', 'deleted'])
            ->where(function ($query) use ($request) {
                $query->whereBetween('start_at', [$request->start_at, $request->end_at])
                    ->orWhereBetween('end_at', [$request->start_at, $request->end_at]);
            })->exists();

        if ($isTaken) {
            return back()->withInput()->withErrors([
                'venue_id' => 'The venue is already booked for these dates.'
            ]);
        }

        try {
            $event = DB::transaction(function () use ($request) {

                // 2. Create Event with specific Gate Booleans
                $event = Event::create([
                    'title' => $request->title,
                    'description' => $request->description,
                    'start_at' => $request->start_at,
                    'end_at' => $request->end_at,
                    'venue_id' => $request->venue_id,
                    'requested_by' => Auth::id(),
                    'status' => 'pending_approvals',
                    'is_venue_approved' => false,
                    'is_logistics_approved' => false,
                    'is_finance_approved' => false,
                ]);

                /**
                 * ===================== 3. LOGISTICS & BUDGET CALCULATION =====================
                 */
                $logisticsTotal = 0;

                // NEW FORMAT (Structured array from dynamic JS rows)
                if ($request->has('logistics_items')) {
                    foreach ($request->logistics_items as $row) {
                        $resourceId = $row['resource_id'] ?? null;
                        $qty = $row['quantity'] ?? 0;

                        if ($resourceId && $qty > 0) {
                            $resource = Resource::find($resourceId);
                            if ($resource) {
                                $logisticsTotal += ($resource->price ?? 0) * $qty;

                                $event->resourceAllocations()->create([
                                    'resource_id' => $resourceId,
                                    'quantity' => $qty,
                                ]);
                            }
                        }
                    }
                }

                // OLD FORMAT (Backward compatible / Simple Select)
                if ($request->has('resources')) {
                    foreach ($request->resources as $resourceId => $qty) {
                        if ($qty > 0) {
                            $resource = Resource::find($resourceId);
                            if ($resource) {
                                $logisticsTotal += ($resource->price ?? 0) * $qty;

                                $event->resourceAllocations()->create([
                                    'resource_id' => $resourceId,
                                    'quantity' => $qty,
                                ]);
                            }
                        }
                    }
                }

                /**
                 * ===================== NEW: FINANCE REQUEST GENERATION =====================
                 */
                if ($logisticsTotal > 0) {
                    $event->financeRequest()->create([
                        'logistics_total' => $logisticsTotal,
                        'equipment_total' => 0, 
                        'grand_total'     => $logisticsTotal,
                        'status'          => 'pending',
                        'submitted_by'    => Auth::id(),
                    ]);
                }

                /**
                 * ===================== 4. CUSTODIAN EQUIPMENT =====================
                 */
                if ($request->has('custodian_items')) {
                    foreach ($request->custodian_items as $row) {
                        $materialId = $row['material_id'] ?? null;
                        $qty = $row['quantity'] ?? 0;

                        if ($materialId && $qty > 0) {
                            $event->custodianRequests()->create([
                                'custodian_material_id' => $materialId,
                                'quantity' => $qty,
                            ]);
                        }
                    }
                }

                /**
                 * ===================== 5. COMMITTEE =====================
                 */
                if ($request->has('committee')) {
                    foreach ($request->committee as $member) {
                        if (!empty($member['employee_id'])) {
                            $event->participants()->create([
                                'employee_id' => $member['employee_id'],
                                'role' => $member['role'] ?? null,
                                'type' => 'committee',
                            ]);
                        }
                    }
                }

                /**
                 * ===================== 6. BUDGET (Direct Entry) =====================
                 */
                if ($request->has('budget_items')) {
                    foreach ($request->budget_items as $item) {
                        if (!empty($item['description']) || !empty($item['amount'])) {
                            $event->budget()->create([
                                'description' => $item['description'] ?? '',
                                'estimated_amount' => $item['amount'] ?? 0,
                                'status' => 'pending_finance_approval',
                            ]);
                        }
                    }
                }

                /**
                 * ===================== 7. HISTORY =====================
                 */
                $event->histories()->create([
                    'user_id' => Auth::id(),
                    'action' => 'Request Submitted',
                    'note' => 'Event requested. Awaiting Venue, Logistics, and Finance approvals.'
                ]);

                return $event;
            });

            return redirect()
                ->route('events.index')
                ->with('success', 'Event request and departmental tasks created successfully.');

        } catch (\Exception $e) {
            return back()->withInput()->withErrors('Failed to submit event: ' . $e->getMessage());
        }
    }

    /**
     * Unified Approval for Departments (Venue, Logistics, Finance).
     */
    public function approveGate(Request $request, Event $event, string $gate): RedirectResponse
    {
        $column = "is_{$gate}_approved";

        DB::transaction(function () use ($event, $gate, $column) {
            $event->update([$column => true]);

            $event->histories()->create([
                'user_id' => Auth::id(),
                'action' => ucfirst($gate) . ' Approved',
                'note' => "The $gate department has cleared their portion of the request."
            ]);

            // Auto-Approve the whole event if all 3 gates are true
            $event->refresh();
            if ($event->is_venue_approved && $event->is_logistics_approved && $event->is_finance_approved) {
                $event->update(['status' => 'approved']);
                $event->histories()->create([
                    'user_id' => Auth::id(),
                    'action' => 'Full Approval',
                    'note' => 'All departmental gates cleared. Ready to publish.'
                ]);
            }
        });

        return back()->with('success', ucfirst($gate) . ' approval recorded.');
    }

    public function show(Event $event): View
    {
        $this->authorize('view', $event);

        $event->load([
            'requestedBy',
            'venue',
            'resourceAllocations.resource',
            'budget',
            'participants.employee',
            'histories.user',
            'custodianRequests.custodianMaterial',
            'financeRequest'
        ]);

        return view('events.show', compact('event'));
    }

    public function edit(Event $event): View
    {
        $this->authorize('update', $event);

        $venues = Venue::orderBy('name')->get();
        $resources = Resource::orderBy('name')->get();
        $employees = Employee::orderBy('last_name')->get();
        $custodianMaterials = CustodianMaterial::orderBy('name')->get();

        return view('events.edit', compact(
            'event',
            'venues',
            'resources',
            'employees',
            'custodianMaterials'
        ));
    }

    /**
     * UPDATE
     */
    public function update(EventFormRequest $request, Event $event): RedirectResponse
    {
        $this->authorize('update', $event);

        try {
            DB::transaction(function () use ($request, $event) {

                // 1) Update base event details
                $this->eventService->updateEvent(
                    $event,
                    $request->validated(),
                    $request->user()
                );

                // 2) Clear existing related rows
                $event->resourceAllocations()->delete();
                $event->participants()->delete();
                $event->budget()->delete();
                $event->custodianRequests()->delete();
                $event->financeRequest()->delete(); 

                /**
                 * 3) LOGISTICS & NEW CALCULATION
                 */
                $logisticsTotal = 0;
                if ($request->has('logistics_items')) {
                    foreach ($request->logistics_items as $row) {
                        $resourceId = $row['resource_id'] ?? null;
                        $qty = $row['quantity'] ?? 0;

                        if ($resourceId && $qty > 0) {
                            $resource = Resource::find($resourceId);
                            if ($resource) {
                                $logisticsTotal += ($resource->price ?? 0) * $qty;
                                $event->resourceAllocations()->create([
                                    'resource_id' => $resourceId,
                                    'quantity' => $qty,
                                ]);
                            }
                        }
                    }
                }

                if ($request->has('resources')) {
                    foreach ($request->resources as $resourceId => $qty) {
                        if ($qty > 0) {
                            $resource = Resource::find($resourceId);
                            if ($resource) {
                                $logisticsTotal += ($resource->price ?? 0) * $qty;
                                $event->resourceAllocations()->create([
                                    'resource_id' => $resourceId,
                                    'quantity' => $qty,
                                ]);
                            }
                        }
                    }
                }

                /**
                 * NEW: RE-CREATE FINANCE REQUEST
                 */
                if ($logisticsTotal > 0) {
                    $event->financeRequest()->create([
                        'logistics_total' => $logisticsTotal,
                        'equipment_total' => 0,
                        'grand_total'     => $logisticsTotal,
                        'status'          => 'pending',
                        'submitted_by'    => Auth::id(),
                    ]);
                }

                /**
                 * 4) CUSTODIAN EQUIPMENT
                 */
                if ($request->has('custodian_items')) {
                    foreach ($request->custodian_items as $row) {
                        $materialId = $row['material_id'] ?? null;
                        $qty = $row['quantity'] ?? 0;

                        if ($materialId && $qty > 0) {
                            $event->custodianRequests()->create([
                                'custodian_material_id' => $materialId,
                                'quantity' => $qty,
                            ]);
                        }
                    }
                }

                /**
                 * 5) COMMITTEE
                 */
                if ($request->has('committee')) {
                    foreach ($request->committee as $member) {
                        if (!empty($member['employee_id'])) {
                            $event->participants()->create([
                                'employee_id' => $member['employee_id'],
                                'role' => $member['role'] ?? null,
                                'type' => 'committee',
                            ]);
                        }
                    }
                }

                /**
                 * 6) BUDGET (Direct Entry)
                 */
                if ($request->has('budget_items')) {
                    foreach ($request->budget_items as $item) {
                        if (!empty($item['description']) || !empty($item['amount'])) {
                            $event->budget()->create([
                                'description' => $item['description'] ?? '',
                                'estimated_amount' => $item['amount'] ?? 0,
                                'status' => 'pending_finance_approval',
                            ]);
                        }
                    }
                }

                /**
                 * 7) HISTORY
                 */
                $event->histories()->create([
                    'user_id' => Auth::id(),
                    'action' => 'Event Updated',
                    'note' => 'Event request updated with logistics and budget recalculations.'
                ]);
            });

            return redirect()
                ->route('events.index')
                ->with('success', 'Event updated successfully.');

        } catch (\Exception $e) {
            return back()->withInput()->withErrors('Failed to update event: ' . $e->getMessage());
        }
    }

    public function approve(Event $event): RedirectResponse
    {
        $this->authorize('approve', $event);
        $this->eventService->approveEvent($event, Auth::user());

        return redirect()
            ->route('events.index')
            ->with('success', 'Event manually approved.');
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

        DB::transaction(function () use ($event) {
            $event->resourceAllocations()->delete();
            $event->custodianRequests()->delete();
            $event->budget()->delete();
            $event->financeRequest()->delete(); 
            $event->participants()->delete();
            $event->histories()->delete();

            $event->update(['status' => 'deleted']);
            $event->delete();
        });

        return redirect()
            ->route('events.index')
            ->with('success', 'Event and all related departmental requests deleted.');
    }

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

        fgetcsv($handle);

        $count = 0;
        while (($row = fgetcsv($handle)) !== false) {
            if (isset($row[0]) && trim($row[0]) === 'VENUE_REFERENCE') break;
            if (count($row) < 5) continue;

            [$title, $start, $end, $description, $venueId] = $row;

            if (!Venue::where('id', $venueId)->exists()) continue;

            Event::create([
                'title'        => $title,
                'start_at'     => $start,
                'end_at'       => $end,
                'description'  => $description,
                'venue_id'     => $venueId,
                'status'       => 'pending_approval',
                'requested_by' => Auth::id(),
            ]);

            $count++;
        }

        fclose($handle);

        return redirect()
            ->back()
            ->with('success', "{$count} events uploaded and set to pending approval.");
    }
}