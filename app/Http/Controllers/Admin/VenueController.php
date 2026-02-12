<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Venue;
use Illuminate\View\View;

class VenueController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        $canManageVenues = $user->isAdmin() || $user->hasPermissionTo('manage venues');
        
        $venues = Venue::withCount('events')->orderBy('name')->get();
        
        return view('admin.venues.index', compact('venues', 'canManageVenues'));
    }
