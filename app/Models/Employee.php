<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'department',
        'employee_id_number',
    ];

    /**
     * Get the full name of the employee.
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function committeeAssignments(): HasMany
    {
        return $this->hasMany(Participant::class);
    }
}