<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Venue extends Model
{
    protected $fillable = [
        'name',
        'address',
        'capacity',
        'facilities',
    ];

    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }

    public function venueBookings(): HasMany
    {
        return $this->hasMany(VenueBooking::class);
    }

    /**
     * Check if a venue is available for the given date range.
     * Optionally exclude a specific event from the check (for updates).
     */
    public static function checkVenueAvailability($venueId, $startAt, $endAt, $excludeEventId = null): bool
    {
        $query = Event::where('venue_id', $venueId)
            ->whereNotIn('status', ['deleted', 'rejected'])
            ->where(function ($q) use ($startAt, $endAt) {
                $q->whereBetween('start_at', [$startAt, $endAt])
                  ->orWhereBetween('end_at', [$startAt, $endAt])
                  ->orWhere(function ($subQ) use ($startAt, $endAt) {
                      $subQ->where('start_at', '<=', $startAt)
                           ->where('end_at', '>=', $endAt);
                  });
            });

        if ($excludeEventId) {
            $query->where('id', '!=', $excludeEventId);
        }

        return $query->exists();
    }
}
