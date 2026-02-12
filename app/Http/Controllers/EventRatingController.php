<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventRating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventRatingController extends Controller
{
    public function store(Request $request, Event $event)
    {
        $request->validate([
            'rating' => ['required', 'integer', 'between:1,5'],
            'comment' => ['nullable', 'string'],
        ]);

        $user = Auth::user();

        // ensure user is a participant of the event
        $isParticipant = $event->participants()->where('user_id', $user->id)->exists();
        if (! $isParticipant && $user->employee) {
            $isParticipant = $event->participants()->where('employee_id', $user->employee->id)->exists();
        }

        if (! $isParticipant) {
            return back()->withErrors('Only event participants can rate events.');
        }

        EventRating::updateOrCreate(
            ['event_id' => $event->id, 'user_id' => $user->id],
            ['rating' => $request->rating, 'comment' => $request->comment]
        );

        return back()->with('success', 'Rating saved.');
    }
}
