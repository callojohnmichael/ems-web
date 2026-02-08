<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class ParticipantController extends Controller
{
    public function index(): View
    {
        return view('admin.participants.index');
    }
}
