<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Participant extends Model
{
    protected $fillable = [
        'event_id',
        'employee_id',
        'user_id',
        'name',
        'email',
        'phone',
        'role',
        'type',
        'status',
        'registered_at',
    ];

    protected $casts = [
        'registered_at' => 'datetime',
    ];

    /* ---------------- RELATIONSHIPS ---------------- */

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    /* ---------------- SMART DISPLAY ACCESSORS ---------------- */

    public function getDisplayNameAttribute(): string
    {
        if ($this->relationLoaded('user') && $this->user) {
            return $this->user->name;
        }

        if ($this->relationLoaded('employee') && $this->employee) {
            return $this->employee->full_name ?? $this->employee->name;
        }

        return $this->name ?? 'N/A';
    }

    public function getDisplayEmailAttribute(): string
    {
        return $this->user->email
            ?? $this->employee->email
            ?? $this->email
            ?? 'N/A';
    }
}
