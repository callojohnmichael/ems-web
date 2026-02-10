<x-app-layout>
    <div class="max-w-6xl mx-auto py-6 sm:px-6 lg:px-8">

        {{-- Success Notification --}}
        @if(session('success'))
            <div class="rounded-md bg-green-50 p-4 mb-6 border border-green-200">
                <p class="text-sm font-semibold text-green-800">{{ session('success') }}</p>
            </div>
        @endif

        {{-- Header --}}
        <div class="bg-white shadow rounded-lg border mb-6">
            <div class="px-6 py-5 flex justify-between items-center bg-gray-50 border-b">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">{{ $event->title }}</h2>
                    <p class="text-sm text-gray-500 font-medium">
                        Reference #EVT-{{ str_pad($event->id, 5, '0', STR_PAD_LEFT) }}
                    </p>
                </div>

                <span class="px-4 py-1.5 rounded-full text-sm font-bold
                    @if($event->status === 'pending_approvals') bg-yellow-100 text-yellow-800
                    @elseif($event->status === 'approved') bg-blue-100 text-blue-800
                    @elseif($event->status === 'published') bg-green-100 text-green-800
                    @elseif($event->status === 'rejected') bg-red-100 text-red-800
                    @else bg-gray-100 text-gray-700
                    @endif">
                    {{ Str::title(str_replace('_',' ', $event->status)) }}
                </span>
            </div>

            {{-- Core Info --}}
            <div class="grid md:grid-cols-2 gap-6 p-6">
                <div>
                    <h4 class="text-xs font-bold uppercase text-indigo-600 mb-3">Event Details</h4>
                    <p class="text-sm text-gray-700 mb-4">
                        {{ $event->description ?: 'No description provided.' }}
                    </p>

                    <p class="text-sm">
                        <span class="font-semibold">Requested By:</span>
                        {{ $event->requestedBy->name ?? 'System' }}
                    </p>
                    <p class="text-sm">
                        <span class="font-semibold">Created:</span>
                        {{ $event->created_at->format('M d, Y') }}
                    </p>
                </div>

                <div>
                    <h4 class="text-xs font-bold uppercase text-indigo-600 mb-3">Schedule & Venue</h4>
                    <p class="text-sm font-semibold text-gray-900">
                        {{ optional($event->venue)->name ?? 'No venue assigned' }}
                    </p>
                    <p class="text-sm text-gray-600">
                        {{ $event->start_at->format('F d, Y g:i A') }}
                        –
                        {{ $event->end_at->format('g:i A') }}
                    </p>
                </div>
            </div>
        </div>

        {{-- Detail Grid --}}
        <div class="grid md:grid-cols-3 gap-6 mb-8">

            {{-- Budget --}}
            <div class="bg-white border rounded-lg p-5 shadow">
                <h4 class="font-bold mb-3">Budget Items</h4>

                @forelse($event->budget as $item)
                    <div class="flex justify-between text-sm mb-1">
                        <span>{{ $item->description }}</span>
                        <span class="font-semibold">
                            ₱{{ number_format($item->estimated_amount, 2) }}
                        </span>
                    </div>
                @empty
                    <p class="text-xs italic text-gray-400">No budget items.</p>
                @endforelse
            </div>

            {{-- Resources --}}
            <div class="bg-white border rounded-lg p-5 shadow">
                <h4 class="font-bold mb-3">Logistics Resources</h4>

                @forelse($event->resourceAllocations as $allocation)
                    <div class="flex gap-2 text-sm mb-1">
                        <span class="font-bold">{{ $allocation->quantity }}×</span>
                        <span>{{ $allocation->resource->name ?? 'Resource' }}</span>
                    </div>
                @empty
                    <p class="text-xs italic text-gray-400">No resources requested.</p>
                @endforelse
            </div>

            {{-- Committee --}}
            <div class="bg-white border rounded-lg p-5 shadow">
                <h4 class="font-bold mb-3">Committee</h4>

                @forelse($event->participants as $participant)
                    <div class="text-sm mb-2">
                        <p class="font-semibold">
                            {{ $participant->employee->full_name ?? 'Employee' }}
                        </p>
                        <p class="text-xs text-gray-500">
                            {{ $participant->role ?? 'Member' }}
                        </p>
                    </div>
                @empty
                    <p class="text-xs italic text-gray-400">No committee assigned.</p>
                @endforelse
            </div>

        </div>

        {{-- Custodian + Finance --}}
        <div class="grid md:grid-cols-2 gap-6 mb-8">

            {{-- Custodian --}}
            <div class="bg-white border rounded-lg p-5 shadow">
                <h4 class="font-bold mb-3">Custodian Equipment</h4>

                @forelse($event->custodianRequests as $request)
                    <div class="flex justify-between text-sm mb-1">
                        <span>{{ $request->custodianMaterial->name }}</span>
                        <span class="font-semibold">{{ $request->quantity }}</span>
                    </div>
                @empty
                    <p class="text-xs italic text-gray-400">No custodian items.</p>
                @endforelse
            </div>

            {{-- Finance --}}
            <div class="bg-white border rounded-lg p-5 shadow">
                <h4 class="font-bold mb-3">Finance Summary</h4>

                @if($event->financeRequest)
                    <p class="text-sm">
                        Logistics: ₱{{ number_format($event->financeRequest->logistics_total, 2) }}
                    </p>
                    <p class="text-sm font-bold text-indigo-600">
                        Grand Total: ₱{{ number_format($event->financeRequest->grand_total, 2) }}
                    </p>
                    <p class="text-xs text-gray-500 mt-1">
                        Status: {{ Str::title($event->financeRequest->status) }}
                    </p>
                @else
                    <p class="text-xs italic text-gray-400">No finance request generated.</p>
                @endif
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex justify-between items-center bg-white p-5 rounded-lg border shadow">
            <a href="{{ route('events.index') }}" class="text-sm font-semibold text-gray-600">
                ← Back to Events
            </a>

            @can('update', $event)
                <div class="flex gap-3">
                    <a href="{{ route('events.edit', $event) }}"
                       class="px-4 py-2 border rounded text-sm font-bold">
                        Edit
                    </a>
                </div>
            @endcan
        </div>

    </div>
</x-app-layout>
