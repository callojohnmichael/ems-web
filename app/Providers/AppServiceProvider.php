<?php

namespace App\Providers;

use App\Services\MenuAccessService;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('layouts.app', function ($view): void {
            $user = auth()->user();

            if (! $user) {
                return;
            }

            $latestNotifications = $user->notifications()
                ->latest()
                ->take((int) config('ems_notifications.dropdown_limit', 5))
                ->get()
                ->map(function ($notification) {
                    $category = data_get($notification->data, 'category', 'system');

                    if (! array_key_exists($category, config('ems_notifications.categories', []))) {
                        $category = 'system';
                    }

                    return [
                        'id' => $notification->id,
                        'title' => data_get($notification->data, 'title', 'Notification'),
                        'message' => data_get($notification->data, 'message', 'You have a new notification.'),
                        'url' => data_get($notification->data, 'url', route('notifications.index')),
                        'category' => $category,
                        'category_label' => config('ems_notifications.categories.' . $category, 'System'),
                        'read_at' => $notification->read_at,
                        'created_at_human' => $notification->created_at?->diffForHumans(),
                    ];
                });

            $view->with('layoutNotificationData', [
                'unread_count' => $user->unreadNotifications()->count(),
                'latest' => $latestNotifications,
                'poll_seconds' => (int) config('ems_notifications.poll_interval_seconds', 10),
            ]);

            $view->with('layoutMenuVisibility', app(MenuAccessService::class)->getVisibilityMapForUser($user));
        });
    }
}
