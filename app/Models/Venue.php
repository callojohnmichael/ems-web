<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Venue extends Model
{
    protected $fillable = [
        'name',
        'address',
        'capacity',
        'facilities',
    ];

    public function venueBookings(): HasMany
    {
        return $this->hasMany(VenueBooking::class);
    }


}
