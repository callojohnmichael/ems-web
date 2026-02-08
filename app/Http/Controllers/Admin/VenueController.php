<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class VenueController extends Controller
{
    public function index(): View
    {
        return view('admin.venues.index');
    }
}
