<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class MultimediaController extends Controller
{
    public function index(): View
    {
        return view('pages.multimedia');
    }
}
