<?php

namespace App\Services;

class ReportInsightsService
{
    private const LOW_ATTENDANCE_THRESHOLD = 50;

    private const RATING_STRONG = 4.0;

    /**
     * @return array<int, array{text: string, type: 'info'|'success'|'warning'}>
     */
    public function overview(array $data): array
    {
        $totalEvents = (int) ($data['total_events'] ?? 0);
        $pending = (int) ($data['pending'] ?? 0);
        $participants = (int) ($data['participants'] ?? 0);
        $venuesUsed = (int) ($data['venues_used'] ?? 0);

        $insights = [];

        if ($totalEvents === 0) {
            $insights[] = [
                'text' => 'No events in this period. Expand the date range or create new events to see activity here.',
                'type' => 'info',
            ];
        } else {
            $insights[] = [
                'text' => "You have {$totalEvents} " . str('event')->plural($totalEvents) . " in this period — this is your overall activity level.",
                'type' => 'info',
            ];
        }

        if ($pending > 0) {
            $insights[] = [
                'text' => "{$pending} " . str('event')->plural($pending) . " need a decision. Approve or reject them in the pipeline so they can move forward.",
                'type' => 'warning',
            ];
        }

        if ($totalEvents > 0) {
            $insights[] = [
                'text' => $participants > 0
                    ? "{$participants} participants are linked to these events across {$venuesUsed} " . str('venue')->plural($venuesUsed) . " — use the Participants and Venues tabs for detail."
                    : "No participants yet for these events. Add or invite participants to track attendance.",
                'type' => 'info',
            ];
        }

        return $insights;
    }

    /**
     * @return array<int, array{text: string, type: 'info'|'success'|'warning'}>
     */
    public function pipeline(array $data): array
    {
        $totalEvents = (int) ($data['total_events'] ?? 0);
        $pending = (int) ($data['pending'] ?? 0);
        $approved = (int) ($data['approved'] ?? 0);
        $published = (int) ($data['published'] ?? 0);
        $statusRows = $data['status_rows'] ?? [];

        $insights = [];

        if ($totalEvents === 0) {
            $insights[] = [
                'text' => 'No events in this period. Change the date range or check that events are being created.',
                'type' => 'info',
            ];

            return $insights;
        }

        $dominant = collect($statusRows)->sortByDesc('Total')->first();
        if ($dominant) {
            $status = str($dominant['Status'])->lower();
            $count = $dominant['Total'];
            $pct = $totalEvents > 0 ? (int) round(($count / $totalEvents) * 100) : 0;
            if ($status === 'published') {
                $insights[] = [
                    'text' => "{$pct}% of events are published ({$count}) — your pipeline is in good shape.",
                    'type' => 'success',
                ];
            } elseif ($status === 'pending approvals' || $status === 'pending approval') {
                $insights[] = [
                    'text' => "{$pct}% of events are still pending approval ({$count}). Review them so they can move to published.",
                    'type' => 'warning',
                ];
            } else {
                $insights[] = [
                    'text' => "Most events are {$status} ({$count} of {$totalEvents}).",
                    'type' => 'info',
                ];
            }
        }

        if ($pending > 0) {
            $insights[] = [
                'text' => "{$pending} " . str('event')->plural($pending) . " waiting on your decision. Open the pipeline to approve or reject.",
                'type' => 'warning',
            ];
        }

        if ($approved > 0 && $published === 0) {
            $insights[] = [
                'text' => "{$approved} approved " . str('event')->plural($approved) . " are ready to publish. Publishing makes them visible to users.",
                'type' => 'info',
            ];
        }

        return $insights;
    }

    /**
     * @return array<int, array{text: string, type: 'info'|'success'|'warning'}>
     */
    public function participants(array $data): array
    {
        $total = (int) ($data['total'] ?? 0);
        $confirmed = (int) ($data['confirmed'] ?? 0);
        $attended = (int) ($data['attended'] ?? 0);
        $absent = (int) ($data['absent'] ?? 0);

        $insights = [];

        if ($total === 0) {
            $insights[] = [
                'text' => 'No participants in this period. Add participants to events or widen the date range.',
                'type' => 'info',
            ];

            return $insights;
        }

        $attendanceRate = $total > 0 ? (int) round(($attended / $total) * 100) : 0;
        $confirmationRate = $total > 0 ? (int) round(($confirmed / $total) * 100) : 0;

        $insights[] = [
            'text' => "{$total} participants are registered. {$attended} attended and " . ($absent > 0 ? "{$absent} were marked absent." : "none marked absent."),
            'type' => 'info',
        ];

        if ($attendanceRate >= self::LOW_ATTENDANCE_THRESHOLD) {
            $insights[] = [
                'text' => "Attendance rate is {$attendanceRate}% — strong show-up for this period.",
                'type' => 'success',
            ];
        } else {
            $insights[] = [
                'text' => "Attendance rate is {$attendanceRate}% ({$attended} of {$total}). Consider reminders or checking for scheduling conflicts.",
                'type' => 'warning',
            ];
        }

        $insights[] = [
            'text' => $confirmationRate >= 70
                ? "{$confirmationRate}% of participants confirmed — good response rate."
                : "{$confirmationRate}% confirmed. Sending reminders may improve confirmation and attendance.",
            'type' => $confirmationRate >= 70 ? 'success' : 'info',
        ];

        return $insights;
    }

    /**
     * @return array<int, array{text: string, type: 'info'|'success'|'warning'}>
     */
    public function venues(array $data): array
    {
        $venuesUsed = (int) ($data['venues_used'] ?? 0);
        $totalBookings = (int) ($data['total_bookings'] ?? 0);
        $topVenueEvents = (int) ($data['top_venue_events'] ?? 0);
        $topVenueName = $data['top_venue_name'] ?? null;

        $insights = [];

        if ($venuesUsed === 0) {
            $insights[] = [
                'text' => 'No venues were used in this period. Assign venues to events to see utilization here.',
                'type' => 'info',
            ];
        } else {
            $insights[] = [
                'text' => "{$venuesUsed} " . str('venue')->plural($venuesUsed) . " had events in this period. Use the table below to see which are busiest.",
                'type' => 'info',
            ];
        }

        if ($topVenueName !== null && $topVenueName !== '' && $topVenueEvents > 0) {
            $insights[] = [
                'text' => "\"{$topVenueName}\" is your busiest venue with {$topVenueEvents} " . str('event')->plural($topVenueEvents) . " — plan capacity or backups if needed.",
                'type' => 'info',
            ];
        }

        $insights[] = [
            'text' => "{$totalBookings} " . str('booking')->plural($totalBookings) . " in total. Check for double-bookings or gaps in the calendar.",
            'type' => 'info',
        ];

        return $insights;
    }

    /**
     * @return array<int, array{text: string, type: 'info'|'success'|'warning'}>
     */
    public function finance(array $data): array
    {
        $totalRequested = (float) ($data['total_requested'] ?? 0);
        $approved = (float) ($data['approved'] ?? 0);
        $logistics = (float) ($data['logistics'] ?? 0);
        $openRequests = (int) ($data['open_requests'] ?? 0);

        $insights = [];

        if ($totalRequested <= 0 && $logistics <= 0) {
            $insights[] = [
                'text' => 'No finance or logistics data in this period. Finance requests and logistics items will appear here once added to events.',
                'type' => 'info',
            ];

            return $insights;
        }

        $approvalPct = $totalRequested > 0 ? (int) round(($approved / $totalRequested) * 100) : 0;
        $insights[] = [
            'text' => 'Requested budget is ' . number_format($totalRequested, 2) . ', with ' . number_format($approved, 2) . ' approved (' . $approvalPct . '%).',
            'type' => 'info',
        ];

        if ($logistics > 0) {
            $logisticsPct = $totalRequested > 0 ? (int) round(($logistics / $totalRequested) * 100) : 0;
            $insights[] = [
                'text' => 'Logistics cost is ' . number_format($logistics, 2) . ($totalRequested > 0 ? " ({$logisticsPct}% of requested)." : '.'),
                'type' => 'info',
            ];
        }

        if ($openRequests > 0) {
            $insights[] = [
                'text' => "{$openRequests} finance " . str('request')->plural($openRequests) . " are still open. Approve or reject them so events can proceed.",
                'type' => 'warning',
            ];
        }

        return $insights;
    }

    /**
     * @return array<int, array{text: string, type: 'info'|'success'|'warning'}>
     */
    public function engagement(array $data): array
    {
        $posts = (int) ($data['posts'] ?? 0);
        $comments = (int) ($data['comments'] ?? 0);
        $topEventPosts = (int) ($data['top_event_posts'] ?? 0);
        $avgRating = isset($data['avg_rating']) ? (float) $data['avg_rating'] : null;
        $topEventName = $data['top_event_name'] ?? null;

        $insights = [];

        if ($posts === 0 && $comments === 0) {
            $insights[] = [
                'text' => 'No posts or comments in this period. More activity here usually means stronger audience interaction.',
                'type' => 'info',
            ];
        } else {
            $insights[] = [
                'text' => "{$posts} " . str('post')->plural($posts) . " and {$comments} " . str('comment')->plural($comments) . " — higher numbers mean more people are engaging with event content.",
                'type' => 'info',
            ];
        }

        if ($avgRating !== null) {
            $ratingText = number_format($avgRating, 2);
            if ($avgRating >= self::RATING_STRONG) {
                $insights[] = [
                    'text' => "Average rating is {$ratingText} out of 5 — participants are satisfied.",
                    'type' => 'success',
                ];
            } else {
                $insights[] = [
                    'text' => "Average rating is {$ratingText} out of 5. There may be room to improve with feedback or follow-up.",
                    'type' => 'info',
                ];
            }
        }

        if ($topEventName !== null && $topEventName !== '' && $topEventPosts > 0) {
            $insights[] = [
                'text' => "\"{$topEventName}\" had the most engagement ({$topEventPosts} " . str('post')->plural($topEventPosts) . "). Focus content or follow-up here.",
                'type' => 'info',
            ];
        }

        return $insights;
    }

    /**
     * @return array<int, array{text: string, type: 'info'|'success'|'warning'}>
     */
    public function support(array $data): array
    {
        $totalTickets = (int) ($data['total_tickets'] ?? 0);
        $open = (int) ($data['open'] ?? 0);
        $closed = (int) ($data['closed'] ?? 0);
        $messages = (int) ($data['messages'] ?? 0);

        $insights = [];

        if ($totalTickets === 0) {
            $insights[] = [
                'text' => 'No support tickets in this period. Tickets created in this date range will show here.',
                'type' => 'info',
            ];

            return $insights;
        }

        $openPct = $totalTickets > 0 ? (int) round(($open / $totalTickets) * 100) : 0;
        $insights[] = [
            'text' => "{$totalTickets} " . str('ticket')->plural($totalTickets) . " in total: {$open} still open ({$openPct}%), {$closed} closed. {$messages} " . str('message')->plural($messages) . " in threads.",
            'type' => 'info',
        ];

        if ($open > 0) {
            $insights[] = [
                'text' => "{$open} open " . str('ticket')->plural($open) . " need a response. Replying soon helps resolve issues faster.",
                'type' => 'warning',
            ];
        } elseif ($closed > 0) {
            $insights[] = [
                'text' => 'All tickets in this period are closed — support load looks clear.',
                'type' => 'success',
            ];
        }

        return $insights;
    }

    /**
     * @return array<int, array{text: string, type: 'info'|'success'|'warning'}>
     */
    public function multimedia(array $data): array
    {
        $posts = (int) ($data['posts'] ?? 0);
        $comments = (int) ($data['comments'] ?? 0);
        $reactions = (int) ($data['reactions'] ?? 0);
        $postsWithAi = (int) ($data['posts_with_ai'] ?? 0);
        $mediaUpload = (int) ($data['media_upload'] ?? 0);
        $mediaAi = (int) ($data['media_ai'] ?? 0);
        $topEventName = $data['top_event_name'] ?? null;
        $topEventPosts = (int) ($data['top_event_posts'] ?? 0);
        $topType = $data['top_type'] ?? null;

        $insights = [];

        if ($posts === 0) {
            $insights[] = [
                'text' => 'No multimedia posts in this period. Create posts from the Multimedia area to see analytics here.',
                'type' => 'info',
            ];

            return $insights;
        }

        $insights[] = [
            'text' => "{$posts} " . str('post')->plural($posts) . ", {$comments} " . str('comment')->plural($comments) . ", {$reactions} " . str('reaction')->plural($reactions) . " — engagement on event content.",
            'type' => 'info',
        ];

        if ($postsWithAi > 0) {
            $pct = $posts > 0 ? (int) round(($postsWithAi / $posts) * 100) : 0;
            $insights[] = [
                'text' => "{$pct}% of posts use AI captions or narrative ({$postsWithAi} of {$posts}).",
                'type' => 'info',
            ];
        }

        if ($topType !== null && $topType !== '') {
            $insights[] = [
                'text' => 'Most used post type: ' . ucfirst($topType) . '.',
                'type' => 'info',
            ];
        }

        if ($topEventName !== null && $topEventName !== '' && $topEventPosts > 0) {
            $insights[] = [
                'text' => "\"{$topEventName}\" has the most posts ({$topEventPosts}). Focus or replicate content strategy here.",
                'type' => 'info',
            ];
        }

        if ($mediaAi > 0 && ($mediaUpload + $mediaAi) > 0) {
            $totalMedia = $mediaUpload + $mediaAi;
            $aiPct = (int) round(($mediaAi / $totalMedia) * 100);
            $insights[] = [
                'text' => "{$aiPct}% of media items are AI-generated ({$mediaAi} of {$totalMedia}).",
                'type' => 'info',
            ];
        }

        return $insights;
    }
}
