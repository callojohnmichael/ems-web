<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Resource extends Model
{
    protected $fillable = [
        'name',
        'type',
        'quantity',
        'price', // Added price
    ];

    /**
     * Ensure price is always treated as a decimal with 2 points.
     */
    protected $casts = [
        'price' => 'decimal:2',
    ];

    public function resourceAllocations(): HasMany
    {
        return $this->hasMany(ResourceAllocation::class);
    }
}