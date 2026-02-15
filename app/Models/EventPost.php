<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventPost extends Model
{
    protected $fillable = [
        'event_id',
        'user_id',
        'type',
        'status',
        'caption',
        'ai_prompt',
        'ai_generated_content',
        'scheduled_at',
    ];

    protected $appends = ['parsed_caption'];

    private const CAPTION_TRUNCATE_LENGTH = 280;

    private const STORY_ELEMENTS_MARKERS = [
        '✨ Story elements:',
        'Story elements:',
        'Key points:',
    ];

    /**
     * Parse caption into summary, optional story elements list, and full text for AI narratives.
     *
     * @return array{summary: string, story_elements: array<int, string>, has_narrative: bool, full: string}
     */
    public function getParsedCaptionAttribute(): array
    {
        $caption = $this->caption ?? '';
        if ($caption === '') {
            return [
                'summary' => '',
                'story_elements' => [],
                'has_narrative' => false,
                'full' => '',
            ];
        }

        $storyElements = [];
        $summary = $caption;
        $marker = null;

        foreach (self::STORY_ELEMENTS_MARKERS as $m) {
            if (stripos($caption, $m) !== false) {
                $marker = $m;
                break;
            }
        }

        if ($marker !== null) {
            $parts = preg_split('/\s*' . preg_quote($marker, '/') . '\s*/iu', $caption, 2);
            $summary = trim($parts[0] ?? '');
            $rest = trim($parts[1] ?? '');
            if ($rest !== '') {
                $storyElements = array_filter(
                    array_map('trim', preg_split('/[,;]|\n/', $rest)),
                    fn (string $s): bool => $s !== ''
                );
                $storyElements = array_values($storyElements);
            }
        }

        return [
            'summary' => $summary,
            'story_elements' => $storyElements,
            'has_narrative' => $this->ai_generated_content && (count($storyElements) > 0 || strlen($caption) > self::CAPTION_TRUNCATE_LENGTH),
            'full' => $caption,
        ];
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function media()
    {
        return $this->hasMany(PostMedia::class, 'event_post_id');
    }

    public function reactions()
    {
        return $this->hasMany(PostReaction::class, 'event_post_id');
    }

    public function comments()
    {
        return $this->hasMany(PostComment::class, 'event_post_id');
    }
}
