<aside class="fixed left-0 top-0 z-40 h-screen w-64 border-r border-gray-200 bg-white shadow-sm" x-data="{ collapsed: false }">
    <div class="flex h-full flex-col">
        <div class="flex h-16 items-center gap-2 border-b border-gray-100 px-4">
            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-gradient-to-br from-violet-600 to-purple-700 text-white">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
            </div>
            <div class="min-w-0 flex-1">
                <p class="truncate font-semibold text-gray-900">Event Manager</p>
                <p class="truncate text-xs text-gray-500">School Events</p>
            </div>
        </div>

        <nav class="flex-1 space-y-0.5 overflow-y-auto p-3" data-menu-search-nav>
            <div class="mb-2">
                <label for="sidebar-menu-search" class="sr-only">Search Menu</label>
                <div class="relative">
                    <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35m1.85-5.15a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </span>
                    <input id="sidebar-menu-search" type="text" data-menu-search-input placeholder="Search menu..." class="w-full rounded-lg border border-gray-200 bg-white py-2 pl-9 pr-3 text-sm text-gray-700 focus:border-violet-400 focus:outline-none focus:ring-2 focus:ring-violet-200">
                </div>
            </div>

            <a href="{{ route(Auth::user()->dashboardRoute()) }}" data-menu-label="dashboard home" class="menu-search-item flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium {{ request()->routeIs('admin.dashboard') || request()->routeIs('user.dashboard') || request()->routeIs('media.dashboard') ? 'bg-gradient-to-r from-violet-600 to-purple-600 text-white' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                Dashboard
            </a>

            <!-- @role('admin') -->
            <!-- <a href="{{ route('admin.approvals') }}" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium {{ request()->routeIs('admin.approvals') ? 'bg-gradient-to-r from-violet-600 to-purple-600 text-white' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}"> -->
                <!-- <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> -->
                <!-- Approvals -->
            <!-- </a> -->
            <!-- @endrole -->

            <a href="{{ route('calendar.index') }}" data-menu-label="calendar schedule" class="menu-search-item flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium {{ request()->routeIs('calendar.*') ? 'bg-gradient-to-r from-violet-600 to-purple-600 text-white' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                Calendar
            </a>

            <div
                data-menu-label="event management events program flow event check-in participants attendance"
                class="menu-search-item rounded-xl bg-gray-100 p-1.5"
                x-data="{ eventOpen: {{ (request()->routeIs('events.*') || request()->routeIs('program-flow.*') || request()->routeIs('checkin.*') || request()->routeIs('admin.participants.*') || request()->routeIs('admin.events.participants.*') || request()->routeIs('admin.attendance.*')) ? 'true' : 'false' }} }"
            >
                <button
                    type="button"
                    @click="eventOpen = !eventOpen"
                    class="flex w-full items-center justify-between rounded-lg px-3 py-2.5 text-sm font-medium {{ (request()->routeIs('events.*') || request()->routeIs('program-flow.*') || request()->routeIs('checkin.*') || request()->routeIs('admin.participants.*') || request()->routeIs('admin.events.participants.*') || request()->routeIs('admin.attendance.*')) ? 'bg-gradient-to-r from-violet-600 to-purple-600 text-white' : 'text-gray-800 hover:bg-gray-200' }}"
                >
                    <span class="flex items-center gap-3">
                        <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        Event Management
                    </span>
                    <svg class="h-4 w-4 transition-transform duration-200" :class="{ 'rotate-180': eventOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="eventOpen" x-transition class="mt-2 pl-3" style="display: none;">
                    <div class="space-y-1.5 border-l-2 border-gray-300 pl-3">
                        <a href="{{ route('events.index') }}" class="block rounded-lg px-3 py-2 text-sm {{ request()->routeIs('events.*') ? 'bg-gray-200 font-medium text-gray-900' : 'text-gray-700 hover:bg-gray-200' }}">
                            Events
                        </a>
                        <a href="{{ route('program-flow.index') }}" class="block rounded-lg px-3 py-2 text-sm {{ request()->routeIs('program-flow.*') ? 'bg-gray-200 font-medium text-gray-900' : 'text-gray-700 hover:bg-gray-200' }}">
                            Program Flow
                        </a>
                        @can('event check-in access')
                            <a href="{{ route('checkin.index') }}" class="block rounded-lg px-3 py-2 text-sm {{ request()->routeIs('checkin.*') ? 'bg-gray-200 font-medium text-gray-900' : 'text-gray-700 hover:bg-gray-200' }}">
                                Event Check-In
                            </a>
                        @endcan
                        @role('admin')
                            <a href="{{ route('admin.participants.index') }}" class="block rounded-lg px-3 py-2 text-sm {{ (request()->routeIs('admin.participants.*') || request()->routeIs('admin.events.participants.*')) ? 'bg-gray-200 font-medium text-gray-900' : 'text-gray-700 hover:bg-gray-200' }}">
                                Participants
                            </a>
                            @can('manage participants')
                                <a href="{{ route('admin.attendance.index') }}" class="block rounded-lg px-3 py-2 text-sm {{ request()->routeIs('admin.attendance.*') ? 'bg-gray-200 font-medium text-gray-900' : 'text-gray-700 hover:bg-gray-200' }}">
                                    Attendance
                                </a>
                            @endcan
                        @endrole
                    </div>
                </div>
            </div>

            @can('view multimedia')
            <a href="{{ route('multimedia.index') }}" data-menu-label="multimedia posts media" class="menu-search-item flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium {{ request()->routeIs('multimedia.*') ? 'bg-gradient-to-r from-violet-600 to-purple-600 text-white' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                Multimedia
            </a>
            @endcan
            @role('admin')
            <a href="{{ route('admin.venues.index') }}" data-menu-label="venues locations" class="menu-search-item flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium {{ request()->routeIs('admin.venues.*') ? 'bg-gradient-to-r from-violet-600 to-purple-600 text-white' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                Venues
            </a>
            <a href="{{ route('admin.documents.index') }}" data-menu-label="documents files" class="menu-search-item flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium {{ request()->routeIs('admin.documents.*') ? 'bg-gradient-to-r from-violet-600 to-purple-600 text-white' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Documents
            </a>
            <a href="{{ route('notifications.index') }}" data-menu-label="notifications alerts inbox" class="menu-search-item flex items-center justify-between gap-3 rounded-lg px-3 py-2.5 text-sm font-medium {{ request()->routeIs('notifications.*') ? 'bg-gradient-to-r from-violet-600 to-purple-600 text-white' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                <span class="flex items-center gap-3">
                <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                Notifications
                </span>
                <span data-notification-badge class="{{ ($layoutNotificationData['unread_count'] ?? 0) > 0 ? '' : 'hidden ' }}shrink-0 rounded-full bg-red-500 px-2 py-0.5 text-xs font-semibold text-white">
                    {{ $layoutNotificationData['unread_count'] ?? 0 }}
                </span>
            </a>
            <a href="{{ route('support.index') }}" data-menu-label="help support tickets" class="menu-search-item flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium {{ request()->routeIs('support.*') ? 'bg-gradient-to-r from-violet-600 to-purple-600 text-white' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                Help Support
            </a>
            @can('view reports')
            <div
                data-menu-label="reports analytics overview pipeline participants venues finance engagement support"
                class="menu-search-item rounded-xl bg-gray-100 p-1.5"
                x-data="{ reportsOpen: {{ request()->routeIs('reports.*') ? 'true' : 'false' }} }"
            >
                <button
                    type="button"
                    @click="reportsOpen = !reportsOpen"
                    class="flex w-full items-center justify-between rounded-lg px-3 py-2.5 text-sm font-medium {{ request()->routeIs('reports.*') ? 'bg-gradient-to-r from-violet-600 to-purple-600 text-white' : 'text-gray-800 hover:bg-gray-200' }}"
                >
                    <span class="flex items-center gap-3">
                        <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Reports
                    </span>
                    <svg class="h-4 w-4 transition-transform duration-200" :class="{ 'rotate-180': reportsOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="reportsOpen" x-transition class="mt-2 pl-3" style="display: none;">
                    <div class="space-y-1.5 border-l-2 border-gray-300 pl-3">
                        <a href="{{ route('reports.index') }}" class="block rounded-lg px-3 py-2 text-sm {{ request()->routeIs('reports.index') ? 'bg-gray-200 font-medium text-gray-900' : 'text-gray-700 hover:bg-gray-200' }}">Overview</a>
                        <a href="{{ route('reports.pipeline') }}" class="block rounded-lg px-3 py-2 text-sm {{ request()->routeIs('reports.pipeline') ? 'bg-gray-200 font-medium text-gray-900' : 'text-gray-700 hover:bg-gray-200' }}">Pipeline</a>
                        <a href="{{ route('reports.participants') }}" class="block rounded-lg px-3 py-2 text-sm {{ request()->routeIs('reports.participants') ? 'bg-gray-200 font-medium text-gray-900' : 'text-gray-700 hover:bg-gray-200' }}">Participants</a>
                        <a href="{{ route('reports.venues') }}" class="block rounded-lg px-3 py-2 text-sm {{ request()->routeIs('reports.venues') ? 'bg-gray-200 font-medium text-gray-900' : 'text-gray-700 hover:bg-gray-200' }}">Venues</a>
                        <a href="{{ route('reports.finance') }}" class="block rounded-lg px-3 py-2 text-sm {{ request()->routeIs('reports.finance') ? 'bg-gray-200 font-medium text-gray-900' : 'text-gray-700 hover:bg-gray-200' }}">Finance</a>
                        <a href="{{ route('reports.engagement') }}" class="block rounded-lg px-3 py-2 text-sm {{ request()->routeIs('reports.engagement') ? 'bg-gray-200 font-medium text-gray-900' : 'text-gray-700 hover:bg-gray-200' }}">Engagement</a>
                        <a href="{{ route('reports.support') }}" class="block rounded-lg px-3 py-2 text-sm {{ request()->routeIs('reports.support') ? 'bg-gray-200 font-medium text-gray-900' : 'text-gray-700 hover:bg-gray-200' }}">Support</a>
                    </div>
                </div>
            </div>
            @endcan
            <div
                data-menu-label="account management users roles permissions"
                class="menu-search-item rounded-xl bg-gray-100 p-1.5"
                x-data="{ accountOpen: {{ (request()->routeIs('admin.roles.*') || request()->routeIs('admin.users.*')) ? 'true' : 'false' }} }"
            >
                <button
                    type="button"
                    @click="accountOpen = !accountOpen"
                    class="flex w-full items-center justify-between rounded-lg px-3 py-2.5 text-sm font-medium text-gray-800 hover:bg-gray-200"
                >
                    <span class="flex items-center gap-3">
                        <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        Account Management
                    </span>
                    <svg class="h-4 w-4 text-gray-700 transition-transform duration-200" :class="{ 'rotate-180': accountOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                <div x-show="accountOpen" x-transition class="mt-2 pl-3" style="display: none;">
                    <div class="space-y-1.5 border-l-2 border-gray-300 pl-3">
                        <a href="{{ route('admin.users.index') }}" class="block rounded-lg px-3 py-2 text-sm {{ request()->routeIs('admin.users.*') ? 'bg-gray-200 font-medium text-gray-900' : 'text-gray-700 hover:bg-gray-200' }}">
                            Users
                        </a>
                        <a href="{{ route('admin.roles.index') }}" class="block rounded-lg px-3 py-2 text-sm {{ request()->routeIs('admin.roles.index') || request()->routeIs('admin.roles.create-role') || request()->routeIs('admin.roles.edit-role') ? 'bg-gray-200 font-medium text-gray-900' : 'text-gray-700 hover:bg-gray-200' }}">
                            Roles
                        </a>
                        <a href="{{ route('admin.roles.permissions.index') }}" class="block rounded-lg px-3 py-2 text-sm {{ request()->routeIs('admin.roles.permissions.*') || request()->routeIs('admin.roles.create-permission') ? 'bg-gray-200 font-medium text-gray-900' : 'text-gray-700 hover:bg-gray-200' }}">
                            Permissions
                        </a>
                    </div>
                </div>
            </div>
            @endrole

            <div data-menu-search-empty class="hidden rounded-lg border border-dashed border-gray-300 px-4 py-6 text-center">
                <div class="mx-auto mb-2 flex h-10 w-10 items-center justify-center rounded-full bg-gray-100 text-gray-500">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 9.172a4 4 0 015.656 0M9.172 9.172L5.636 5.636m3.536 3.536l-3.536 3.536m9.9-3.536l3.536-3.536m-3.536 3.536l3.536 3.536M7 17h10"/></svg>
                </div>
                <p class="text-sm font-medium text-gray-700">No menu matched</p>
                <p class="text-xs text-gray-500">Try another keyword.</p>
            </div>
        </nav>

        <div class="border-t border-gray-100 p-3">
            <a href="{{ route('profile.edit') }}" class="mb-2 flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium text-gray-600 hover:bg-gray-100 hover:text-gray-900">
                <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                Profile
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="flex w-full items-center justify-center gap-2 rounded-lg bg-gradient-to-r from-violet-600 to-purple-600 px-3 py-2.5 text-sm font-medium text-white hover:from-violet-700 hover:to-purple-700">
                    Log out
                </button>
            </form>
            <p class="mt-3 text-center text-xs text-gray-400">Version 1.0.0</p>
            <p class="text-center text-xs text-gray-400">© {{ date('Y') }} Event Manager</p>
        </div>
    </div>
</aside>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const nav = document.querySelector('[data-menu-search-nav]');
        if (!nav) return;

        const input = nav.querySelector('[data-menu-search-input]');
        const items = nav.querySelectorAll('.menu-search-item');
        const emptyState = nav.querySelector('[data-menu-search-empty]');

        if (!input || !emptyState || items.length === 0) return;

        input.addEventListener('input', function (event) {
            const query = String(event.target.value || '').trim().toLowerCase();
            let visibleCount = 0;

            items.forEach(function (item) {
                const label = String(item.getAttribute('data-menu-label') || '').toLowerCase();
                const visible = query.length === 0 || label.includes(query);
                item.style.display = visible ? '' : 'none';
                if (visible) {
                    visibleCount += 1;
                }
            });

            emptyState.classList.toggle('hidden', visibleCount > 0);
        });
    });
</script>
