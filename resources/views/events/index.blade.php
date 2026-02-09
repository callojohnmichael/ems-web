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

        {{-- Success Toast --}}
        @if(session('success'))
            <div x-data="{ show: true }"
                 x-show="show"
                 x-init="setTimeout(() => show = false, 4000)"
                 class="fixed bottom-5 right-5 z-[60] max-w-sm rounded-lg bg-white shadow-xl border-l-4 border-green-500">
                <div class="p-4 text-sm text-gray-700">
                    {{ session('success') }}
                </div>
            </div>
        @endif

        <div class="px-4 py-6 sm:px-0">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-semibold text-gray-900">
                    @role('admin')
                        All Events
                    @else
                        Published Events
                    @endrole
                </h1>

                @role('user')
                    <a href="{{ route('events.create') }}"
                       class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">
                        Request New Event
                    </a>
                @endrole
            </div>

            <div class="bg-white shadow sm:rounded-md border border-gray-200">
                @if($events->count())
                    <ul class="divide-y divide-gray-200">
                        @foreach($events as $event)
                            <li class="hover:bg-gray-50">
                                <div class="px-4 py-4 sm:px-6 flex flex-col gap-3 md:flex-row md:justify-between">

                                    {{-- Event Info --}}
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2">
                                            <span class="font-semibold text-gray-900">
                                                {{ $event->title }}
                                            </span>

                                            <span class="text-xs font-semibold px-2 py-0.5 rounded-full
                                                @switch($event->status)
                                                    @case('pending_approval') bg-yellow-100 text-yellow-800 @break
                                                    @case('approved') bg-blue-100 text-blue-800 @break
                                                    @case('published') bg-green-100 text-green-800 @break
                                                    @default bg-gray-100 text-gray-600
                                                @endswitch
                                            ">
                                                {{ Str::title(str_replace('_', ' ', $event->status)) }}
                                            </span>
                                        </div>

                                        <div class="mt-1 text-sm text-gray-600">
                                            {{ optional($event->venue)->name ?? 'No venue assigned' }}
                                        </div>

                                        <div class="mt-1 text-xs text-gray-500">
                                            {{ $event->start_at }} → {{ $event->end_at }}
                                        </div>
                                    </div>

                                    {{-- Actions --}}
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <a href="{{ route('events.show', $event) }}"
                                           class="px-3 py-1.5 text-xs border rounded text-gray-700 hover:bg-gray-50">
                                            View
                                        </a>

                                        @role('admin')
                                            @if($event->status === 'pending_approval')
                                                <button
                                                    @click="setupModal(
                                                        '{{ route('events.approve', $event) }}',
                                                        'Approve Event',
                                                        'Approve this event request?',
                                                        'Approve',
                                                        'green'
                                                    )"
                                                    class="px-3 py-1.5 text-xs bg-green-600 text-white rounded hover:bg-green-700">
                                                    Approve
                                                </button>

                                                <button
                                                    @click="setupModal(
                                                        '{{ route('events.reject', $event) }}',
                                                        'Reject Event',
                                                        'Reject this event request?',
                                                        'Reject',
                                                        'red'
                                                    )"
                                                    class="px-3 py-1.5 text-xs bg-red-600 text-white rounded hover:bg-red-700">
                                                    Reject
                                                </button>
                                            @endif

                                            @if($event->status === 'approved')
                                                <button
                                                    @click="setupModal(
                                                        '{{ route('events.publish', $event) }}',
                                                        'Publish Event',
                                                        'Publish this event?',
                                                        'Publish',
                                                        'indigo'
                                                    )"
                                                    class="px-3 py-1.5 text-xs bg-indigo-600 text-white rounded hover:bg-indigo-700">
                                                    Publish
                                                </button>
                                            @endif

                                            @if($event->status === 'published')
                                                <button
                                                    @click="setupModal(
                                                        '',
                                                        'Action Blocked',
                                                        'Published events cannot be deleted. Unpublish or reject first.',
                                                        'Understood',
                                                        'gray',
                                                        false,
                                                        true
                                                    )"
                                                    class="px-3 py-1.5 text-xs bg-gray-100 text-gray-400 rounded cursor-not-allowed">
                                                    Delete
                                                </button>
                                            @else
                                                <button
                                                    @click="setupModal(
                                                        '{{ route('events.destroy', $event) }}',
                                                        'Delete Event',
                                                        'This action is permanent.',
                                                        'Delete',
                                                        'red',
                                                        true
                                                    )"
                                                    class="px-3 py-1.5 text-xs border border-red-300 text-red-600 rounded hover:bg-red-50">
                                                    Delete
                                                </button>
                                            @endif
                                        @endrole
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <div class="p-8 text-center text-gray-500">
                        No events available.
                    </div>
                @endif
            </div>
        </div>

        {{-- Confirmation / Info Modal --}}
        <div x-show="showModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
                <h2 class="text-lg font-semibold text-gray-900" x-text="modalTitle"></h2>
                <p class="mt-2 text-sm text-gray-600" x-text="modalBody"></p>

                <div class="mt-6 flex justify-end gap-2">
                    <template x-if="!isInfoOnly">
                        <form :action="actionUrl" method="POST">
                            @csrf
                            <template x-if="isDelete">
                                <input type="hidden" name="_method" value="DELETE">
                            </template>
                            <button type="submit"
                                    class="px-4 py-2 text-sm rounded text-white"
                                    :class="{
                                        'bg-green-600': modalColor === 'green',
                                        'bg-red-600': modalColor === 'red',
                                        'bg-indigo-600': modalColor === 'indigo'
                                    }"
                                    x-text="modalButtonText">
                            </button>
                        </form>
                    </template>

                    <button @click="showModal = false"
                            class="px-4 py-2 text-sm border rounded">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
