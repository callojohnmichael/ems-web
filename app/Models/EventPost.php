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
        'scheduled_at',
    ];

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
