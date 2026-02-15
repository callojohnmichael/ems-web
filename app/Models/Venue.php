<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Builder;

class Venue extends Model
{
protected $fillable = [
    'name',
    'address',
    'capacity',
];

public function events(): HasMany
{
    return $this->hasMany(Event::class);
}

public function locations(): HasMany
{
    return $this->hasMany(VenueLocation::class);
}

public function venueBookings(): HasMany
{
    return $this->hasMany(VenueBooking::class);
}

public function campuses(): BelongsToMany
{
    return $this->belongsToMany(Campus::class, 'venue_campus');
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

/**
 * Check if any selected venue locations are already booked in a date range.
 */
public static function hasLocationBookingConflict(
    int $venueId,
    array $locationIds,
    string $startAt,
    string $endAt,
    ?int $excludeEventId = null
): bool {
    if (empty($locationIds)) {
        return false;
    }

    $query = VenueBooking::query()
        ->where('venue_id', $venueId)
        ->whereIn('venue_location_id', $locationIds)
        ->where(function (Builder $q) use ($startAt, $endAt) {
            $q->whereBetween('start_at', [$startAt, $endAt])
                ->orWhereBetween('end_at', [$startAt, $endAt])
                ->orWhere(function (Builder $q2) use ($startAt, $endAt) {
                    $q2->where('start_at', '<', $startAt)
                        ->where('end_at', '>', $endAt);
                });
        })
        ->whereHas('event', function (Builder $q) {
            $q->whereNotIn('status', ['rejected', 'deleted', 'cancelled', 'completed']);
        });

    if ($excludeEventId) {
        $query->where('event_id', '!=', $excludeEventId);
    }

    return $query->exists();
}
}
