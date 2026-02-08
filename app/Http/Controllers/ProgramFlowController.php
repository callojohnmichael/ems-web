<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class ProgramFlowController extends Controller
{
    public function index(): View
    {
        return view('pages.program-flow');
    }
}
