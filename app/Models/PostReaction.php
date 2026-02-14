<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PostReaction extends Model
{
    protected $fillable = [
        'event_post_id',
        'user_id',
        'type',
    ];

    public function post(): BelongsTo
    {
        return $this->belongsTo(EventPost::class, 'event_post_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
