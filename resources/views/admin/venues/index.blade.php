<x-app-layout>
<div class="max-w-7xl mx-auto py-8 sm:px-6 lg:px-8">

    {{-- ================= HEADER ================= --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Venue Management</h1>
            <p class="mt-1 text-sm text-gray-500">
                Manage campus venues including capacity, facilities, and assigned events.
            </p>
        </div>

        <div class="flex flex-col md:flex-row items-start md:items-center gap-3 w-full md:w-auto">
            {{-- Search Form --}}
            <form action="{{ route('admin.venues.index') }}" method="GET" class="flex w-full md:w-auto">
                <input type="text"
                       name="search"
                       value="{{ request('search') }}"
                       placeholder="Search venues..."
                       class="w-full md:w-64 px-3 py-2 border border-gray-300 rounded-l-lg focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                <button type="submit"
                        class="px-4 py-2 bg-indigo-600 text-white rounded-r-lg text-sm hover:bg-indigo-700 transition">
                    Search
                </button>
            </form>

            {{-- Create Button --}}
            @can('create', \App\Models\Venue::class)
                <a href="{{ route('admin.venues.create') }}"
                   class="inline-flex items-center rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700 transition">
                    + Create Venue
                </a>
            @endcan
        </div>
    </div>

    {{-- ================= VENUE LIST ================= --}}
    <div class="bg-white shadow-sm ring-1 ring-gray-900/5 sm:rounded-xl overflow-hidden">

        @forelse($venues as $venue)
            <div class="relative flex flex-col gap-4 px-6 py-6 border-b last:border-0 hover:bg-gray-50 lg:flex-row lg:items-center">
                
                {{-- LEFT CONTENT --}}
                <div class="flex-1 min-w-0">
                    <h2 class="text-lg font-bold text-gray-900">{{ $venue->name }}</h2>
                    <p class="mt-1 text-sm text-gray-500">📍 {{ $venue->address }}</p>

                    <div class="mt-3 flex flex-wrap gap-2">
                        <span class="badge bg-blue-50 text-blue-700">
                            👥 Capacity: {{ $venue->capacity }} persons
                        </span>

                        <span class="badge bg-indigo-50 text-indigo-700">
                            📅 Events: {{ $venue->events_count }}
                        </span>

                        @if($venue->facilities)
                            <span class="badge bg-green-50 text-green-700">
                                🏗️ {{ Str::limit($venue->facilities, 40) }}
                            </span>
                        @endif
                    </div>
                </div>

                {{-- RIGHT ACTIONS --}}
                <div class="flex flex-wrap gap-2 lg:mt-0">
                    <a href="{{ route('admin.venues.show', $venue) }}"
                       class="btn-secondary text-gray-700">
                        View Events
                    </a>

                    @can('update', $venue)
                        <a href="{{ route('admin.venues.edit', $venue) }}"
                           class="btn-secondary text-blue-700">
                            Edit
                        </a>
                    @endcan

                    @can('delete', $venue)
                        <form action="{{ route('admin.venues.destroy', $venue) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    onclick="return confirm('Are you sure? This venue must have no active events.')"
                                    class="btn-danger-outline text-red-600">
                                Delete
                            </button>
                        </form>
                    @endcan
                </div>
            </div>

        @empty
            <div class="text-center py-12 text-gray-500">
                <p class="text-lg font-semibold">No venues found</p>
                <p class="text-sm mt-1">Try a different search or create a new venue.</p>

               
            </div>
        @endforelse
    </div>
</div>

{{-- ================= REUSABLE BUTTON STYLES ================= --}}
<style>
.badge{ @apply px-2 py-1 text-xs font-medium bg-gray-100 text-gray-600 rounded-md; }
.btn-primary{ @apply px-3 py-1.5 bg-indigo-600 text-white text-xs rounded-md hover:bg-indigo-500; }
.btn-success{ @apply px-3 py-1.5 bg-green-600 text-white text-xs rounded-md hover:bg-green-500; }
.btn-danger{ @apply px-3 py-1.5 bg-red-600 text-white text-xs rounded-md hover:bg-red-500; }
.btn-danger-outline{ @apply px-3 py-1.5 border border-red-500 text-red-600 text-xs rounded-md hover:bg-red-50; }
.btn-secondary{ @apply px-3 py-1.5 bg-gray-100 text-gray-700 text-xs rounded-md hover:bg-gray-200; }
.btn-disabled{ @apply px-3 py-1.5 bg-gray-100 text-gray-400 text-xs rounded-md cursor-not-allowed; }
</style>

</x-app-layout>
