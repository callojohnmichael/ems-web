<x-app-layout>
    <div class="space-y-6">
        <div class="flex flex-col items-center justify-center rounded-2xl bg-gradient-to-br from-violet-600 to-purple-700 py-12 text-white">
            <div class="mb-4 flex h-24 w-24 items-center justify-center rounded-full bg-white/20">
                <svg class="h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            </div>
            <h2 class="text-2xl font-bold">Welcome, {{ Auth::user()->name }}!</h2>
            <p class="text-white/90">Participant Dashboard</p>
        </div>

        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                <div class="flex items-center justify-between">
                    <span class="text-sm font-medium text-gray-500">All Events</span>
                    <span class="rounded-lg bg-violet-100 p-2 text-violet-600"><svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg></span>
                </div>
                <p class="mt-2 text-2xl font-bold text-violet-600">0</p>
            </div>
            <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                <div class="flex items-center justify-between">
                    <span class="text-sm font-medium text-gray-500">My Events</span>
                    <span class="rounded-lg bg-green-100 p-2 text-green-600"><svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg></span>
                </div>
                <p class="mt-2 text-2xl font-bold text-green-600">0</p>
            </div>
            <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                <div class="flex items-center justify-between">
                    <span class="text-sm font-medium text-gray-500">Registered</span>
                    <span class="rounded-lg bg-blue-100 p-2 text-blue-600"><svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg></span>
                </div>
                <p class="mt-2 text-2xl font-bold text-blue-600">0</p>
            </div>
            <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                <div class="flex items-center justify-between">
                    <span class="text-sm font-medium text-gray-500">Event Requests</span>
                    <span class="rounded-lg bg-violet-100 p-2 text-violet-600"><svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg></span>
                </div>
                <p class="mt-2 text-2xl font-bold text-violet-600">0</p>
            </div>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <h3 class="text-lg font-semibold text-gray-900">Quick Actions</h3>
            <p class="text-sm text-gray-500">Frequently used actions</p>
            <div class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <a href="{{ route('user.requests') }}" class="flex flex-col items-center justify-center rounded-xl bg-gradient-to-r from-violet-600 to-purple-600 py-8 text-white shadow-md transition hover:from-violet-700 hover:to-purple-700">
                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span class="mt-2 font-medium">Request Event</span>
                </a>
                <a href="{{ route('calendar.index') }}" class="flex flex-col items-center justify-center rounded-xl border border-gray-200 bg-white py-8 text-gray-700 shadow-sm transition hover:bg-gray-50">
                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <span class="mt-2 font-medium">View Calendar</span>
                </a>
                <a href="{{ route('notifications.index') }}" class="flex flex-col items-center justify-center rounded-xl border border-gray-200 bg-white py-8 text-gray-700 shadow-sm transition hover:bg-gray-50">
                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                    <span class="mt-2 font-medium">Notifications</span>
                </a>
                <a href="{{ route('events.index') }}" class="flex flex-col items-center justify-center rounded-xl border border-gray-200 bg-white py-8 text-gray-700 shadow-sm transition hover:bg-gray-50">
                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span class="mt-2 font-medium">Reports</span>
                </a>
            </div>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <h3 class="text-lg font-semibold text-gray-900">Recent Activity</h3>
            <p class="mt-1 text-sm text-gray-500">Your latest actions and updates</p>
            <div class="mt-4 text-sm text-gray-500">No recent activity yet.</div>
        </div>
    </div>
</x-app-layout>
