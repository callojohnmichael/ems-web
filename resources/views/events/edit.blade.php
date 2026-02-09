<x-app-layout>
    <div class="max-w-2xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="bg-white shadow-sm rounded-lg border border-gray-200">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">
                    Edit Event: {{ $event->title }}
                </h3>
                
                <form action="{{ route('events.update', $event) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="space-y-6">
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700">
                                Event Title <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="text"
                                id="title"
                                name="title"
                                value="{{ old('title', $event->title) }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('title') border-red-500 @enderror"
                                placeholder="Enter event title"
                                required
                            >
                            @error('title')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700">
                                Event Description <span class="text-red-500">*</span>
                            </label>
                            <textarea
                                id="description"
                                name="description"
                                rows="4"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('description') border-red-500 @enderror"
                                placeholder="Describe your event in detail"
                                required
                            >{{ old('description', $event->description) }}</textarea>
                            @error('description')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <div>
                                <label for="start_at" class="block text-sm font-medium text-gray-700">
                                    Start Date & Time <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="datetime-local"
                                    id="start_at"
                                    name="start_at"
                                    value="{{ old('start_at', $event->start_at->format('Y-m-d\TH:i')) }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('start_at') border-red-500 @enderror"
                                    required
                                >
                                @error('start_at')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="end_at" class="block text-sm font-medium text-gray-700">
                                    End Date & Time <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="datetime-local"
                                    id="end_at"
                                    name="end_at"
                                    value="{{ old('end_at', $event->end_at->format('Y-m-d\TH:i')) }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('end_at') border-red-500 @enderror"
                                    required
                                >
                                @error('end_at')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700">
                                Event Status <span class="text-red-500">*</span>
                            </label>
                            <select
                                id="status"
                                name="status"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('status') border-red-500 @enderror"
                                required
                            >
                                <option value="{{ \App\Models\Event::STATUS_PENDING_APPROVAL }}" {{ old('status', $event->status) === \App\Models\Event::STATUS_PENDING_APPROVAL ? 'selected' : '' }}>
                                    Pending Approval
                                </option>
                                <option value="{{ \App\Models\Event::STATUS_APPROVED }}" {{ old('status', $event->status) === \App\Models\Event::STATUS_APPROVED ? 'selected' : '' }}>
                                    Approved
                                </option>
                                <option value="{{ \App\Models\Event::STATUS_REJECTED }}" {{ old('status', $event->status) === \App\Models\Event::STATUS_REJECTED ? 'selected' : '' }}>
                                    Rejected
                                </option>
                                <option value="{{ \App\Models\Event::STATUS_PUBLISHED }}" {{ old('status', $event->status) === \App\Models\Event::STATUS_PUBLISHED ? 'selected' : '' }}>
                                    Published
                                </option>
                                <option value="{{ \App\Models\Event::STATUS_CANCELLED }}" {{ old('status', $event->status) === \App\Models\Event::STATUS_CANCELLED ? 'selected' : '' }}>
                                    Cancelled
                                </option>
                                <option value="{{ \App\Models\Event::STATUS_COMPLETED }}" {{ old('status', $event->status) === \App\Models\Event::STATUS_COMPLETED ? 'selected' : '' }}>
                                    Completed
                                </option>
                            </select>
                            @error('status')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="bg-blue-50 border border-blue-200 rounded-md p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-blue-800">
                                        Current Event Information
                                    </h3>
                                    <div class="mt-2 text-sm text-blue-700">
                                        <p><strong>Requested by:</strong> {{ $event->requestedBy->name }}</p>
                                        <p><strong>Created:</strong> {{ $event->created_at->format('M j, Y g:i A') }}</p>
                                        <p><strong>Last updated:</strong> {{ $event->updated_at->format('M j, Y g:i A') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end space-x-3">
                        <a href="{{ route('events.show', $event) }}" class="rounded-md border border-gray-300 bg-white py-2 px-4 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                            Cancel
                        </a>
                        <button type="submit" class="rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                            Update Event
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>