<?php

namespace App\Http\Controllers;

use App\Models\EventPost;
use App\Models\PostComment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostCommentController extends Controller
{
    public function store(Request $request, EventPost $post): RedirectResponse
    {
        $request->validate([
            'content' => ['required', 'string', 'max:1000'],
        ]);

        $post->comments()->create([
            'user_id' => Auth::id(),
            'content' => $request->content,
        ]);

        return redirect()->back();
    }

    public function update(Request $request, EventPost $post, PostComment $comment): RedirectResponse
    {
        // Only allow users to edit their own comments
        if ($comment->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'content' => ['required', 'string', 'max:1000'],
        ]);

        $comment->update(['content' => $request->content]);

        return redirect()->back();
    }

    public function destroy(EventPost $post, PostComment $comment): RedirectResponse
    {
        // Only allow users to delete their own comments
        if ($comment->user_id !== Auth::id()) {
            abort(403);
        }

        $comment->delete();

        return redirect()->back();
    }
}
