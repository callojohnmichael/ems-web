<?php

namespace App\Http\Controllers;

use App\Models\EventPost;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MultimediaController extends Controller
{
    public function index(Request $request): View
    {
        $posts = EventPost::with([
            'event',
            'user',
            'media',
            'reactions',
            'comments.user',
        ])
        ->latest()
        ->paginate(10);

        return view('multimedia.index', compact('posts'));
    }
}
