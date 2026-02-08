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
    ];

    public function resourceAllocations(): HasMany
    {
        return $this->hasMany(ResourceAllocation::class);
    }
}
