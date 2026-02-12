<?php

namespace App\Http\Controllers;

use App\Http\Requests\EventFormRequest;
use App\Models\Event;
use App\Models\Venue;
use App\Models\Resource;
use App\Models\Employee;
use App\Models\Budget;
use App\Models\EventHistory;
use App\Models\CustodianMaterial;
use App\Models\EventLogisticsItem;
use App\Models\EventCustodianRequest;
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

    $query = Event::with([
        'requestedBy',
        'venue',
        'participants',
        'logisticsItems',
        'budget',
    ])->latest();

    if ($user->isAdmin()) {

        // Admin sees everything except hard deleted
        $query->whereNotIn('status', ['deleted']);

    } else {

        // Regular users:
        // 1. See their own requests (any status except deleted)
        // 2. See published events from others

        $query->where(function ($q) use ($user) {
            $q->where('requested_by', $user->id)
              ->whereNotIn('status', ['deleted'])
              ->orWhere('status', 'published');
        });
    }

    $events = $query->get();
    
    // Pass permission flag to view
    $canManageVenues = $user->isAdmin() || $user->hasPermissionTo('manage venues');
    $canManageParticipants = $user->isAdmin() || $user->hasPermissionTo('manage participants');

    return view('events.index', compact('events', 'canManageVenues', 'canManageParticipants'));
        }

        /**
         * Show the form for creating a new event.
         */
        public function create(): View
        {
            $venues = Venue::orderBy('name')->get();
            $resources = Resource::orderBy('name')->get();
            $employees = Employee::orderBy('last_name')->get();
            $custodianMaterials = CustodianMaterial::orderBy('name')->get();

            // Include previously requested logistics descriptions so users can re-select
            $previousLogistics = EventLogisticsItem::query()
                ->whereNotNull('description')
                ->distinct()
                ->orderBy('description')
                ->pluck('description');

            return view('events.create', compact(
                'venues',
                'resources',
                'employees',
                'custodianMaterials',
                'previousLogistics'
            ));
        }

        /**
         * Store a newly created event in storage.
         */
        public function store(EventFormRequest $request): RedirectResponse
        {
            $isTaken = Venue::checkVenueAvailability(
                $request->venue_id,
                $request->start_at,
                $request->end_at,
                null
            );
    
            if ($isTaken) {
            return back()->withInput()->withErrors([
                'venue_id' => 'The venue is already booked for these dates.'
            ]);
        }

        try {
            $event = DB::transaction(function () use ($request) {

                // ================= CREATE EVENT =================
                $event = Event::create([
                    'title' => $request->title,
                    'description' => $request->description,
                    'start_at' => $request->start_at,
                    'end_at' => $request->end_at,
                    'venue_id' => $request->venue_id,
                    'number_of_participants' => $request->number_of_participants,
                    'requested_by' => Auth::id(),
                    'status' => 'pending_approvals',
                ]);

                $logisticsTotal = 0;
                $budgetTotal = 0;

                // ================= LOGISTICS ITEMS =================
                if ($request->has('logistics_items')) {
                    foreach ($request->logistics_items as $item) {

                        $resourceId = $item['resource_id'] ?? null;
                        $name = $item['resource_name'] ?? null;
                        $quantity = $item['quantity'] ?? 0;
                        $unitPrice = $item['unit_price'] ?? 0;

                        // Convert 'custom' string to null
                        if ($resourceId === 'custom') {
                            $resourceId = null;
                        }

                        if ((!empty($resourceId) || !empty($name)) && $quantity > 0) {

                            $subtotal = $quantity * $unitPrice;
                            $logisticsTotal += $subtotal;

                            $event->logisticsItems()->create([
                                'resource_id'  => $resourceId,
                                'description'  => $name,
                                'quantity'     => $quantity,
                                'unit_price'   => $unitPrice,
                                'subtotal'     => $subtotal,
                            ]);
                        }
                    }
                }

                // ================= BUDGET ITEMS =================
                if ($request->has('budget_items')) {
                    foreach ($request->budget_items as $item) {

                        if (!empty($item['description']) || !empty($item['amount'])) {

                            $budgetTotal += $item['amount'] ?? 0;

                            $event->budget()->create([
                                'description'      => $item['description'],
                                'estimated_amount' => $item['amount'] ?? 0,
                                'status'           => 'pending_finance_approval',
                            ]);
                        }
                    }
                }

                // ================= CUSTODIAN =================
                if ($request->has('custodian_items')) {
                    foreach ($request->custodian_items as $row) {

                        if (!empty($row['material_id']) && $row['quantity'] > 0) {
                            $event->custodianRequests()->create([
                                'custodian_material_id' => $row['material_id'],
                                'quantity' => $row['quantity'],
                            ]);
                        }
                    }
                }

                // ================= COMMITTEE =================
                if ($request->has('committee')) {
                    foreach ($request->committee as $member) {
                        if (!empty($member['employee_id'])) {
                            $event->participants()->create([
                                'employee_id' => $member['employee_id'],
                                'role'        => $member['role'] ?? null,
                                'type'        => 'committee',
                            ]);
                        }
                    }
                }

                // ================= FINANCE REQUEST =================
                $grandTotal = $logisticsTotal + $budgetTotal;

                if ($grandTotal > 0) {
                    $event->financeRequest()->create([
                        'logistics_total' => $logisticsTotal,
                        'equipment_total' => 0,
                        'grand_total'     => $grandTotal,
                        'status'          => 'pending',
                        'submitted_by'    => Auth::id(),
                    ]);
                }

                // ================= HISTORY =================
                $event->histories()->create([
                    'user_id' => Auth::id(),
                    'action'  => 'Request Submitted',
                    'note'    => 'Event requested. Awaiting Finance and Custodian approvals.'
                ]);

                return $event;
            });

            return redirect()
                ->route('events.index')
                ->with('success', 'Event request submitted successfully.');

        } catch (\Exception $e) {
            return back()->withInput()
                ->withErrors('Failed to submit event: ' . $e->getMessage());
        }
    }




public function show(Event $event): View
{
    $this->authorize('view', $event);

    $event->load([
        'requestedBy',
        'venue',
        'logisticsItems',
        'budget',
        'participants.employee',
        'participants.user', // Ensure user is loaded for display_name logic
        'histories.user',
        'custodianRequests.custodianMaterial',
        'financeRequest'
    ]);

    // Group participants by their type attribute
    $groupedParticipants = $event->participants->groupBy('type');

    $committees = $groupedParticipants->get('committee', collect());
    $standardParticipants = $groupedParticipants->get('participant', collect());

    // Stats
    $participantCount = $event->participants()->count();
    $attendedCount = $event->participants()->where('status', 'attended')->count();
    $absentCount = $event->participants()->where('status', 'absent')->count();

    return view('events.show', compact(
        'event', 
        'committees', 
        'standardParticipants', 
        'participantCount', 
        'attendedCount', 
        'absentCount'
    ));
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

            /* ================= UPDATE EVENT ================= */
            $event->update([
                'title' => $request->title,
                'description' => $request->description,
                'start_at' => $request->start_at,
                'end_at' => $request->end_at,
                'venue_id' => $request->venue_id,
                'number_of_participants' => $request->number_of_participants,
            ]);

            /* ================= CLEAR OLD RELATED DATA ================= */
            $event->logisticsItems()->delete(); // 🔥 changed
            $event->budget()->delete();
            $event->custodianRequests()->delete();
            $event->financeRequest()->delete();

            $logisticsTotal = 0;
            $budgetTotal = 0;

            /* ================= LOGISTICS ITEMS ================= */
            if ($request->has('logistics_items')) {
                foreach ($request->logistics_items as $item) {

                    $resourceId = $item['resource_id'] ?? null;
                    $name = $item['resource_name'] ?? null;
                    $quantity = $item['quantity'] ?? 0;
                    $unitPrice = $item['unit_price'] ?? 0;

                    // Convert 'custom' string to null
                    if ($resourceId === 'custom') {
                        $resourceId = null;
                    }

                    if ((!empty($resourceId) || !empty($name)) && $quantity > 0) {

                        $subtotal = $quantity * $unitPrice;
                        $logisticsTotal += $subtotal;

                        $event->logisticsItems()->create([
                            'resource_id'  => $resourceId,
                            'description'  => $name,
                            'quantity'     => $quantity,
                            'unit_price'   => $unitPrice,
                            'subtotal'     => $subtotal,
                        ]);
                    }
                }
            }

            /* ================= BUDGET ITEMS ================= */
            if ($request->has('budget_items')) {
                foreach ($request->budget_items as $item) {

                    if (!empty($item['description']) || !empty($item['amount'])) {

                        $budgetTotal += $item['amount'] ?? 0;

                        $event->budget()->create([
                            'description'      => $item['description'],
                            'estimated_amount' => $item['amount'] ?? 0,
                            'status'           => 'pending_finance_approval',
                        ]);
                    }
                }
            }

            /* ================= CUSTODIAN ================= */
            if ($request->has('custodian_items')) {
                foreach ($request->custodian_items as $row) {

                    if (!empty($row['material_id']) && $row['quantity'] > 0) {
                        $event->custodianRequests()->create([
                            'custodian_material_id' => $row['material_id'],
                            'quantity' => $row['quantity'],
                        ]);
                    }
                }
            }

            /* ================= FINANCE REQUEST ================= */
            $grandTotal = $logisticsTotal + $budgetTotal;

            if ($grandTotal > 0) {
                $event->financeRequest()->create([
                    'logistics_total' => $logisticsTotal,
                    'equipment_total' => 0,
                    'grand_total'     => $grandTotal,
                    'status'          => 'pending',
                    'submitted_by'    => Auth::id(),
                ]);
            }

            /* ================= HISTORY ================= */
            $event->histories()->create([
                'user_id' => Auth::id(),
                'action'  => 'Event Updated',
                'note'    => 'Event request updated with recalculated logistics and budget.'
            ]);
        });

        return redirect()
            ->route('events.index')
            ->with('success', 'Event updated successfully.');

    } catch (\Exception $e) {
        return back()->withInput()
            ->withErrors('Failed to update event: ' . $e->getMessage());
    }
}


        public function approve(Event $event): RedirectResponse
{
    $this->authorize('approve', $event);

    $event->load([
        'financeRequest',
        'custodianRequests'
    ]);

    if (!$event->canBeFullyApproved()) {

        $missing = [];

        if (!$event->isFinanceRequestApproved()) {
            $missing[] = "Finance Request Approval";
        }

        if (!$event->isCustodianApproved()) {
            $missing[] = "Custodian Request Approval";
        }

        return back()->withErrors(
            "Cannot approve event yet. Missing: " . implode(", ", $missing)
        );
    }

    $this->eventService->approveEvent($event, Auth::user());

    return redirect()
        ->route('events.index')
        ->with('success', 'Event approved successfully.');
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

    if (
        !$event->isFinanceRequestApproved() ||
        !$event->isCustodianApproved()
    ) {
        return back()->withErrors(
            'Event cannot be published. Finance or Custodian request is still pending.'
        );
    }

    $this->eventService->publishEvent($event, Auth::user());

    return redirect()
        ->route('events.index')
        ->with('success', 'Event published.');
}

public function destroy(Event $event): RedirectResponse
{
    $this->authorize('delete', $event);

    DB::transaction(function () use ($event) {
        $event->logisticsItems()->delete(); // 🔥 changed
        $event->custodianRequests()->delete();
        $event->budget()->delete();
        $event->financeRequest()->delete();
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