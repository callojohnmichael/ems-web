<?php

namespace App\Services;

use App\Models\EventPost;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MultimediaAnalyticsService
{
    /**
     * Summary stats for multimedia posts in the given date range (by event start_at).
     *
     * @return array{total_posts: int, posts_with_ai: int, media_images: int, media_videos: int, top_event_name: string|null, top_event_posts: int}
     */
    public function summary(Carbon $from, Carbon $to): array
    {
        $postsQuery = EventPost::query()
            ->join('events', 'events.id', '=', 'event_posts.event_id')
            ->whereBetween('events.start_at', [$from->copy()->startOfDay(), $to->copy()->endOfDay()]);

        $totalPosts = (clone $postsQuery)->count();
        $postsWithAi = (clone $postsQuery)->where('event_posts.ai_generated_content', true)->count();

        $topRow = (clone $postsQuery)
            ->select('events.title', DB::raw('COUNT(event_posts.id) as total_posts'))
            ->groupBy('events.title')
            ->orderByDesc('total_posts')
            ->first();
        $topEventName = $topRow?->title;
        $topEventPosts = (int) ($topRow?->total_posts ?? 0);

        $mediaImages = 0;
        $mediaVideos = 0;
        if (Schema::hasTable('post_media')) {
            $mediaRows = DB::table('post_media')
                ->join('event_posts', 'event_posts.id', '=', 'post_media.event_post_id')
                ->join('events', 'events.id', '=', 'event_posts.event_id')
                ->whereBetween('events.start_at', [$from->copy()->startOfDay(), $to->copy()->endOfDay()])
                ->select('post_media.type', DB::raw('COUNT(*) as total'))
                ->groupBy('post_media.type')
                ->get();
            foreach ($mediaRows as $row) {
                if (strtolower($row->type ?? '') === 'image') {
                    $mediaImages = (int) $row->total;
                } elseif (strtolower($row->type ?? '') === 'video') {
                    $mediaVideos = (int) $row->total;
                }
            }
        }

        return [
            'total_posts' => $totalPosts,
            'posts_with_ai' => $postsWithAi,
            'media_images' => $mediaImages,
            'media_videos' => $mediaVideos,
            'top_event_name' => $topEventName,
            'top_event_posts' => $topEventPosts,
        ];
    }
}
