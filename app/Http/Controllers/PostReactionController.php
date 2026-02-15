<?php

namespace App\Http\Controllers;

use App\Models\EventPost;
use App\Models\PostReaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostReactionController extends Controller
{
    public function store(Request $request, EventPost $post): JsonResponse|RedirectResponse
    {
        $request->validate([
            'type' => ['required', 'in:like,love,wow,laugh,sad,angry'],
        ]);

        $existingReaction = $post->reactions()
            ->where('user_id', Auth::id())
            ->first();

        if ($existingReaction) {
            $existingReaction->update(['type' => $request->type]);
        } else {
            $post->reactions()->create([
                'user_id' => Auth::id(),
                'type' => $request->type,
            ]);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'reacted' => true,
                'count' => $post->reactions()->count(),
            ]);
        }

        return redirect()->back();
    }

    public function destroy(Request $request, EventPost $post): JsonResponse|RedirectResponse
    {
        $post->reactions()
            ->where('user_id', Auth::id())
            ->delete();

        if ($request->expectsJson()) {
            return response()->json([
                'reacted' => false,
                'count' => $post->reactions()->count(),
            ]);
        }

        return redirect()->back();
    }
}
