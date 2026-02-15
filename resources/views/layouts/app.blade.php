<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
        <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
        <title>@yield('title', config('app.name', 'Event Manager'))</title>
        @stack('meta')
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @stack('styles')
    </head>

    <body class="min-h-screen bg-gray-50 font-sans antialiased">
        @auth
            @if(!($hideSidebar ?? false))
                @include('layouts.partials.sidebar')
            @endif
            @stack('scripts')

            <div class="{{ ($hideSidebar ?? false) ? '' : 'pl-64' }}">
                <header class="sticky top-0 z-30 border-b border-gray-200 bg-white/95 backdrop-blur">
                    <div class="flex h-16 items-center justify-between px-6">
                        <div>
                            <h1 class="text-xl font-bold text-gray-900">Welcome back, {{ Auth::user()->name }}!</h1>
                            <p class="text-sm text-gray-500">Here's what's happening with your school events today.</p>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="relative" x-data="{ open: false }">
                                <button
                                    type="button"
                                    @click="open = !open"
                                    class="relative rounded-lg p-2 text-gray-600 hover:bg-gray-100 hover:text-gray-900"
                                    aria-label="Open notifications"
                                >
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                    </svg>
                                    <span data-notification-badge class="{{ ($layoutNotificationData['unread_count'] ?? 0) > 0 ? '' : 'hidden ' }}absolute -right-1 -top-1 rounded-full bg-red-500 px-1.5 py-0.5 text-xs font-semibold leading-none text-white">
                                        {{ $layoutNotificationData['unread_count'] ?? 0 }}
                                    </span>
                                </button>

                                <div
                                    x-show="open"
                                    @click.outside="open = false"
                                    x-transition
                                    class="absolute right-0 z-40 mt-2 w-80 overflow-hidden rounded-lg border border-gray-200 bg-white shadow-lg"
                                    style="display: none;"
                                >
                                    <div class="flex items-center justify-between border-b border-gray-100 px-4 py-3">
                                        <h3 class="text-sm font-semibold text-gray-900">Notifications</h3>
                                        <a href="{{ route('notifications.index') }}" class="text-xs font-medium text-violet-700 hover:text-violet-800">View all</a>
                                    </div>
                                    <div data-notification-dropdown-list class="max-h-96 overflow-y-auto divide-y divide-gray-100">
                                        @forelse(($layoutNotificationData['latest'] ?? collect()) as $item)
                                            <a href="{{ $item['url'] }}" class="block px-4 py-3 hover:bg-gray-50">
                                                <p class="text-sm font-medium text-gray-900">{{ $item['title'] }}</p>
                                                <p class="mt-1 line-clamp-2 text-xs text-gray-600">{{ $item['message'] }}</p>
                                                <p class="mt-1 text-xs text-gray-400">{{ $item['created_at_human'] }}</p>
                                            </a>
                                        @empty
                                            <p class="px-4 py-6 text-center text-sm text-gray-500">No notifications yet.</p>
                                        @endforelse
                                    </div>
                                </div>
                            </div>

                            <div class="relative" x-data="{ open: false }">
                                <button
                                    type="button"
                                    @click="open = !open"
                                    class="flex items-center gap-2 rounded-lg px-2 py-1.5 hover:bg-gray-100"
                                >
                                    <div class="flex h-9 w-9 items-center justify-center rounded-full bg-gray-200 text-gray-600">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                    </div>
                                    <span class="font-medium text-gray-700">{{ Auth::user()->name }}</span>
                                    <span class="rounded-full bg-gradient-to-r from-violet-600 to-purple-600 px-3 py-0.5 text-xs font-medium text-white">
                                        {{ ucfirst(str_replace('_', ' ', Auth::user()->getRoleNames()->first() ?? 'user')) }}
                                    </span>
                                    <svg class="h-4 w-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>

                                <div
                                    x-show="open"
                                    @click.outside="open = false"
                                    x-transition
                                    class="absolute right-0 z-40 mt-2 w-48 overflow-hidden rounded-lg border border-gray-200 bg-white shadow-lg"
                                    style="display: none;"
                                >
                                    <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-4 py-3 text-sm font-medium text-gray-800 hover:bg-gray-50">
                                        <i class="bi bi-person text-xl leading-none text-gray-900"></i>
                                        Profile
                                    </a>

                                    <form method="POST" action="{{ route('logout') }}" class="border-t border-gray-100">
                                        @csrf
                                        <button type="submit" class="flex w-full items-center gap-2 px-4 py-2.5 text-left text-sm text-red-600 hover:bg-red-50">
                                            <i class="bi bi-box-arrow-right text-base leading-none"></i>
                                            Log out
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </header>

                {{-- ✅ THIS IS THE MISSING PART --}}
                @isset($header)
                    <div class="border-b border-gray-200 bg-white">
                        <div class="px-6 py-5">
                            {{ $header }}
                        </div>
                    </div>
                @endisset

                <main class="p-6">
                    @if(session('status'))
                        <div class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    {{-- The $slot variable captures content from <x-app-layout> --}}
                    {{ $slot }}

                    @if(session('success'))
                        <div class="fixed bottom-6 right-6 z-50 max-w-sm rounded-lg border border-green-200 bg-green-400 p-4 shadow-lg" role="alert">
                            <p class="font-semibold text-green-900">Success!</p>
                            <p class="mt-1 text-sm text-gray-600">{{ session('success') }}</p>
                        </div>
                    @endif
                </main>
            </div>
        @else
            <div class="min-h-screen bg-gray-100">
                @include('layouts.navigation')

                @isset($header)
                    <header class="bg-white shadow">
                        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                            {{ $header }}
                        </div>
                    </header>
                @endisset

                <main>
                    {{ $slot }}
                </main>
            </div>
        @endauth

        @auth
            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    const pollMs = Number({{ (int) ($layoutNotificationData['poll_seconds'] ?? 10) }}) * 1000;
                    const dropdownList = document.querySelector('[data-notification-dropdown-list]');

                    const renderDropdown = (items) => {
                        if (!dropdownList) {
                            return;
                        }

                        if (!items.length) {
                            dropdownList.innerHTML = '<p class=\"px-4 py-6 text-center text-sm text-gray-500\">No notifications yet.</p>';
                            return;
                        }

                        const escapeHtml = (value) => String(value ?? '')
                            .replaceAll('&', '&amp;')
                            .replaceAll('<', '&lt;')
                            .replaceAll('>', '&gt;')
                            .replaceAll('\"', '&quot;')
                            .replaceAll(\"'\", '&#039;');

                        dropdownList.innerHTML = items.map((item) => {
                            const title = escapeHtml(item.title ?? 'Notification');
                            const message = escapeHtml(item.message ?? 'You have a new notification.');
                            const createdAt = escapeHtml(item.created_at_human ?? '');
                            const url = escapeHtml(item.url ?? '{{ route('notifications.index') }}');

                            return `<a href=\"${url}\" class=\"block px-4 py-3 hover:bg-gray-50\">
                                <p class=\"text-sm font-medium text-gray-900\">${title}</p>
                                <p class=\"mt-1 line-clamp-2 text-xs text-gray-600\">${message}</p>
                                <p class=\"mt-1 text-xs text-gray-400\">${createdAt}</p>
                            </a>`;
                        }).join('');
                    };

                    const syncUnreadCount = (count) => {
                        document.querySelectorAll('[data-notification-badge]').forEach((badge) => {
                            badge.textContent = count;
                            badge.classList.toggle('hidden', Number(count) === 0);
                        });

                        document.querySelectorAll('.js-unread-count').forEach((el) => {
                            el.textContent = count;
                        });
                    };

                    const refreshFeed = async () => {
                        const response = await fetch('{{ route('notifications.feed') }}', {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json',
                            },
                        });

                        if (!response.ok) {
                            return;
                        }

                        const payload = await response.json();
                        syncUnreadCount(payload.unread_count ?? 0);
                        renderDropdown(payload.latest ?? []);
                    };

                    document.addEventListener('notifications:updated', (event) => {
                        const detail = event.detail ?? {};

                        if (detail.unread_count !== undefined) {
                            syncUnreadCount(detail.unread_count);
                        }

                        if (Array.isArray(detail.latest)) {
                            renderDropdown(detail.latest);
                        }
                    });

                    setInterval(refreshFeed, pollMs);
                });
            </script>
        @endauth
    </body>
</html>
