<?php

namespace App\Providers;

use App\Models\Event;
use App\Models\Venue;
use App\Policies\EventPolicy;
use App\Policies\VenuePolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Event::class => EventPolicy::class,
        Venue::class => VenuePolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}
