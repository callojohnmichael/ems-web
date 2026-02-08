<x-app-layout>
    <div class="space-y-6">
        <div class="flex flex-col items-center justify-center rounded-2xl bg-gradient-to-br from-violet-600 to-purple-700 py-12 text-white">
            <div class="mb-4 flex h-24 w-24 items-center justify-center rounded-full bg-white/20">
                <svg class="h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
            </div>
            <h2 class="text-2xl font-bold">Welcome, {{ Auth::user()->name }}!</h2>
            <p class="text-white/90">Admin Dashboard</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <h3 class="text-lg font-semibold text-gray-900">Quick links</h3>
            <div class="mt-4 flex flex-wrap gap-3">
                <a href="{{ route('admin.approvals') }}" class="rounded-lg bg-gradient-to-r from-violet-600 to-purple-600 px-4 py-2 text-sm font-medium text-white hover:from-violet-700 hover:to-purple-700">Approvals</a>
                <a href="{{ route('admin.roles.index') }}" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Roles & Permissions</a>
                <a href="{{ route('admin.venues.index') }}" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Venues</a>
                <a href="{{ route('admin.reports.index') }}" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Reports</a>
            </div>
        </div>
    </div>
</x-app-layout>
