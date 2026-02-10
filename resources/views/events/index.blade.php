<x-app-layout>
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8"
         x-data="{
            showModal: false,
            actionUrl: '',
            modalTitle: '',
            modalBody: '',
            modalButtonText: '',
            modalColor: 'indigo',
            isDelete: false,
            isInfoOnly: false,
            setupModal(url, title, body, btnText, color, isDeleteAction = false, isInfo = false) {
                this.actionUrl = url;
                this.modalTitle = title;
                this.modalBody = body;
                this.modalButtonText = btnText;
                this.modalColor = color;
                this.isDelete = isDeleteAction;
                this.isInfoOnly = isInfo;
                this.showModal = true;
            }
         }">

        <div class="px-4 py-6 sm:px-0">
            {{-- Header Section --}}
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">
                        @role('admin')
                            Event Request Management
                        @else
                            Event Dashboard
                        @endrole
                    </h1>
                    <p class="mt-1 text-sm text-gray-500">
                        @role('admin')
                            Review, approve, and manage campus event logistics.
                        @else
                            View your requested events and upcoming published schedules.
                        @endrole
                    </p>
                </div>

                <a href="{{ route('events.create') }}"
                   class="inline-flex items-center rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 transition-all">
                    <svg class="-ml-1 mr-2 h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" /></svg>
                    Request New Event
                </a>
            </div>

            {{-- Events List --}}
            <div class="bg-white shadow-sm ring-1 ring-gray-900/5 sm:rounded-xl overflow-hidden">
                @if($events->count())
                    <ul role="list" class="divide-y divide-gray-100">
                        @foreach($events as $event)
                            <li class="relative flex flex-col gap-x-6 px-4 py-5 hover:bg-gray-50 sm:px-6 lg:flex-row lg:items-center">
                                
                                {{-- Event Information --}}
                                <div class="min-w-0 flex-auto">
                                    <div class="flex items-start gap-x-3">
                                        <p class="text-sm font-bold leading-6 text-gray-900">{{ $event->title }}</p>
                                        <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset 
                                            @switch($event->status)
                                                @case('pending_approval') bg-yellow-50 text-yellow-800 ring-yellow-600/20 @break
                                                @case('approved') bg-blue-50 text-blue-700 ring-blue-700/10 @break
                                                @case('published') bg-green-50 text-green-700 ring-green-600/20 @break
                                                @default bg-gray-50 text-gray-600 ring-gray-500/10
                                            @endswitch">
                                            {{ Str::title(str_replace('_', ' ', $event->status)) }}
                                        </span>
                                    </div>
                                    
                                    <div class="mt-2 flex flex-col space-y-1">
                                        <div class="flex items-center gap-x-2 text-xs leading-5 text-gray-500">
                                            <svg class="h-4 w-4 flex-none text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                            {{ optional($event->venue)->name ?? 'Venue TBD' }}
                                        </div>
                                        <div class="flex items-center gap-x-2 text-xs leading-5 text-gray-500">
                                            <svg class="h-4 w-4 flex-none text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                            {{ $event->start_at->format('M d, Y') }} | {{ $event->start_at->format('h:i A') }} - {{ $event->end_at->format('h:i A') }}
                                        </div>
                                    </div>

                                    {{-- Integrated Detailed Stats --}}
                                    <div class="mt-4 flex flex-wrap gap-2">
                                        <span class="inline-flex items-center rounded-md bg-gray-100 px-2 py-1 text-xs font-medium text-gray-600">
                                            Committee: {{ $event->participants->count() }}
                                        </span>
                                        <span class="inline-flex items-center rounded-md bg-gray-100 px-2 py-1 text-xs font-medium text-gray-600">
                                            Resources: {{ $event->resourceAllocations->count() }}
                                        </span>
                                        
                                        {{-- NEW: Resource Logistics Total Calculation --}}
                                        @php
                                            $logisticsTotal = $event->resourceAllocations->sum(function($allocation) {
                                                return $allocation->quantity * ($allocation->resource->price ?? 0);
                                            });
                                        @endphp
                                        
                                        @if($logisticsTotal > 0)
                                            <span class="inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20">
                                                Logistics: ${{ number_format($logisticsTotal, 2) }}
                                            </span>
                                        @endif

                                        {{-- Keep original budget if exists --}}
                                        @if($event->budget && $event->budget->count() > 0)
                                            <span class="inline-flex items-center rounded-md bg-indigo-50 px-2 py-1 text-xs font-medium text-indigo-700">
                                                Est. Budget: ${{ number_format($event->budget->sum('estimated_amount'), 2) }}
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                {{-- Action Buttons --}}
                                <div class="mt-4 flex flex-shrink-0 flex-wrap gap-2 lg:mt-0">
                                    <a href="{{ route('events.show', $event) }}"
                                       class="flex items-center gap-1 rounded-md bg-white px-3 py-1.5 text-xs font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                                        View Full Details
                                    </a>

                                    @role('admin')
                                        @if($event->status === 'pending_approval')
                                            <button @click="setupModal('{{ route('events.approve', $event) }}', 'Approve Event Request', 'Confirm approval? This notifies the requester and locks in the venue.', 'Approve', 'green')"
                                                    class="rounded-md bg-green-600 px-3 py-1.5 text-xs font-semibold text-white shadow-sm hover:bg-green-500">
                                                Approve
                                            </button>
                                            <button @click="setupModal('{{ route('events.reject', $event) }}', 'Reject Event', 'This will cancel the request and resource allocations.', 'Reject', 'red')"
                                                    class="rounded-md bg-red-600 px-3 py-1.5 text-xs font-semibold text-white shadow-sm hover:bg-red-500">
                                                Reject
                                            </button>
                                        @endif

                                        @if($event->status === 'approved')
                                            <button @click="setupModal('{{ route('events.publish', $event) }}', 'Publish Event', 'This makes the event visible to the public calendar.', 'Publish Now', 'indigo')"
                                                    class="rounded-md bg-indigo-600 px-3 py-1.5 text-xs font-semibold text-white shadow-sm hover:bg-indigo-500">
                                                Publish
                                            </button>
                                        @endif

                                        @if($event->status === 'published')
                                            <button @click="setupModal('', 'Action Blocked', 'Published events cannot be deleted to maintain record integrity. Unpublish first.', 'Understood', 'gray', false, true)"
                                                    class="rounded-md bg-gray-100 px-3 py-1.5 text-xs font-semibold text-gray-400 cursor-not-allowed">
                                                Delete
                                            </button>
                                        @else
                                            <button @click="setupModal('{{ route('events.destroy', $event) }}', 'Delete Event', 'Are you sure? This deletes all associated budget and resource data.', 'Delete Permanently', 'red', true)"
                                                    class="rounded-md bg-white px-3 py-1.5 text-xs font-semibold text-red-600 shadow-sm ring-1 ring-inset ring-red-300 hover:bg-red-50">
                                                Delete
                                            </button>
                                        @endif
                                    @endrole
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <h3 class="mt-2 text-sm font-semibold text-gray-900">No events</h3>
                        <p class="mt-1 text-sm text-gray-500">Get started by creating a new event request.</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Dynamic Alpine Modal --}}
        <div x-show="showModal" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/75" 
             x-cloak>
            
            <div class="bg-white rounded-xl shadow-2xl max-w-md w-full overflow-hidden transform transition-all" 
                 @click.away="showModal = false">
                
                <div class="p-6">
                    <h2 class="text-xl font-bold text-gray-900" x-text="modalTitle"></h2>
                    <p class="mt-3 text-sm text-gray-600 leading-relaxed" x-text="modalBody"></p>
                </div>

                <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3">
                    <button @click="showModal = false" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                        Cancel
                    </button>
                    
                    <template x-if="!isInfoOnly">
                        <form :action="actionUrl" method="POST">
                            @csrf
                            <template x-if="isDelete">
                                <input type="hidden" name="_method" value="DELETE">
                            </template>
                            <button type="submit"
                                    class="px-4 py-2 text-sm font-bold rounded-lg text-white shadow-sm transition-all"
                                    :class="{
                                        'bg-green-600 hover:bg-green-700': modalColor === 'green',
                                        'bg-red-600 hover:bg-red-700': modalColor === 'red',
                                        'bg-indigo-600 hover:bg-indigo-700': modalColor === 'indigo',
                                        'bg-gray-600 hover:bg-gray-700': modalColor === 'gray'
                                    }"
                                    x-text="modalButtonText">
                            </button>
                        </form>
                    </template>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
