<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventPost;
use App\Services\MultimediaAnalyticsService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class MultimediaController extends Controller
{
    public function __construct(
        private MultimediaAnalyticsService $multimediaAnalytics
    ) {}

    public function index(Request $request): View
    {
        $query = EventPost::with([
            'event',
            'user',
            'media',
            'reactions',
            'comments.user',
        ]);

        if ($request->filled('event_id')) {
            $query->where('event_id', $request->event_id);
        }
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $sort = $request->query('sort', 'latest');
        if ($sort === 'oldest') {
            $query->oldest();
        } else {
            $query->latest();
        }

        $posts = $query->paginate(10)->withQueryString();

        $periodEnd = Carbon::now();
        $periodStart = Carbon::now()->subDays(30);
        $multimediaSummary = $this->multimediaAnalytics->summary($periodStart, $periodEnd);

        $eventsForFilter = Event::query()
            ->whereHas('multimediaPosts')
            ->orderByDesc('start_at')
            ->take(100)
            ->get(['id', 'title']);

        $view = $request->query('view') === 'gallery' ? 'multimedia.gallery' : 'multimedia.index';

        return view($view, compact('posts', 'multimediaSummary', 'eventsForFilter'));
    }

    public function posts(): View
    {
        $posts = Post::query()
            ->with(['event', 'user'])
            ->latest()
            ->paginate(12);

        return view('media.posts.index', compact('posts'));
    }

    public function postsShow(Post $post): View
    {
        $post->load(['event', 'user', 'comments.user']);

        return view('media.posts.show', compact('post'));
    }

    public function postsCreate(): View
    {
        $events = Event::query()
            ->whereIn('status', [Event::STATUS_PENDING_APPROVAL, Event::STATUS_APPROVED, Event::STATUS_PUBLISHED])
            ->orderByDesc('start_at')
            ->get();

        return view('media.posts.create', compact('events'));
    }

    public function postsStore(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'event_id' => ['required', 'exists:events,id'],
            'body' => ['required', 'string', 'max:5000'],
            'type' => ['nullable', 'string', 'max:32'],
        ]);

        $post = Post::create([
            'event_id' => $validated['event_id'],
            'user_id' => Auth::id(),
            'body' => $validated['body'],
            'type' => $validated['type'] ?? 'post',
        ]);

        return redirect()
            ->route('media.posts.show', $post)
            ->with('success', 'Post created successfully.');
    }

    public function postsDestroy(Post $post): RedirectResponse
    {
        $user = auth()->user();
        $canManageAll = $user->isAdmin() || $user->can('manage all posts');

        if (!$canManageAll && (int) $post->user_id !== (int) $user->id) {
            abort(403);
        }

        $post->delete();

        return redirect()
            ->route('media.posts')
            ->with('success', 'Post deleted successfully.');
    }
}
