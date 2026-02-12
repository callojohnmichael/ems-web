<x-app-layout>
    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
        <div class="flex items-start justify-between">
            <div>
                <h2 class="text-lg font-semibold text-gray-900">Program Flow: {{ $event->title }}</h2>
                <p class="mt-1 text-sm text-gray-500">Event date: {{ optional($event->start_at)->format('M d, Y H:i') ?? '-' }}</p>
            </div>

            <div class="text-sm text-gray-600">Status: <span class="font-medium">{{ ucfirst($event->status) }}</span></div>
        </div>

        <div class="mt-6">
            @if($event->programItems->isEmpty())
                <div class="text-sm text-gray-600">No program items yet.</div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full table-auto" id="program-items-table">
                        <thead>
                            <tr class="text-left text-sm text-gray-500">
                                <th class="px-2 py-2">#</th>
                                <th class="px-2 py-2">Title</th>
                                <th class="px-2 py-2">Start</th>
                                <th class="px-2 py-2">End</th>
                                <th class="px-2 py-2">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($event->programItems as $item)
                                <tr class="border-t" data-id="{{ $item->id }}">
                                    <td class="px-2 py-3 text-sm cursor-move">{{ $item->order }}</td>
                                    <td class="px-2 py-3 text-sm">{{ $item->title }}</td>
                                    <td class="px-2 py-3 text-sm">{{ optional($item->start_at)->format('M d, H:i') ?? '-' }}</td>
                                    <td class="px-2 py-3 text-sm">{{ optional($item->end_at)->format('M d, H:i') ?? '-' }}</td>
                                    <td class="px-2 py-3 text-sm">
                                        @can('manage scheduling')
                                            <details class="inline-block">
                                                <summary class="cursor-pointer text-sm text-indigo-600">Edit</summary>
                                                <form action="{{ route('program-flow.items.update', $item) }}" method="POST" class="mt-2 space-y-2">
                                                    @csrf
                                                    @method('PUT')
                                                    <div>
                                                        <input type="text" name="title" value="{{ session('program_item_error_item_id') == $item->id ? old('title', $item->title) : $item->title }}" class="border rounded px-2 py-1 w-full" required />
                                                        @if(session('program_item_error_item_id') == $item->id)
                                                            @error('title') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                                                        @endif
                                                    </div>
                                                    <div class="grid grid-cols-2 gap-2">
                                                        <input type="datetime-local" name="start_at" value="{{ session('program_item_error_item_id') == $item->id ? old('start_at', $item->start_at ? $item->start_at->format('Y-m-d\TH:i') : '') : ($item->start_at ? $item->start_at->format('Y-m-d\TH:i') : '') }}" class="border rounded px-2 py-1" @if($event->start_at) min="{{ $event->start_at->format('Y-m-d\TH:i') }}" @endif @if($event->end_at) max="{{ $event->end_at->format('Y-m-d\TH:i') }}" @endif />
                                                        <input type="datetime-local" name="end_at" value="{{ session('program_item_error_item_id') == $item->id ? old('end_at', $item->end_at ? $item->end_at->format('Y-m-d\TH:i') : '') : ($item->end_at ? $item->end_at->format('Y-m-d\TH:i') : '') }}" class="border rounded px-2 py-1" @if($event->start_at) min="{{ $event->start_at->format('Y-m-d\TH:i') }}" @endif @if($event->end_at) max="{{ $event->end_at->format('Y-m-d\TH:i') }}" @endif />
                                                    </div>
                                                    @if(session('program_item_error_item_id') == $item->id)
                                                        @error('start_at') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
                                                        @error('end_at') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
                                                    @endif
                                                    <div class="flex items-center space-x-2">
                                                        <button class="px-3 py-1 bg-green-600 text-white rounded">Save</button>
                                                        <form action="{{ route('program-flow.items.destroy', $item) }}" method="POST" onsubmit="return confirm('Remove this item?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="px-3 py-1 bg-red-600 text-white rounded">Delete</button>
                                                        </form>
                                                    </div>
                                                </form>
                                            </details>
                                        @endcan
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        @can('manage scheduling')
            <div class="mt-6 border-t pt-4">
                <h3 class="text-sm font-medium">Add Program Item</h3>
                <form action="{{ route('program-flow.items.store', $event) }}" method="POST" class="mt-3 space-y-3">
                    @csrf
                    <div>
                        <label class="text-sm">Title</label>
                        <input type="text" name="title" required class="mt-1 block w-full border rounded px-2 py-1" value="{{ old('title') }}" />
                        @error('title') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="text-sm">Start</label>
                            <input type="datetime-local" name="start_at" class="mt-1 block w-full border rounded px-2 py-1" value="{{ old('start_at', $event->start_at ? $event->start_at->format('Y-m-d\TH:i') : '') }}" @if($event->start_at) min="{{ $event->start_at->format('Y-m-d\TH:i') }}" @endif @if($event->end_at) max="{{ $event->end_at->format('Y-m-d\TH:i') }}" @endif />
                            @error('start_at') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="text-sm">End</label>
                            <input type="datetime-local" name="end_at" class="mt-1 block w-full border rounded px-2 py-1" value="{{ old('end_at', $event->end_at ? $event->end_at->format('Y-m-d\TH:i') : '') }}" @if($event->start_at) min="{{ $event->start_at->format('Y-m-d\TH:i') }}" @endif @if($event->end_at) max="{{ $event->end_at->format('Y-m-d\TH:i') }}" @endif />
                            @error('end_at') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="flex items-center space-x-2">
                        <button class="px-4 py-2 bg-indigo-600 text-white rounded">Add Item</button>
                    </div>
                </form>
            </div>
        @endcan

    </div>
</x-app-layout>

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const tableBody = document.querySelector('#program-items-table tbody');
            if (!tableBody) return;

            const sortable = Sortable.create(tableBody, {
                handle: '.cursor-move',
                animation: 150,
                onEnd: function (evt) {
                    const ids = Array.from(tableBody.querySelectorAll('tr[data-id]')).map(tr => parseInt(tr.getAttribute('data-id')));
                    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                    fetch("{{ route('program-flow.items.reorder', $event) }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': token,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ order: ids })
                    }).then(r => r.json()).then(data => {
                        if (data.message) {
                            showToast(data.message, 'success');
                        } else {
                            showToast('Order updated.', 'success');
                        }
                    }).catch(err => console.error(err));
                }
            });
        });

        function showToast(message, type = 'info') {
            const existing = document.getElementById('program-toast-container');
            let container;
            if (existing) {
                container = existing;
            } else {
                container = document.createElement('div');
                container.id = 'program-toast-container';
                container.className = 'fixed top-5 right-5 z-50 space-y-2';
                document.body.appendChild(container);
            }

            const toast = document.createElement('div');
            toast.className = 'max-w-sm w-full p-3 rounded shadow-lg flex items-start space-x-3';
            toast.style.opacity = '0';

            const color = type === 'success' ? 'bg-green-50 border-green-200 text-green-800' : (type === 'error' ? 'bg-red-50 border-red-200 text-red-800' : 'bg-white border-gray-200');
            toast.className += ' border ' + color;

            const content = document.createElement('div');
            content.className = 'flex-1';
            content.innerText = message;

            const close = document.createElement('button');
            close.className = 'text-sm text-gray-500 hover:text-gray-700';
            close.innerText = '×';
            close.onclick = () => toast.remove();

            toast.appendChild(content);
            toast.appendChild(close);
            container.appendChild(toast);

            // fade in
            setTimeout(() => { toast.style.transition = 'opacity 200ms'; toast.style.opacity = '1'; }, 10);
            // auto remove
            setTimeout(() => { toast.style.opacity = '0'; setTimeout(() => toast.remove(), 250); }, 3500);
        }
    </script>
@endpush
