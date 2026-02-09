<x-app-layout>
    <div class="max-w-4xl mx-auto py-6 sm:px-6 lg:px-8">
        @if(session('success'))
            <div class="rounded-md bg-green-50 p-4 mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <div class="bg-white shadow overflow-hidden sm:rounded-lg border border-gray-200">
            <div class="px-4 py-5 sm:px-6">
                <div class="flex justify-between items-start">
                    <div>
                        <h3 class="text-lg leading-6 font-medium text-gray-900">
                            {{ $event->title }}
                        </h3>
                        <p class="mt-1 max-w-2xl text-sm text-gray-500">
                            Event Details
                        </p>
                    </div>
                    <div class="flex space-x-2">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                            @if($event->status === 'pending_approval') bg-yellow-100 text-yellow-800
                            @elseif($event->status === 'approved') bg-blue-100 text-blue-800
                            @elseif($event->status === 'rejected') bg-red-100 text-red-800
                            @elseif($event->status === 'published') bg-green-100 text-green-800
                            @elseif($event->status === 'cancelled') bg-gray-100 text-gray-800
                            @elseif($event->status === 'completed') bg-purple-100 text-purple-800
                            @endif">
                            {{ Str::title(str_replace('_', ' ', $event->status)) }}
                        </span>
                    </div>
                </div>
            </div>
            <div class="border-t border-gray-200">
                <dl>
                    <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Description</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            {{ $event->description ?: 'No description provided' }}
                        </dd>
                    </div>
                    <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Start Date & Time</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            {{ $event->start_at->format('l, F j, Y \a\t g:i A') }}
                        </dd>
                    </div>
                    <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">End Date & Time</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            {{ $event->end_at->format('l, F j, Y \a\t g:i A') }}
                        </dd>
                    </div>
                    <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Duration</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            {{ $event->start_at->diffInHours($event->end_at) }} hours
                        </dd>
                    </div>
                    @if($event->requestedBy)
                        <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Requested By</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                {{ $event->requestedBy->name }} ({{ $event->requestedBy->email }})
                            </dd>
                        </div>
                    @endif
                    <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Created</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            {{ $event->created_at->format('M j, Y g:i A') }}
                        </dd>
                    </div>
                </dl>
            </div>
            
            @if(auth()->user()->hasRole('admin'))
                <div class="bg-gray-50 px-4 py-3 sm:px-6 border-t border-gray-200">
                    <div class="flex justify-between items-center">
                        <div class="text-sm text-gray-500 font-medium">
                            Admin Actions
                        </div>
                        <div class="flex space-x-3">
                            @if($event->status === 'pending_approval')
                                <form action="{{ route('events.approve', $event) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="rounded-md bg-green-600 px-3 py-2 text-sm font-medium text-white shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                                        Approve Event
                                    </button>
                                </form>
                                
                                <button onclick="openRejectModal()" class="rounded-md bg-red-600 px-3 py-2 text-sm font-medium text-white shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                                    Reject Event
                                </button>
                            @endif
                            
                            @if($event->status === 'approved')
                                <form action="{{ route('events.publish', $event) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                        Publish Event
                                    </button>
                                </form>
                            @endif
                            
                            <a href="{{ route('events.edit', $event) }}" class="rounded-md border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                Edit Event
                            </a>
                            
                            <form action="{{ route('events.destroy', $event) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this event?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="rounded-md border border-transparent bg-red-600 px-3 py-2 text-sm font-medium text-white shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                                    Delete Event
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endif
        </div>
        
        <div class="mt-6 flex justify-end">
            <a href="{{ route('events.index') }}" class="rounded-md border border-gray-300 bg-white py-2 px-4 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                Back to Events
            </a>
        </div>
    </div>

    @if(auth()->user()->hasRole('admin') && $event->status === 'pending_approval')
    <div id="rejectModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900">Reject Event</h3>
                <div class="mt-2">
                    <p class="text-sm text-gray-500">
                        Please provide a reason for rejecting this event request (optional).
                    </p>
                </div>
                <form action="{{ route('events.reject', $event) }}" method="POST" class="mt-4">
                    @csrf
                    <div class="mb-4">
                        <label for="reason" class="block text-sm font-medium text-gray-700">Reason</label>
                        <textarea
                            id="reason"
                            name="reason"
                            rows="3"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            placeholder="Optional reason for rejection"
                        ></textarea>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeRejectModal()" class="rounded-md border border-gray-300 bg-white py-2 px-4 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                            Cancel
                        </button>
                        <button type="submit" class="rounded-md border border-transparent bg-red-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                            Reject Event
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
    function openRejectModal() {
        document.getElementById('rejectModal').classList.remove('hidden');
    }

    function closeRejectModal() {
        document.getElementById('rejectModal').classList.add('hidden');
    }
    </script>
    @endif
</x-app-layout>