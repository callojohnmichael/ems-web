<x-app-layout>
    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
        <h2 class="text-lg font-semibold text-gray-900">Program Flow - Published Events</h2>
        <p class="mt-1 text-sm text-gray-500">Manage program flow items for published events.</p>

        <div class="mt-6 space-y-4">
            @forelse($events as $event)
                <div class="border rounded-md p-4 flex items-center justify-between">
                    <div>
                        <a href="{{ route('program-flow.show', $event) }}" class="text-blue-600 hover:underline font-medium">{{ $event->title }}</a>
                        <div class="text-sm text-gray-500">{{ optional($event->start_at)->format('M d, Y H:i') ?? '-' }} &mdash; {{ optional($event->end_at)->format('M d, Y H:i') ?? '-' }}</div>
                    </div>

                    <div class="flex items-center space-x-3">
                        @can('manage scheduling')
                            <a href="{{ route('program-flow.show', $event) }}" class="px-3 py-1 bg-indigo-600 text-white rounded-md text-sm">Manage</a>
                        @else
                            <a href="{{ route('program-flow.show', $event) }}" class="px-3 py-1 border border-gray-200 rounded-md text-sm">View</a>
                        @endcan
                    </div>
                </div>
            @empty
                <div class="text-sm text-gray-600">No published events found.</div>
            @endforelse
        </div>
    </div>
</x-app-layout>
