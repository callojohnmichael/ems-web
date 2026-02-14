<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PostMedia extends Model
{
    protected $fillable = [
        'event_post_id',
        'type',
        'path',
        'thumbnail_path',
        'source',
    ];

    public function post(): BelongsTo
    {
        return $this->belongsTo(EventPost::class, 'event_post_id');
    }
}
