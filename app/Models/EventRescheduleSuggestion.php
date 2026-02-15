<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventRescheduleSuggestion extends Model
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_ACCEPTED = 'accepted';
    public const STATUS_DECLINED = 'declined';

    protected $fillable = [
        'event_id',
        'suggested_start_at',
        'suggested_end_at',
        'reason',
        'requested_by',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'suggested_start_at' => 'datetime',
            'suggested_end_at' => 'datetime',
        ];
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function requestedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }
}
