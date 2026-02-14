<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MenuAccessAudit extends Model
{
    protected $fillable = [
        'actor_user_id',
        'target_type',
        'target_id',
        'menu_key',
        'previous_value',
        'new_value',
        'action',
        'meta',
    ];

    protected function casts(): array
    {
        return [
            'previous_value' => 'boolean',
            'new_value' => 'boolean',
            'meta' => 'array',
        ];
    }

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_user_id');
    }
}
