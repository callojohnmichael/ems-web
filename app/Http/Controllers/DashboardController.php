<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Participant;
use App\Models\Post;
use App\Models\SupportTicket;
use App\Models\Venue;
use App\Services\EventService;
use App\Services\MultimediaAnalyticsService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        private EventService $eventService,
        private MultimediaAnalyticsService $multimediaAnalytics
    ) {}

    /**
     * Redirect to the role-specific dashboard.
     */
    public function redirect(Request $request): RedirectResponse
    {
        $user = $request->user();
        if (! $user) {
            return redirect()->route('login');
        }

        return redirect()->route($user->dashboardRoute());
    }

    public function admin(Request $request): View
    {
        $pendingEvents = $this->eventService->getPendingEvents();
        $upcomingEvents = $this->eventService->getUpcomingEvents();
        $totalEvents = Event::count();

        $statusRows = Event::query()
            ->select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->orderByDesc('total')
            ->get();

        $months = collect(range(5, 0))
            ->map(fn ($offset) => now()->subMonths($offset)->startOfMonth())
            ->push(now()->startOfMonth());

        $eventsByMonthRaw = Event::query()
            ->where('start_at', '>=', $months->first()->copy()->startOfMonth())
            ->selectRaw("DATE_FORMAT(start_at, '%Y-%m') as ym, COUNT(*) as total")
            ->groupBy('ym')
            ->pluck('total', 'ym');

        $eventsByMonth = $months->map(function ($month) use ($eventsByMonthRaw) {
            $key = $month->format('Y-m');
            return [
                'label' => $month->format('M Y'),
                'value' => (int) ($eventsByMonthRaw[$key] ?? 0),
            ];
        })->values();

        $statusChart = $statusRows->map(fn ($row) => [
            'label' => ucfirst(str_replace('_', ' ', $row->status)),
            'value' => (int) $row->total,
        ])->values();

        return view('dashboard.admin', compact(
            'pendingEvents',
            'upcomingEvents',
            'totalEvents',
            'eventsByMonth',
            'statusChart'
        ));
    }

    public function user(Request $request): View
    {
        $user = $request->user();
        $userEvents = $this->eventService->getUserEvents($user);
        $pendingRequests = $userEvents->where('status', \App\Models\Event::STATUS_PENDING_APPROVAL);
        $approvedEvents = $userEvents->where('status', \App\Models\Event::STATUS_APPROVED);
        $publishedEvents = $this->eventService->getPublishedEvents()->take(5);
        
        return view('dashboard.user', compact('userEvents', 'pendingRequests', 'approvedEvents', 'publishedEvents'));
    }

    public function media(Request $request): View
    {
        $upcomingEvents = $this->eventService->getUpcomingEvents();
        $recentEvents = \App\Models\Event::where('status', \App\Models\Event::STATUS_PUBLISHED)
            ->where('end_at', '<', now())
            ->orderBy('end_at', 'desc')
            ->take(5)
            ->get();

        $periodEnd = Carbon::now();
        $periodStart = Carbon::now()->subDays(30);
        $multimediaSummary = $this->multimediaAnalytics->summary($periodStart, $periodEnd);

        return view('dashboard.media', compact('upcomingEvents', 'recentEvents', 'multimediaSummary'));
    }

    /**
     * Role test pages (to verify 403 when accessed by wrong role).
     */
    public function adminApprovals(): View
    {
        $pendingEvents = $this->eventService->getPendingEvents();
        return view('test.admin-approvals', compact('pendingEvents'));
    }

    public function userRequests(): View
    {
        $user = request()->user();
        $userEvents = $this->eventService->getUserEvents($user);
        return view('test.user-requests', compact('userEvents'));
    }

    public function mediaPosts(): View
    {
        return view('test.media-posts');
    }

    public function insights(Request $request): View
    {
        $months = collect(range(5, 0))
            ->map(fn ($offset) => now()->subMonths($offset)->startOfMonth())
            ->push(now()->startOfMonth());

        $eventsByMonthRaw = Event::query()
            ->where('start_at', '>=', $months->first()->copy()->startOfMonth())
            ->selectRaw("DATE_FORMAT(start_at, '%Y-%m') as ym, COUNT(*) as total")
            ->groupBy('ym')
            ->pluck('total', 'ym');

        $eventsByMonth = $months->map(function ($month) use ($eventsByMonthRaw) {
            $key = $month->format('Y-m');
            return [
                'label' => $month->format('M Y'),
                'total' => (int) ($eventsByMonthRaw[$key] ?? 0),
            ];
        })->values();

        $eventStatusRows = Event::query()
            ->select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->orderByDesc('total')
            ->get();

        $participantStatusRows = Participant::query()
            ->select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->orderByDesc('total')
            ->get();

        $venueUsageRows = Venue::query()
            ->withCount('events')
            ->orderByDesc('events_count')
            ->take(8)
            ->get();

        return view('dashboard', [
            'kpis' => [
                'events_total' => Event::count(),
                'participants_total' => Participant::count(),
                'posts_total' => Post::count(),
                'support_total' => SupportTicket::count(),
            ],
            'eventsByMonth' => $eventsByMonth,
            'eventStatus' => $eventStatusRows->map(fn ($row) => [
                'label' => ucfirst(str_replace('_', ' ', $row->status)),
                'value' => (int) $row->total,
            ])->values(),
            'participantStatus' => $participantStatusRows->map(fn ($row) => [
                'label' => ucfirst($row->status),
                'value' => (int) $row->total,
            ])->values(),
            'venueUsage' => $venueUsageRows->map(fn ($row) => [
                'label' => $row->name,
                'value' => (int) $row->events_count,
            ])->values(),
        ]);
    }
}
