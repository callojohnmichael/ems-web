<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Venue;
use App\Models\Campus;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class VenueController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        $canManageVenues = $user->isAdmin() || $user->hasPermissionTo('manage venues');
        
        $venues = Venue::withCount('events')->orderBy('name')->get();
        
        return view('admin.venues.index', compact('venues', 'canManageVenues'));
    }

    public function create(): View
    {
        $campuses = Campus::with('facilities.amenities')->orderBy('name')->get();
        return view('admin.venues.create', compact('campuses'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'capacity' => 'required|integer|min:1',
            'facilities' => 'nullable|string',
            'campuses' => 'array',
            'campuses.*' => 'exists:campuses,id',
        ]);

        $venue = Venue::create($validated);
        
        if ($request->has('campuses') && !empty($request->campuses)) {
            $venue->campuses()->sync($request->campuses);
        }

        return redirect()->route('admin.venues.index')
            ->with('success', 'Venue created successfully!');
    }

    public function edit(Venue $venue): View
    {
        $campuses = Campus::with('facilities.amenities')->orderBy('name')->get();
        $selectedCampusIds = $venue->campuses()->pluck('campus_id')->toArray();
        
        return view('admin.venues.edit', compact('venue', 'campuses', 'selectedCampusIds'));
    }

    public function update(Request $request, Venue $venue): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'capacity' => 'required|integer|min:1',
            'facilities' => 'nullable|string',
            'campuses' => 'array',
            'campuses.*' => 'exists:campuses,id',
        ]);

        $venue->update($validated);
        
        if ($request->has('campuses')) {
            $venue->campuses()->sync($request->campuses);
        }

        return redirect()->route('admin.venues.index')
            ->with('success', 'Venue updated successfully!');
    }

    public function show(Venue $venue): View
    {
        $venue->load('campuses.facilities.amenities', 'events');
        return view('admin.venues.show', compact('venue'));
    }

    public function destroy(Venue $venue): RedirectResponse
    {
        $venue->delete();
        return redirect()->route('admin.venues.index')
            ->with('success', 'Venue deleted successfully!');
    }
}
