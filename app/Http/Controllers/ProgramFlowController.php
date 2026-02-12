<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\ProgramItem;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;

class ProgramFlowController extends Controller
{
    public function __construct()
    {
        // Viewing program flow requires view permission
        $this->middleware('permission:view program flow')->only(['index', 'show']);

    // Managing items requires manage scheduling permission
    $this->middleware('permission:manage scheduling')->only(['storeItem', 'updateItem', 'destroyItem', 'reorderItems']);
    }

    /**
     * Show a list of published events that have program flows.
     */
    public function index(): View
    {
        $events = Event::where('status', Event::STATUS_PUBLISHED)
            ->orderBy('start_at', 'asc')
            ->get();

        return view('program-flow.index', compact('events'));
    }

    /**
     * Show program flow for a published event.
     */
    public function show(Event $event): View
    {
        if ($event->status !== Event::STATUS_PUBLISHED) {
            abort(404);
        }

        $event->load('programItems.assignedTo');

        return view('program-flow.show', compact('event'));
    }

    /**
     * Store a new program item for an event.
     */
    public function storeItem(Request $request, Event $event): RedirectResponse
    {
        if ($event->status !== Event::STATUS_PUBLISHED) {
            abort(404);
        }

        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'start_at' => ['nullable', 'date'],
            'end_at' => ['nullable', 'date'],
            'type' => ['nullable', 'string', 'max:100'],
            'assigned_to' => ['nullable', 'exists:users,id'],
        ]);

        // Ensure any provided start/end are inside the event's timeframe
        $eventStart = $event->start_at ? Carbon::parse($event->start_at) : null;
        $eventEnd = $event->end_at ? Carbon::parse($event->end_at) : null;

        $start = $request->start_at ? Carbon::parse($request->start_at) : null;
        $end = $request->end_at ? Carbon::parse($request->end_at) : null;

        $errors = [];
        if ($start && $eventStart && $start->lt($eventStart)) {
            $errors['start_at'] = 'Start time must be on or after the event start time (' . $eventStart->format('Y-m-d H:i') . ').';
        }
        if ($end && $eventEnd && $end->gt($eventEnd)) {
            $errors['end_at'] = 'End time must be on or before the event end time (' . $eventEnd->format('Y-m-d H:i') . ').';
        }
        if (($start && !$end) || (!$start && $end)) {
            $errors['start_at'] = 'Both start and end time must be provided together.';
        }
        if ($start && $end && $start->gt($end)) {
            $errors['start_at'] = 'Start time must be before end time.';
        }

        if (!empty($errors)) {
            return back()->withErrors($errors)->withInput();
        }

        $item = $event->programItems()->create([
            'title' => $request->title,
            'start_at' => $request->start_at,
            'end_at' => $request->end_at,
            'type' => $request->type,
            'assigned_to' => $request->assigned_to,
            // temporary order; will be recalculated below
            'order' => 0,
        ]);

        // Recalculate order based on start/end times
        $this->reorderByStart($event);

        return back()->with('success', 'Program item added.');
    }

    /**
     * Update an existing program item.
     */
    public function updateItem(Request $request, ProgramItem $item): RedirectResponse
    {
        $event = $item->event;
        if ($event->status !== Event::STATUS_PUBLISHED) {
            abort(404);
        }

        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'start_at' => ['nullable', 'date'],
            'end_at' => ['nullable', 'date'],
            'type' => ['nullable', 'string', 'max:100'],
            'assigned_to' => ['nullable', 'exists:users,id'],
        ]);

        // Ensure dates (if provided) are within event timeframe
        $eventStart = $event->start_at ? Carbon::parse($event->start_at) : null;
        $eventEnd = $event->end_at ? Carbon::parse($event->end_at) : null;

        $start = $request->start_at ? Carbon::parse($request->start_at) : null;
        $end = $request->end_at ? Carbon::parse($request->end_at) : null;

        $errors = [];
        if ($start && $eventStart && $start->lt($eventStart)) {
            $errors['start_at'] = 'Start time must be on or after the event start time (' . $eventStart->format('Y-m-d H:i') . ').';
        }
        if ($end && $eventEnd && $end->gt($eventEnd)) {
            $errors['end_at'] = 'End time must be on or before the event end time (' . $eventEnd->format('Y-m-d H:i') . ').';
        }
        if (($start && !$end) || (!$start && $end)) {
            $errors['start_at'] = 'Both start and end time must be provided together.';
        }
        if ($start && $end && $start->gt($end)) {
            $errors['start_at'] = 'Start time must be before end time.';
        }

        if (!empty($errors)) {
            return back()->withErrors($errors)->withInput()->with('program_item_error_item_id', $item->id);
        }

        $item->update($request->only(['title', 'start_at', 'end_at', 'type', 'assigned_to']));

        // Recalculate order after updates
        $this->reorderByStart($event);

        return back()->with('success', 'Program item updated.');
    }

    /**
     * Reorder program items for an event. Accepts array of ids in new order: { order: [id1,id2,...] }
     */
    public function reorderItems(Request $request, Event $event): JsonResponse
    {
        if ($event->status !== Event::STATUS_PUBLISHED) {
            return response()->json(['message' => 'Event not available for reordering.'], 404);
        }

        $data = $request->validate([
            'order' => ['required', 'array'],
            'order.*' => ['integer', 'exists:program_items,id'],
        ]);

        $ids = $data['order'];

        // Ensure all ids belong to this event
        $count = ProgramItem::whereIn('id', $ids)->where('event_id', $event->id)->count();
        if ($count !== count($ids)) {
            return response()->json(['message' => 'Invalid program items for this event.'], 422);
        }

        DB::transaction(function () use ($ids) {
            foreach ($ids as $index => $id) {
                ProgramItem::where('id', $id)->update(['order' => $index + 1]);
            }
        });

        return response()->json(['message' => 'Order updated.']);
    }

    /**
     * Recalculate ordering of program items for an event based on start_at, then end_at, then id.
     */
    protected function reorderByStart(Event $event): void
    {
        $items = ProgramItem::where('event_id', $event->id)->get()->sortBy(function ($i) {
            // Use a far future date for nulls so they appear at end
            return $i->start_at ? Carbon::parse($i->start_at) : Carbon::createFromDate(9999, 12, 31);
        })->values();

        DB::transaction(function () use ($items) {
            foreach ($items as $index => $it) {
                $newOrder = $index + 1;
                if ($it->order !== $newOrder) {
                    $it->order = $newOrder;
                    $it->save();
                }
            }
        });
    }

    /**
     * Delete a program item.
     */
    public function destroyItem(ProgramItem $item): RedirectResponse
    {
        $event = $item->event;
        if ($event->status !== Event::STATUS_PUBLISHED) {
            abort(404);
        }

        $item->delete();

        return back()->with('success', 'Program item removed.');
    }
}
