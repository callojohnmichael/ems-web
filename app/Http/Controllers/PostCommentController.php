<?php

namespace App\Http\Controllers;

use App\Models\EventPost;
use App\Models\PostComment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostCommentController extends Controller
{
    public function store(Request $request, EventPost $post): JsonResponse|RedirectResponse
    {
        $request->validate([
            'content' => ['required', 'string', 'max:1000'],
        ]);

        $comment = $post->comments()->create([
            'user_id' => Auth::id(),
            'body' => $request->content,
        ]);

        $comment->load('user');

        if ($request->expectsJson()) {
            return response()->json([
                'comment' => [
                    'id' => $comment->id,
                    'body' => $comment->body,
                    'user_name' => 'You',
                    'created_at_human' => $comment->created_at->diffForHumans(),
                    'is_own' => true,
                ],
                'total_count' => $post->comments()->count(),
            ]);
        }

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

        $comment->update(['body' => $request->content]);

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
