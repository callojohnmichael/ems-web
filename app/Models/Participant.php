<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Participant extends Model
{
protected $fillable = [
    'event_id',
    'employee_id',  // added
    'role',         // added
    'type',         // added (e.g., 'committee')
    'user_id',
    'name',
    'email',
    'phone',
    'status',
    'registered_at',
];


    protected function casts(): array
    {
        return [
            'registered_at' => 'datetime',
        ];
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function employee(): BelongsTo
    {
        // Ensure 'employee_id' is the actual foreign key name in your participants table
        return $this->belongsTo(Employee::class, 'employee_id');
    }
}
