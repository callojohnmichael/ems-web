<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Participant;
use App\Models\Post;
use App\Models\SupportTicket;
use App\Models\Venue;
use App\Services\ReportInsightsService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function __construct(
        private ReportInsightsService $insights
    ) {}

    public function index(Request $request): View
    {
        [$startDate, $endDate] = $this->resolveDateRange($request);

        $eventsQuery = $this->eventsInRange($startDate, $endDate);

        $statusCounts = (clone $eventsQuery)
            ->select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        $rows = collect([
            ['Metric' => 'Total Events', 'Value' => (clone $eventsQuery)->count()],
            ['Metric' => 'Published Events', 'Value' => (int) ($statusCounts['published'] ?? 0)],
            ['Metric' => 'Pending Approvals', 'Value' => (int) ($statusCounts['pending_approvals'] ?? 0)],
            ['Metric' => 'Completed Events', 'Value' => (int) ($statusCounts['completed'] ?? 0)],
            ['Metric' => 'Participants Added', 'Value' => Participant::whereHas('event', fn ($q) => $this->applyEventDateRange($q, $startDate, $endDate))->count()],
            ['Metric' => 'Venues Used', 'Value' => Venue::whereHas('events', fn ($q) => $this->applyEventDateRange($q, $startDate, $endDate))->count()],
        ])->toArray();

        $totalEvents = (clone $eventsQuery)->count();
        $participantsCount = Participant::whereHas('event', fn ($q) => $this->applyEventDateRange($q, $startDate, $endDate))->count();
        $venueRow = collect($rows)->firstWhere('Metric', 'Venues Used');
        $venuesUsed = (int) ($venueRow['Value'] ?? 0);

        return $this->reportView(
            request: $request,
            section: 'overview',
            title: 'Executive Overview',
            cards: [
                'Total Events' => $totalEvents,
                'Published' => (int) ($statusCounts['published'] ?? 0),
                'Pending' => (int) ($statusCounts['pending_approvals'] ?? 0),
                'Participants' => $participantsCount,
            ],
            columns: ['Metric', 'Value'],
            rows: $rows,
            charts: [
                $this->makeChart(
                    id: 'overview-status',
                    title: 'Event Status Breakdown',
                    type: 'doughnut',
                    labels: collect($statusCounts)->keys()->map(fn ($status) => ucfirst(str_replace('_', ' ', $status)))->values()->toArray(),
                    data: collect($statusCounts)->values()->map(fn ($value) => (int) $value)->toArray(),
                    datasetLabel: 'Events',
                    fullWidth: true
                ),
                $this->makeChart(
                    id: 'overview-metrics',
                    title: 'Core Metrics',
                    type: 'bar',
                    labels: array_column($rows, 'Metric'),
                    data: array_map(fn ($row) => (float) $row['Value'], $rows),
                    datasetLabel: 'Total'
                ),
            ],
            interpretations: $this->insights->overview([
                'total_events' => $totalEvents,
                'pending' => (int) ($statusCounts['pending_approvals'] ?? 0),
                'published' => (int) ($statusCounts['published'] ?? 0),
                'participants' => $participantsCount,
                'venues_used' => $venuesUsed,
            ])
        );
    }

    public function pipeline(Request $request): View
    {
        [$startDate, $endDate] = $this->resolveDateRange($request);
        $eventsQuery = $this->eventsInRange($startDate, $endDate);

        $statusRows = (clone $eventsQuery)
            ->select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->orderByDesc('total')
            ->get()
            ->map(fn ($row) => [
                'Status' => ucfirst(str_replace('_', ' ', $row->status)),
                'Total' => (int) $row->total,
            ])
            ->toArray();

        $cards = [
            'Total Events' => (clone $eventsQuery)->count(),
            'Pending Approvals' => (clone $eventsQuery)->where('status', Event::STATUS_PENDING_APPROVAL)->count(),
            'Approved' => (clone $eventsQuery)->where('status', Event::STATUS_APPROVED)->count(),
            'Published' => (clone $eventsQuery)->where('status', Event::STATUS_PUBLISHED)->count(),
        ];

        return $this->reportView(
            request: $request,
            section: 'pipeline',
            title: 'Event Pipeline',
            cards: $cards,
            columns: ['Status', 'Total'],
            rows: $statusRows,
            charts: [
                $this->makeChart(
                    id: 'pipeline-status',
                    title: 'Pipeline by Status',
                    type: 'bar',
                    labels: array_column($statusRows, 'Status'),
                    data: array_map(fn ($row) => (int) $row['Total'], $statusRows),
                    datasetLabel: 'Events'
                ),
            ],
            interpretations: $this->insights->pipeline([
                'total_events' => $cards['Total Events'],
                'pending' => $cards['Pending Approvals'],
                'approved' => $cards['Approved'],
                'published' => $cards['Published'],
                'status_rows' => $statusRows,
            ])
        );
    }

    public function participants(Request $request): View
    {
        [$startDate, $endDate] = $this->resolveDateRange($request);

        $participantsQuery = Participant::query()
            ->whereHas('event', fn ($q) => $this->applyEventDateRange($q, $startDate, $endDate));

        $statusRows = (clone $participantsQuery)
            ->select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->orderByDesc('total')
            ->get()
            ->map(fn ($row) => [
                'Status' => ucfirst($row->status),
                'Total' => (int) $row->total,
            ])
            ->toArray();

        $cards = [
            'Total Participants' => (clone $participantsQuery)->count(),
            'Confirmed' => (clone $participantsQuery)->where('status', 'confirmed')->count(),
            'Attended' => (clone $participantsQuery)->where('status', 'attended')->count(),
            'Absent' => (clone $participantsQuery)->where('status', 'absent')->count(),
        ];

        return $this->reportView(
            request: $request,
            section: 'participants',
            title: 'Participants & Attendance',
            cards: $cards,
            columns: ['Status', 'Total'],
            rows: $statusRows,
            charts: [
                $this->makeChart(
                    id: 'participants-status',
                    title: 'Participant Status Distribution',
                    type: 'doughnut',
                    labels: array_column($statusRows, 'Status'),
                    data: array_map(fn ($row) => (int) $row['Total'], $statusRows),
                    datasetLabel: 'Participants',
                    fullWidth: true
                ),
            ],
            interpretations: $this->insights->participants([
                'total' => $cards['Total Participants'],
                'confirmed' => $cards['Confirmed'],
                'attended' => $cards['Attended'],
                'absent' => $cards['Absent'],
            ])
        );
    }

    public function venues(Request $request): View
    {
        [$startDate, $endDate] = $this->resolveDateRange($request);

        $venueRows = Venue::query()
            ->withCount(['events as events_count' => fn ($q) => $this->applyEventDateRange($q, $startDate, $endDate)])
            ->orderByDesc('events_count')
            ->take(20)
            ->get()
            ->map(fn ($venue) => [
                'Venue' => $venue->name,
                'Events' => (int) $venue->events_count,
                'Capacity' => (int) ($venue->capacity ?? 0),
            ])
            ->toArray();

        $bookingsInRange = DB::table('venue_bookings')
            ->whereBetween('start_at', [$startDate->copy()->startOfDay(), $endDate->copy()->endOfDay()]);

        $venuesUsed = Venue::whereHas('events', fn ($q) => $this->applyEventDateRange($q, $startDate, $endDate))->count();
        $totalBookings = (clone $bookingsInRange)->count();
        $firstVenue = collect($venueRows)->first();
        $topVenueEvents = (int) ($firstVenue['Events'] ?? 0);
        $topVenueName = $firstVenue['Venue'] ?? null;

        return $this->reportView(
            request: $request,
            section: 'venues',
            title: 'Venue Utilization',
            cards: [
                'Venues Used' => $venuesUsed,
                'Total Bookings' => $totalBookings,
                'Top Venue Events' => $topVenueEvents,
                'Total Venues' => Venue::count(),
            ],
            columns: ['Venue', 'Events', 'Capacity'],
            rows: $venueRows,
            charts: [
                $this->makeChart(
                    id: 'venues-top',
                    title: 'Top Venues by Event Count',
                    type: 'bar',
                    labels: array_slice(array_column($venueRows, 'Venue'), 0, 8),
                    data: array_slice(array_map(fn ($row) => (int) $row['Events'], $venueRows), 0, 8),
                    datasetLabel: 'Events',
                    horizontal: true
                ),
            ],
            interpretations: $this->insights->venues([
                'venues_used' => $venuesUsed,
                'total_bookings' => $totalBookings,
                'top_venue_events' => $topVenueEvents,
                'top_venue_name' => $topVenueName,
                'venue_rows' => $venueRows,
            ])
        );
    }

    public function finance(Request $request): View
    {
        [$startDate, $endDate] = $this->resolveDateRange($request);

        $financeTableExists = Schema::hasTable('event_finance_requests');
        $logisticsTableExists = Schema::hasTable('event_logistics_items');

        $financeTotal = 0;
        $financeApproved = 0;
        $financeRows = [];

        if ($financeTableExists) {
            $financeQuery = DB::table('event_finance_requests as efr')
                ->join('events', 'events.id', '=', 'efr.event_id')
                ->whereBetween('events.start_at', [$startDate->copy()->startOfDay(), $endDate->copy()->endOfDay()]);

            $financeTotal = (float) (clone $financeQuery)->sum('efr.grand_total');
            $financeApproved = (float) (clone $financeQuery)
                ->where('efr.status', 'approved')
                ->sum('efr.grand_total');

            $financeRows = (clone $financeQuery)
                ->select('efr.status', DB::raw('COUNT(*) as total_requests'), DB::raw('COALESCE(SUM(efr.grand_total),0) as total_amount'))
                ->groupBy('efr.status')
                ->orderByDesc('total_requests')
                ->get()
                ->map(fn ($row) => [
                    'Status' => ucfirst($row->status),
                    'Requests' => (int) $row->total_requests,
                    'Total Amount' => number_format((float) $row->total_amount, 2),
                ])
                ->toArray();
        }

        $logisticsTotal = 0;
        if ($logisticsTableExists) {
            $logisticsTotal = (float) DB::table('event_logistics_items as eli')
                ->join('events', 'events.id', '=', 'eli.event_id')
                ->whereBetween('events.start_at', [$startDate->copy()->startOfDay(), $endDate->copy()->endOfDay()])
                ->sum('eli.subtotal');
        }

        $openRequests = $financeTableExists
            ? DB::table('event_finance_requests as efr')
                ->join('events', 'events.id', '=', 'efr.event_id')
                ->whereBetween('events.start_at', [$startDate->copy()->startOfDay(), $endDate->copy()->endOfDay()])
                ->where('efr.status', 'pending')
                ->count()
            : 0;

        return $this->reportView(
            request: $request,
            section: 'finance',
            title: 'Finance & Logistics',
            cards: [
                'Total Requested Budget' => number_format($financeTotal, 2),
                'Approved Budget' => number_format($financeApproved, 2),
                'Logistics Cost' => number_format($logisticsTotal, 2),
                'Open Requests' => $openRequests,
            ],
            columns: ['Status', 'Requests', 'Total Amount'],
            rows: $financeRows,
            charts: [
                $this->makeChart(
                    id: 'finance-requests',
                    title: 'Finance Requests by Status',
                    type: 'bar',
                    labels: array_column($financeRows, 'Status'),
                    data: array_map(fn ($row) => (int) $row['Requests'], $financeRows),
                    datasetLabel: 'Requests'
                ),
                $this->makeChart(
                    id: 'finance-amounts',
                    title: 'Budget Snapshot',
                    type: 'pie',
                    labels: ['Requested', 'Approved', 'Logistics'],
                    data: [$financeTotal, $financeApproved, $logisticsTotal],
                    datasetLabel: 'Amount'
                ),
            ],
            interpretations: $this->insights->finance([
                'total_requested' => $financeTotal,
                'approved' => $financeApproved,
                'logistics' => $logisticsTotal,
                'open_requests' => $openRequests,
            ])
        );
    }

    public function engagement(Request $request): View
    {
        [$startDate, $endDate] = $this->resolveDateRange($request);

        $engagementPostTable = Schema::hasTable('event_posts')
            ? 'event_posts'
            : (Schema::hasTable('posts') ? 'posts' : null);

        $postRows = $engagementPostTable
            ? DB::table("{$engagementPostTable} as p")
                ->join('events', 'events.id', '=', 'p.event_id')
                ->whereBetween('events.start_at', [$startDate->copy()->startOfDay(), $endDate->copy()->endOfDay()])
                ->select('events.title', DB::raw('COUNT(p.id) as total_posts'))
                ->groupBy('events.title')
                ->orderByDesc('total_posts')
                ->take(20)
                ->get()
                ->map(fn ($row) => [
                    'Event' => $row->title,
                    'Posts' => (int) $row->total_posts,
                ])
                ->toArray()
            : [];

        $postsCount = $engagementPostTable
            ? DB::table("{$engagementPostTable} as p")
                ->join('events', 'events.id', '=', 'p.event_id')
                ->whereBetween('events.start_at', [$startDate->copy()->startOfDay(), $endDate->copy()->endOfDay()])
                ->count()
            : 0;

        $commentsCount = 0;
        if (Schema::hasTable('post_comments')) {
            if (Schema::hasTable('event_posts') && Schema::hasColumn('post_comments', 'event_post_id')) {
                $commentsCount = DB::table('post_comments')
                    ->join('event_posts', 'event_posts.id', '=', 'post_comments.event_post_id')
                    ->join('events', 'events.id', '=', 'event_posts.event_id')
                    ->whereBetween('events.start_at', [$startDate->copy()->startOfDay(), $endDate->copy()->endOfDay()])
                    ->count();
            } elseif (Schema::hasTable('posts') && Schema::hasColumn('post_comments', 'post_id')) {
                $commentsCount = DB::table('post_comments')
                    ->join('posts', 'posts.id', '=', 'post_comments.post_id')
                    ->join('events', 'events.id', '=', 'posts.event_id')
                    ->whereBetween('events.start_at', [$startDate->copy()->startOfDay(), $endDate->copy()->endOfDay()])
                    ->count();
            }
        }

        $avgRating = null;
        if (Schema::hasTable('event_ratings')) {
            $avgRating = DB::table('event_ratings')
                ->join('events', 'events.id', '=', 'event_ratings.event_id')
                ->whereBetween('events.start_at', [$startDate->copy()->startOfDay(), $endDate->copy()->endOfDay()])
                ->avg('event_ratings.rating');
        }

        $firstPostRow = collect($postRows)->first();
        $topEventPosts = (int) ($firstPostRow['Posts'] ?? 0);
        $topEventName = $firstPostRow['Event'] ?? null;

        return $this->reportView(
            request: $request,
            section: 'engagement',
            title: 'Engagement & Media',
            cards: [
                'Posts' => $postsCount,
                'Comments' => $commentsCount,
                'Top Event Posts' => $topEventPosts,
                'Average Rating' => $avgRating !== null ? number_format((float) $avgRating, 2) : 'N/A',
            ],
            columns: ['Event', 'Posts'],
            rows: $postRows,
            charts: [
                $this->makeChart(
                    id: 'engagement-posts',
                    title: 'Top Events by Posts',
                    type: 'bar',
                    labels: array_slice(array_column($postRows, 'Event'), 0, 8),
                    data: array_slice(array_map(fn ($row) => (int) $row['Posts'], $postRows), 0, 8),
                    datasetLabel: 'Posts',
                    horizontal: true
                ),
            ],
            interpretations: $this->insights->engagement([
                'posts' => $postsCount,
                'comments' => $commentsCount,
                'top_event_posts' => $topEventPosts,
                'avg_rating' => $avgRating !== null ? (float) $avgRating : null,
                'top_event_name' => $topEventName,
            ])
        );
    }

    public function support(Request $request): View
    {
        [$startDate, $endDate] = $this->resolveDateRange($request);

        $ticketsQuery = SupportTicket::query()
            ->whereBetween('created_at', [$startDate->copy()->startOfDay(), $endDate->copy()->endOfDay()]);

        $statusRows = (clone $ticketsQuery)
            ->select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->orderByDesc('total')
            ->get()
            ->map(fn ($row) => [
                'Status' => ucfirst($row->status),
                'Tickets' => (int) $row->total,
            ])
            ->toArray();

        $messageCount = Schema::hasTable('support_messages')
            ? DB::table('support_messages as sm')
                ->join('support_tickets as st', 'st.id', '=', 'sm.support_ticket_id')
                ->whereBetween('st.created_at', [$startDate->copy()->startOfDay(), $endDate->copy()->endOfDay()])
                ->count()
            : 0;

        $totalTickets = (clone $ticketsQuery)->count();
        $openTickets = (clone $ticketsQuery)->where('status', 'open')->count();
        $closedTickets = (clone $ticketsQuery)->where('status', 'closed')->count();

        return $this->reportView(
            request: $request,
            section: 'support',
            title: 'Support & Operations',
            cards: [
                'Total Tickets' => $totalTickets,
                'Open Tickets' => $openTickets,
                'Closed Tickets' => $closedTickets,
                'Messages' => $messageCount,
            ],
            columns: ['Status', 'Tickets'],
            rows: $statusRows,
            charts: [
                $this->makeChart(
                    id: 'support-status',
                    title: 'Ticket Status Distribution',
                    type: 'doughnut',
                    labels: array_column($statusRows, 'Status'),
                    data: array_map(fn ($row) => (int) $row['Tickets'], $statusRows),
                    datasetLabel: 'Tickets'
                ),
            ],
            interpretations: $this->insights->support([
                'total_tickets' => $totalTickets,
                'open' => $openTickets,
                'closed' => $closedTickets,
                'messages' => $messageCount,
            ])
        );
    }

    public function export(Request $request, string $section): StreamedResponse
    {
        $allowed = [
            'overview' => fn () => $this->index($request),
            'pipeline' => fn () => $this->pipeline($request),
            'participants' => fn () => $this->participants($request),
            'venues' => fn () => $this->venues($request),
            'finance' => fn () => $this->finance($request),
            'engagement' => fn () => $this->engagement($request),
            'support' => fn () => $this->support($request),
        ];

        abort_unless(array_key_exists($section, $allowed), 404);

        $reportPayload = $this->buildExportPayload($request, $section);
        $filename = "report-{$section}-" . now()->format('Y-m-d-His') . '.csv';

        return response()->streamDownload(function () use ($reportPayload) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $reportPayload['columns']);

            foreach ($reportPayload['rows'] as $row) {
                fputcsv($handle, array_values($row));
            }

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    private function buildExportPayload(Request $request, string $section): array
    {
        [$startDate, $endDate] = $this->resolveDateRange($request);

        return match ($section) {
            'overview' => [
                'columns' => ['Metric', 'Value'],
                'rows' => [
                    ['Metric' => 'Date Range', 'Value' => $startDate->toDateString() . ' to ' . $endDate->toDateString()],
                    ['Metric' => 'Total Events', 'Value' => $this->eventsInRange($startDate, $endDate)->count()],
                    ['Metric' => 'Total Participants', 'Value' => Participant::whereHas('event', fn ($q) => $this->applyEventDateRange($q, $startDate, $endDate))->count()],
                ],
            ],
            'pipeline' => [
                'columns' => ['Status', 'Total'],
                'rows' => $this->eventsInRange($startDate, $endDate)
                    ->select('status', DB::raw('COUNT(*) as total'))
                    ->groupBy('status')
                    ->get()
                    ->map(fn ($row) => ['Status' => ucfirst(str_replace('_', ' ', $row->status)), 'Total' => (int) $row->total])
                    ->toArray(),
            ],
            'participants' => [
                'columns' => ['Status', 'Total'],
                'rows' => Participant::query()
                    ->whereHas('event', fn ($q) => $this->applyEventDateRange($q, $startDate, $endDate))
                    ->select('status', DB::raw('COUNT(*) as total'))
                    ->groupBy('status')
                    ->get()
                    ->map(fn ($row) => ['Status' => ucfirst($row->status), 'Total' => (int) $row->total])
                    ->toArray(),
            ],
            'venues' => [
                'columns' => ['Venue', 'Events', 'Capacity'],
                'rows' => Venue::query()
                    ->withCount(['events as events_count' => fn ($q) => $this->applyEventDateRange($q, $startDate, $endDate)])
                    ->orderByDesc('events_count')
                    ->take(50)
                    ->get()
                    ->map(fn ($row) => ['Venue' => $row->name, 'Events' => (int) $row->events_count, 'Capacity' => (int) ($row->capacity ?? 0)])
                    ->toArray(),
            ],
            'finance' => [
                'columns' => ['Status', 'Requests', 'Total Amount'],
                'rows' => Schema::hasTable('event_finance_requests')
                    ? DB::table('event_finance_requests as efr')
                        ->join('events', 'events.id', '=', 'efr.event_id')
                        ->whereBetween('events.start_at', [$startDate->copy()->startOfDay(), $endDate->copy()->endOfDay()])
                        ->select('efr.status', DB::raw('COUNT(*) as total_requests'), DB::raw('COALESCE(SUM(efr.grand_total),0) as total_amount'))
                        ->groupBy('efr.status')
                        ->get()
                        ->map(fn ($row) => ['Status' => ucfirst($row->status), 'Requests' => (int) $row->total_requests, 'Total Amount' => number_format((float) $row->total_amount, 2)])
                        ->toArray()
                    : [],
            ],
            'engagement' => [
                'columns' => ['Event', 'Posts'],
                'rows' => $this->engagementRows($startDate, $endDate),
            ],
            'support' => [
                'columns' => ['Status', 'Tickets'],
                'rows' => SupportTicket::query()
                    ->whereBetween('created_at', [$startDate->copy()->startOfDay(), $endDate->copy()->endOfDay()])
                    ->select('status', DB::raw('COUNT(*) as total'))
                    ->groupBy('status')
                    ->get()
                    ->map(fn ($row) => ['Status' => ucfirst($row->status), 'Tickets' => (int) $row->total])
                    ->toArray(),
            ],
            default => ['columns' => ['Metric', 'Value'], 'rows' => []],
        };
    }

    private function reportView(
        Request $request,
        string $section,
        string $title,
        array $cards,
        array $columns,
        array $rows,
        array $charts = [],
        array $interpretations = []
    ): View {
        [$startDate, $endDate] = $this->resolveDateRange($request);

        return view('reports.show', [
            'section' => $section,
            'title' => $title,
            'cards' => $cards,
            'columns' => $columns,
            'rows' => $rows,
            'charts' => $charts,
            'interpretations' => $interpretations,
            'startDate' => $startDate->toDateString(),
            'endDate' => $endDate->toDateString(),
        ]);
    }

    private function makeChart(
        string $id,
        string $title,
        string $type,
        array $labels,
        array $data,
        string $datasetLabel,
        bool $horizontal = false,
        bool $fullWidth = false
    ): array {
        return [
            'id' => $id,
            'title' => $title,
            'type' => $type,
            'labels' => array_values($labels),
            'data' => array_values($data),
            'datasetLabel' => $datasetLabel,
            'horizontal' => $horizontal,
            'fullWidth' => $fullWidth,
        ];
    }

    private function engagementRows(Carbon $startDate, Carbon $endDate): array
    {
        $engagementPostTable = Schema::hasTable('event_posts')
            ? 'event_posts'
            : (Schema::hasTable('posts') ? 'posts' : null);

        if (! $engagementPostTable) {
            return [];
        }

        return DB::table("{$engagementPostTable} as p")
            ->join('events', 'events.id', '=', 'p.event_id')
            ->whereBetween('events.start_at', [$startDate->copy()->startOfDay(), $endDate->copy()->endOfDay()])
            ->select('events.title', DB::raw('COUNT(p.id) as total_posts'))
            ->groupBy('events.title')
            ->orderByDesc('total_posts')
            ->get()
            ->map(fn ($row) => ['Event' => $row->title, 'Posts' => (int) $row->total_posts])
            ->toArray();
    }

    private function resolveDateRange(Request $request): array
    {
        [$defaultStart, $defaultEnd] = $this->defaultDataRange();

        $startDate = Carbon::parse($request->query('start_date', $defaultStart->toDateString()));
        $endDate = Carbon::parse($request->query('end_date', $defaultEnd->toDateString()));

        if ($startDate->gt($endDate)) {
            [$startDate, $endDate] = [$endDate->copy(), $startDate->copy()];
        }

        return [$startDate->startOfDay(), $endDate->endOfDay()];
    }

    private function defaultDataRange(): array
    {
        $minimums = collect([
            Event::query()->min('start_at'),
            Participant::query()->min('created_at'),
            Post::query()->min('created_at'),
            Venue::query()->min('created_at'),
            SupportTicket::query()->min('created_at'),
        ])->filter();

        $maximums = collect([
            Event::query()->max('start_at'),
            Participant::query()->max('created_at'),
            Post::query()->max('created_at'),
            Venue::query()->max('created_at'),
            SupportTicket::query()->max('created_at'),
        ])->filter();

        if ($minimums->isEmpty() || $maximums->isEmpty()) {
            return [now()->startOfMonth(), now()];
        }

        return [
            Carbon::parse($minimums->min())->startOfDay(),
            Carbon::parse($maximums->max())->endOfDay(),
        ];
    }

    private function eventsInRange(Carbon $startDate, Carbon $endDate)
    {
        return Event::query()->whereBetween('start_at', [$startDate, $endDate]);
    }

    private function applyEventDateRange($query, Carbon $startDate, Carbon $endDate): void
    {
        $query->whereBetween('start_at', [$startDate, $endDate]);
    }
}
