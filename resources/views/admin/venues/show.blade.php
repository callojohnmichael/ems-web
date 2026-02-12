<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $venue->name }}
            </h2>

            <div class="flex gap-2">
                <a href="{{ route('admin.venues.edit', $venue) }}"
                   class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 transition">
                    Edit
                </a>

                <a href="{{ route('admin.venues.index') }}"
                   class="inline-flex items-center px-4 py-2 bg-gray-100 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-200 transition">
                    ← Back
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

            {{-- Venue Details Card --}}
            <div class="bg-white shadow-sm rounded-xl border border-gray-200 p-6 mb-6">
                <div class="grid md:grid-cols-2 gap-6">
                    
                    {{-- Left Column --}}
                    <div>
                        <h3 class="text-xs font-bold uppercase text-indigo-600 mb-3">Venue Information</h3>

                        <div class="space-y-4">
                            <div>
                                <p class="text-xs text-gray-500 uppercase font-semibold">Name</p>
                                <p class="text-lg font-bold text-gray-900">{{ $venue->name }}</p>
                            </div>

                            <div>
                                <p class="text-xs text-gray-500 uppercase font-semibold">Address</p>
                                <p class="text-sm text-gray-700">{{ $venue->address }}</p>
                            </div>

                            <div>
                                <p class="text-xs text-gray-500 uppercase font-semibold">Capacity</p>
                                <p class="text-sm font-bold text-blue-600">{{ $venue->capacity }} persons</p>
                            </div>
                        </div>
                    </div>

                    {{-- Right Column --}}
                    <div>
                        <h3 class="text-xs font-bold uppercase text-indigo-600 mb-3">Facilities & Amenities</h3>

                        @if($venue->facilities)
                            <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ $venue->facilities }}</p>
                        @else
                            <p class="text-sm text-gray-500 italic">No facilities listed</p>
                        @endif
                    </div>

                </div>
            </div>

            {{-- Scheduled Events --}}
            <div class="bg-white shadow-sm rounded-xl border border-gray-200 overflow-hidden">
                
                <div class="p-6 border-b">
                    <h3 class="text-lg font-bold text-gray-900">
                        Scheduled Events ({{ count($venue->events) }})
                    </h3>
                    <p class="mt-1 text-sm text-gray-500">Upcoming and active events at this venue</p>
                </div>

                @forelse($venue->events as $event)

                    <div class="px-6 py-4 border-b last:border-0 hover:bg-gray-50 transition">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <h4 class="font-bold text-gray-900 text-base">
                                    {{ $event->title }}
                                </h4>

                                <p class="mt-1 text-sm text-gray-600">
                                    {{ $event->description ? Str::limit($event->description, 100) : 'No description' }}
                                </p>

                                <div class="mt-3 flex flex-wrap gap-3 text-xs">
                                    <span class="inline-flex items-center px-2 py-1 rounded-md bg-gray-100 text-gray-700">
                                        📅 {{ $event->start_at->format('M d, Y') }}
                                    </span>

                                    <span class="inline-flex items-center px-2 py-1 rounded-md bg-gray-100 text-gray-700">
                                        🕐 {{ $event->start_at->format('h:i A') }} - {{ $event->end_at->format('h:i A') }}
                                    </span>

                                    <span class="inline-flex items-center px-2 py-1 rounded-md
                                        @switch($event->status)
                                            @case('pending_approvals') bg-yellow-100 text-yellow-800 @break
                                            @case('approved') bg-blue-100 text-blue-800 @break
                                            @case('published') bg-green-100 text-green-800 @break
                                            @default bg-gray-100 text-gray-700
                                        @endswitch">
                                        {{ Str::headline($event->status) }}
                                    </span>

                                    <span class="inline-flex items-center px-2 py-1 rounded-md bg-indigo-100 text-indigo-800">
                                        👥 {{ $event->number_of_participants }} participants
                                    </span>
                                </div>
                            </div>

                            <a href="{{ route('events.show', $event) }}"
                               class="ml-4 px-3 py-1.5 bg-indigo-100 text-indigo-700 text-xs rounded-md hover:bg-indigo-200 transition font-semibold whitespace-nowrap">
                                View Details
                            </a>
                        </div>
                    </div>

                @empty

                    <div class="px-6 py-12 text-center text-gray-500">
                        <p class="text-base font-semibold">No events scheduled</p>
                        <p class="text-sm mt-1">This venue currently has no active events</p>
                    </div>

                @endforelse

            </div>

        </div>
    </div>

</x-app-layout>
