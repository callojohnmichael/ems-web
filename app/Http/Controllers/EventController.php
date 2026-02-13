<?php

namespace App\Http\Controllers;

use App\Http\Requests\EventFormRequest;
use App\Models\Event;
use App\Models\Venue;
use App\Models\VenueBooking;
use App\Models\Resource;
use App\Models\Employee;
use App\Models\CustodianMaterial;
use App\Models\EventLogisticsItem;
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
     * Calendar / index with role-based visibility.
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
            $query->whereNotIn('status', ['deleted']);
        } else {
            $query->where(function ($q) use ($user) {
                $q->where('requested_by', $user->id)
                    ->whereNotIn('status', ['deleted'])
                    ->orWhere('status', 'published');
            });
        }

        $events = $query->get();

        $canManageVenues = $user->isAdmin() || $user->hasPermissionTo('manage venues');
        $canManageParticipants = $user->isAdmin() || $user->hasPermissionTo('manage participants');

        return view('events.index', compact('events', 'canManageVenues', 'canManageParticipants'));
    }

    /**
     * Show create form.
     */
    public function create(): View
    {
        $venues = Venue::with('locations')->orderBy('name')->get();
        $resources = Resource::orderBy('name')->get();
        $employees = Employee::orderBy('last_name')->get();
        $custodianMaterials = CustodianMaterial::orderBy('name')->get();

        // Previously requested logistics descriptions
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
     * Store event.
     */
    public function store(EventFormRequest $request): RedirectResponse
    {
        // Prepare location IDs from either checkbox array or JSON hidden input
        $locationIds = $request->venue_location_ids ?? [];

        if (!empty($request->venue_location_ids_json) && empty($locationIds)) {
            try {
                $locationIds = json_decode($request->venue_location_ids_json, true) ?? [];
            } catch (\Exception $e) {
                $locationIds = [];
            }
        }

        if (empty($locationIds)) {
            return back()->withInput()->withErrors([
                'venue_location_ids' => 'Please select at least one venue location.',
            ]);
        }

        // Check venue availability
        $isTaken = Venue::checkVenueAvailability(
            $request->venue_id,
            $request->start_at,
            $request->end_at,
            null
        );

        if ($isTaken) {
            return back()->withInput()->withErrors([
                'venue_id' => 'The venue is already booked for these dates.',
            ]);
        }

        try {
            $event = DB::transaction(function () use ($request, $locationIds) {

                // ================= CREATE EVENT =================
                $event = Event::create([
                    'title' => $request->title,
                    'description' => $request->description,
                    'start_at' => $request->start_at,
                    'end_at' => $request->end_at,
                    'venue_id' => $request->venue_id,
                    'number_of_participants' => $request->number_of_participants ?? 0,
                    'requested_by' => Auth::id(),
                    'status' => 'pending_approvals',
                ]);

                // ================= VENUE BOOKINGS =================
                foreach ($locationIds as $locationId) {
                    VenueBooking::create([
                        'event_id' => $event->id,
                        'venue_id' => $request->venue_id,
                        'venue_location_id' => $locationId,
                        'start_at' => $request->start_at,
                        'end_at' => $request->end_at,
                    ]);
                }

                $logisticsTotal = 0;
                $budgetTotal = 0;

                // ================= LOGISTICS ITEMS =================
                if ($request->has('logistics_items')) {
                    foreach ($request->logistics_items as $item) {

                        $resourceId = $item['resource_id'] ?? null;
                        $name = $item['resource_name'] ?? null;
                        $quantity = (int) ($item['quantity'] ?? 0);
                        $unitPrice = (float) ($item['unit_price'] ?? 0);

                        if ($resourceId === 'custom') {
                            $resourceId = null;
                        }

                        if ((!empty($resourceId) || !empty($name)) && $quantity > 0) {

                            $subtotal = $quantity * $unitPrice;
                            $logisticsTotal += $subtotal;

                            $event->logisticsItems()->create([
                                'resource_id' => $resourceId,
                                'description' => $name,
                                'employee_id' => $item['employee_id'] ?? null,
                                'quantity' => $quantity,
                                'unit_price' => $unitPrice,
                                'subtotal' => $subtotal,
                            ]);
                        }
                    }
                }

                // ================= BUDGET ITEMS =================
                if ($request->has('budget_items')) {
                    foreach ($request->budget_items as $item) {

                        if (!empty($item['description']) || !empty($item['amount'])) {

                            $amount = (float) ($item['amount'] ?? 0);
                            $budgetTotal += $amount;

                            $event->budget()->create([
                                'description' => $item['description'],
                                'estimated_amount' => $amount,
                                'status' => 'pending_finance_approval',
                            ]);
                        }
                    }
                }

                // ================= CUSTODIAN =================
                if ($request->has('custodian_items')) {
                    foreach ($request->custodian_items as $row) {

                        $materialId = $row['material_id'] ?? null;
                        $qty = (int) ($row['quantity'] ?? 0);

                        if (!empty($materialId) && $qty > 0) {
                            $event->custodianRequests()->create([
                                'custodian_material_id' => $materialId,
                                'quantity' => $qty,
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
                                'role' => $member['role'] ?? null,
                                'type' => 'committee',
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
                        'grand_total' => $grandTotal,
                        'status' => 'pending',
                        'submitted_by' => Auth::id(),
                    ]);
                }

                // ================= HISTORY =================
                $event->histories()->create([
                    'user_id' => Auth::id(),
                    'action' => 'Request Submitted',
                    'note' => 'Event requested. Awaiting Finance and Custodian approvals.',
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

    /**
     * Show event.
     */
    public function show(Event $event): View
    {
        $this->authorize('view', $event);

        $event->load([
            'requestedBy',
            'venue',
            'logisticsItems',
            'budget',
            'participants.employee',
            'participants.user',
            'histories.user',
            'custodianRequests.custodianMaterial',
            'financeRequest',
            'venueBookings.venueLocation',
        ]);

        $venues = Venue::with('locations')->orderBy('name')->get();
        $resources = Resource::orderBy('name')->get();
        
        // Corrected to use the standard CustodianMaterial model class
        $custodianMaterials = CustodianMaterial::orderBy('name')->get(); 

        $groupedParticipants = $event->participants->groupBy('type');
        $committees = $groupedParticipants->get('committee', collect());
        $standardParticipants = $groupedParticipants->get('participant', collect());

        $participantCount = $event->participants()->count();
        $attendedCount = $event->participants()->where('status', 'attended')->count();
        $absentCount = $event->participants()->where('status', 'absent')->count();

        return view('events.show', compact(
            'event',
            'committees',
            'standardParticipants',
            'participantCount',
            'attendedCount',
            'absentCount',
            'venues',
            'resources',
            'custodianMaterials'
        ));
    }

    /**
     * Edit.
     */
    public function edit(Event $event): View
{
    $this->authorize('update', $event);

    // Load related venues with locations
    $venues = Venue::with('locations')->orderBy('name')->get();

    // Load resources for logistics
    $resources = Resource::orderBy('name')->get();

    // Load employees for committee
    $employees = Employee::orderBy('last_name')->get();

    // Load custodian materials
    $custodianMaterials = CustodianMaterial::orderBy('name')->get();

    // Load existing venue bookings with locations for the event
    $event->load('venueBookings.venueLocation');

    // Get existing selected venue location IDs
    $existingVenueLocationIds = $event->venueBookings->pluck('venue_location_id')->toArray();

    // Prepare existing logistics items as array for Alpine
    $existingLogistics = $event->logisticsItems->map(function($l) {
        return [
            'resource_id' => $l->resource_id,
            'resource_name' => $l->description,
            'quantity' => $l->quantity,
            'unit_price' => $l->unit_price,
        ];
    })->toArray();

    // Prepare existing custodian requests for Alpine
    $existingCustodian = $event->custodianRequests->map(function($c) {
        return [
            'material_id' => $c->custodian_material_id,
            'quantity' => $c->quantity,
        ];
    })->toArray();

    // Prepare existing participants for Alpine
    $existingParticipants = $event->participants->map(function($p) {
        return [
            'employee_id' => $p->employee_id,
            'role' => $p->role,
        ];
    })->toArray();

    // Pass all data to the view
    return view('events.edit', compact(
        'event',
        'venues',
        'resources',
        'employees',
        'custodianMaterials',
        'existingLogistics',
        'existingCustodian',
        'existingParticipants',
        'existingVenueLocationIds'
    ));
}

    /**
     * Update.
     */
    public function update(EventFormRequest $request, Event $event): RedirectResponse
{
    $this->authorize('update', $event);

    /* ================= GET LOCATION IDS (NEW + BACKWARD COMPATIBLE) ================= */
    $locationIds = $request->input('venue_location_ids', []);

    // If still empty, fallback to json field (old setup)
    if (!empty($request->venue_location_ids_json) && empty($locationIds)) {
        try {
            $locationIds = json_decode($request->venue_location_ids_json, true) ?? [];
        } catch (\Exception $e) {
            $locationIds = [];
        }
    }

    // Normalize values (checkbox gives string)
    $locationIds = collect($locationIds)
        ->filter(fn($id) => !empty($id))
        ->map(fn($id) => (int) $id)
        ->unique()
        ->values()
        ->toArray();

    if (empty($locationIds)) {
        return back()->withInput()->withErrors([
            'venue_location_ids' => 'Please select at least one venue location.',
        ]);
    }

    /* ================= VENUE AVAILABILITY CHECK (EXCLUDE CURRENT EVENT) ================= */
    $isTaken = Venue::checkVenueAvailability(
        $request->venue_id,
        $request->start_at,
        $request->end_at,
        $event->id
    );

    if ($isTaken) {
        return back()->withInput()->withErrors([
            'venue_id' => 'The venue is already booked for these dates.',
        ]);
    }

    try {
        DB::transaction(function () use ($request, $event, $locationIds) {

            /* ================= UPDATE EVENT ================= */
            $event->update([
                'title' => $request->title,
                'description' => $request->description,
                'start_at' => $request->start_at,
                'end_at' => $request->end_at,
                'venue_id' => $request->venue_id,
                'number_of_participants' => $request->number_of_participants ?? 0,
            ]);

            /* ================= RESET BOOKINGS ================= */
            $event->venueBookings()->delete();

            foreach ($locationIds as $locationId) {
                VenueBooking::create([
                    'event_id' => $event->id,
                    'venue_id' => $request->venue_id,
                    'venue_location_id' => $locationId,
                    'start_at' => $request->start_at,
                    'end_at' => $request->end_at,
                ]);
            }

            /* ================= CLEAR OLD RELATED DATA ================= */
            $event->logisticsItems()->delete();
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
                    $quantity = (int) ($item['quantity'] ?? 0);
                    $unitPrice = (float) ($item['unit_price'] ?? 0);

                    if ($resourceId === 'custom') {
                        $resourceId = null;
                    }

                    if ((!empty($resourceId) || !empty($name)) && $quantity > 0) {

                        $subtotal = $quantity * $unitPrice;
                        $logisticsTotal += $subtotal;

                        $event->logisticsItems()->create([
                            'resource_id' => $resourceId,
                            'description' => $name,
                            'employee_id' => $item['employee_id'] ?? null,
                            'quantity' => $quantity,
                            'unit_price' => $unitPrice,
                            'subtotal' => $subtotal,
                        ]);
                    }
                }
            }

            /* ================= BUDGET ITEMS ================= */
            if ($request->has('budget_items')) {
                foreach ($request->budget_items as $item) {

                    if (!empty($item['description']) || !empty($item['amount'])) {

                        $amount = (float) ($item['amount'] ?? 0);
                        $budgetTotal += $amount;

                        $event->budget()->create([
                            'description' => $item['description'],
                            'estimated_amount' => $amount,
                            'status' => 'pending_finance_approval',
                        ]);
                    }
                }
            }

            /* ================= CUSTODIAN ================= */
            if ($request->has('custodian_items')) {
                foreach ($request->custodian_items as $row) {

                    $materialId = $row['material_id'] ?? null;
                    $qty = (int) ($row['quantity'] ?? 0);

                    if (!empty($materialId) && $qty > 0) {
                        $event->custodianRequests()->create([
                            'custodian_material_id' => $materialId,
                            'quantity' => $qty,
                        ]);
                    }
                }
            }

            /* ================= HISTORY ================= */
            $event->histories()->create([
                'user_id' => Auth::id(),
                'action' => 'Event Updated',
                'note' => 'Event request updated with recalculated logistics and budget.',
            ]);

            /* ================= FINANCE REQUEST ================= */
            $grandTotal = $logisticsTotal + $budgetTotal;

            if ($grandTotal > 0) {
                $event->financeRequest()->create([
                    'logistics_total' => $logisticsTotal,
                    'equipment_total' => 0,
                    'grand_total' => $grandTotal,
                    'status' => 'pending',
                    'submitted_by' => Auth::id(),
                ]);
            }
        });

        return redirect()
            ->route('events.index')
            ->with('success', 'Event updated successfully.');

    } catch (\Exception $e) {
        return back()->withInput()
            ->withErrors('Failed to update event: ' . $e->getMessage());
    }
}

    /**
     * Approve.
     */
    public function approve(Event $event): RedirectResponse
    {
        $this->authorize('approve', $event);

        $event->load(['financeRequest', 'custodianRequests']);

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

    /**
     * Reject.
     */
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

    /**
     * Publish.
     */
    public function publish(Event $event): RedirectResponse
    {
        $this->authorize('publish', $event);

        if (!$event->isFinanceRequestApproved() || !$event->isCustodianApproved()) {
            return back()->withErrors(
                'Event cannot be published. Finance or Custodian request is still pending.'
            );
        }

        $this->eventService->publishEvent($event, Auth::user());

        return redirect()
            ->route('events.index')
            ->with('success', 'Event published.');
    }

    /**
     * Soft delete.
     */
    public function destroy(Event $event): RedirectResponse
    {
        $this->authorize('delete', $event);

        DB::transaction(function () use ($event) {

            $event->logisticsItems()->delete();
            $event->custodianRequests()->delete();
            $event->budget()->delete();
            $event->financeRequest()->delete();
            $event->histories()->delete();

            // also clear bookings
            $event->venueBookings()->delete();

            $event->update(['status' => 'deleted']);
            $event->delete();
        });

        return redirect()
            ->route('events.index')
            ->with('success', 'Event and all related departmental requests deleted.');
    }

    /**
     * Bulk upload (admin only).
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

        fgetcsv($handle);

        $count = 0;

        while (($row = fgetcsv($handle)) !== false) {
            if (isset($row[0]) && trim($row[0]) === 'VENUE_REFERENCE') break;
            if (count($row) < 5) continue;

            [$title, $start, $end, $description, $venueId] = $row;

            if (!Venue::where('id', $venueId)->exists()) continue;

            // If CSV has bad dates, skip
            try {
                if (strtotime($end) <= strtotime($start)) continue;
            } catch (\Exception $e) {
                continue;
            }

            Event::create([
                'title' => $title,
                'start_at' => $start,
                'end_at' => $end,
                'description' => $description,
                'venue_id' => $venueId,
                'status' => 'pending_approval',
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