<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Budget extends Model
{
    protected $fillable = [
        'event_id',
        'total_budget',
        'currency',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }
}
