<x-app-layout>
    <div class="space-y-6">
        <div class="flex flex-col items-center justify-center rounded-2xl bg-gradient-to-br from-violet-600 to-purple-700 py-12 text-white">
            <div class="mb-4 flex h-24 w-24 items-center justify-center rounded-full bg-white/20">
                <svg class="h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14"/></svg>
            </div>
            <h2 class="text-2xl font-bold">Welcome, {{ Auth::user()->name }}!</h2>
            <p class="text-white/90">Multimedia Staff Dashboard</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <h3 class="text-lg font-semibold text-gray-900">Quick links</h3>
            <div class="mt-4 flex flex-wrap gap-3">
                <a href="{{ route('media.posts') }}" class="rounded-lg bg-gradient-to-r from-violet-600 to-purple-600 px-4 py-2 text-sm font-medium text-white hover:from-violet-700 hover:to-purple-700">Handle posts</a>
                <a href="{{ route('calendar.index') }}" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Calendar</a>
                <a href="{{ route('program-flow.index') }}" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Program Flow</a>
            </div>
        </div>
    </div>
</x-app-layout>
