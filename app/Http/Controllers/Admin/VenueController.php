<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Venue;
use App\Models\VenueBooking;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;
use Exception;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class VenueController extends Controller
{
    use AuthorizesRequests;

    
public function index(Request $request): View
{
    // Start query with eager loading counts
    $query = Venue::withCount([
        'events' => function ($query) {
            $query->whereIn('status', ['pending_approvals', 'approved', 'published']);
        },
        'locations'
    ])->orderBy('name');

    // Apply search filter if present
    if ($request->has('search') && $request->search) {
        $search = $request->search;
        $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('address', 'like', "%{$search}%")
              ->orWhere('facilities', 'like', "%{$search}%");
        });
    }

    // Get venues
    $venues = $query->get();

    // Determine if current user can manage venues
    $canManageVenues = auth()->user()->isAdmin() || auth()->user()->hasPermissionTo('manage venues');

    return view('admin.venues.index', compact('venues', 'canManageVenues'));
}

    public function create(): View
    {
        return view('admin.venues.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:venues',
            'address' => 'required|string|max:500',
            'locations' => 'required|array|min:1',
            'locations.*.name' => 'required|string|max:255',
            'locations.*.capacity' => 'required|integer|min:1',
            'locations.*.amenities' => 'nullable|string|max:500',
        ]);

        try {
            return DB::transaction(function () use ($validated) {
                $totalCapacity = collect($validated['locations'])->sum('capacity');

                $venue = Venue::create([
                    'name' => $validated['name'],
                    'address' => $validated['address'],
                    'capacity' => $totalCapacity,
                ]);

                foreach ($validated['locations'] as $loc) {
                    $venue->locations()->create([
                        'name' => $loc['name'],
                        'capacity' => $loc['capacity'],
                        'amenities' => $loc['amenities'] ?? '',
                        'facilities' => '', // Match your DB column
                    ]);
                }

                return redirect()->route('admin.venues.index')
                    ->with('success', 'Venue and locations created successfully!');
            });
        } catch (Exception $e) {
            return back()->withInput()->withErrors(['error' => 'Store Failed: ' . $e->getMessage()]);
        }
    }

    public function update(Request $request, Venue $venue): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:venues,name,' . $venue->id,
            'address' => 'required|string|max:500',
            'locations' => 'required|array|min:1',
            'locations.*.id' => 'nullable|exists:venue_locations,id',
            'locations.*.name' => 'required|string|max:255',
            'locations.*.capacity' => 'required|integer|min:1',
            'locations.*.amenities' => 'nullable|string|max:500',
        ]);

        try {
            return DB::transaction(function () use ($validated, $venue) {
                $totalCapacity = collect($validated['locations'])->sum('capacity');

                $venue->update([
                    'name' => $validated['name'],
                    'address' => $validated['address'],
                    'capacity' => $totalCapacity,
                ]);

                // Sync locations logic (Preserving IDs for Bookings)
                $keepIds = collect($validated['locations'])->pluck('id')->filter()->toArray();
                $venue->locations()->whereNotIn('id', $keepIds)->delete();

                foreach ($validated['locations'] as $loc) {
                    $venue->locations()->updateOrCreate(
                        ['id' => $loc['id'] ?? null],
                        [
                            'name' => $loc['name'],
                            'capacity' => $loc['capacity'],
                            'amenities' => $loc['amenities'] ?? '',
                            'facilities' => '',
                        ]
                    );
                }

                return redirect()->route('admin.venues.index')->with('success', 'Venue updated!');
            });
        } catch (Exception $e) {
            return back()->withInput()->withErrors(['error' => 'Update Failed: ' . $e->getMessage()]);
        }
    }

    // Availability JSON Method (Copied from your other controller)
    public function availability(Request $request, Venue $venue): JsonResponse
    {
        $data = $request->validate([
            'start_at' => ['required', 'date'],
            'end_at' => ['required', 'date', 'after_or_equal:start_at'],
        ]);

        $start = Carbon::parse($data['start_at']);
        $end = Carbon::parse($data['end_at']);

        $conflicts = VenueBooking::where('venue_id', $venue->id)
            ->whereHas('event', function ($q) {
                $q->whereIn('status', ['pending_approvals', 'approved', 'published']);
            })
            ->where(function ($q) use ($start, $end) {
                $q->whereBetween('start_at', [$start, $end])
                  ->orWhereBetween('end_at', [$start, $end])
                  ->orWhere(function ($q2) use ($start, $end) {
                      $q2->where('start_at', '<', $start)->where('end_at', '>', $end);
                  });
            })
            ->with('event')
            ->get();

        $formattedConflicts = $conflicts->map(fn($b) => [
            'id' => $b->id,
            'venue_location_id' => $b->venue_location_id,
            'title' => $b->event->title ?? 'Event',
            'start_at' => $b->start_at->toDateTimeString(),
            'end_at' => $b->end_at->toDateTimeString(),
            'status' => $b->event->status ?? 'unknown',
        ]);

        return response()->json([
            'available' => $conflicts->isEmpty(),
            'conflicts' => $formattedConflicts,
        ]);
    }

    public function show(Venue $venue): View
    {
        $venue->load(['locations', 'events']);
        return view('admin.venues.show', compact('venue'));
    }

    public function edit(Venue $venue): View
    {
        $venue->load('locations');
        return view('admin.venues.edit', compact('venue'));
    }

    public function destroy(Venue $venue): RedirectResponse
    {
        $venue->locations()->delete();
        $venue->delete();
        return redirect()->route('admin.venues.index')->with('success', 'Deleted!');
    }
}