<?php

namespace App\Http\Controllers;

use App\Models\EventPost;
use App\Models\PostReaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostReactionController extends Controller
{
    public function store(Request $request, EventPost $post): RedirectResponse
    {
        $request->validate([
            'type' => ['required', 'in:like,love,wow,laugh,sad,angry'],
        ]);

        // Check if user already reacted
        $existingReaction = $post->reactions()
            ->where('user_id', Auth::id())
            ->first();

        if ($existingReaction) {
            // Update existing reaction
            $existingReaction->update(['type' => $request->type]);
        } else {
            // Create new reaction
            $post->reactions()->create([
                'user_id' => Auth::id(),
                'type' => $request->type,
            ]);
        }

        return redirect()->back();
    }

    public function destroy(EventPost $post): RedirectResponse
    {
        $post->reactions()
            ->where('user_id', Auth::id())
            ->delete();

        return redirect()->back();
    }
}
