<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Venue;

class VenuePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'user', 'multimedia_staff']);
    }

    public function view(User $user, Venue $venue): bool
    {
        return $user->hasAnyRole(['admin', 'user', 'multimedia_staff']);
    }

    public function create(User $user): bool
    {
        return $user->hasRole('admin'); // Only admins can create venues
    }

    public function update(User $user, Venue $venue): bool
    {
        return $user->hasRole('admin'); // Only admins can update
    }

    public function delete(User $user, Venue $venue): bool
    {
        return $user->hasRole('admin'); // Only admins can delete
    }
}
