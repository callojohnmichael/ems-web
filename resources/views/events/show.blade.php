    <x-app-layout>
    <div class="max-w-6xl mx-auto py-6 sm:px-6 lg:px-8 space-y-6">

        {{-- SUCCESS MESSAGE --}}
        @if(session('success'))
            <div class="rounded-md bg-green-50 p-4 mb-6 border border-green-200">
                <p class="text-sm font-semibold text-green-800">
                    {{ session('success') }}
                </p>
            </div>
        @endif

        {{-- ================= HEADER ================= --}}
        <div class="bg-white shadow rounded-lg border">
            <div class="px-6 py-5 flex justify-between items-center bg-gray-50 border-b">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">
                        {{ $event->title }}
                    </h2>
                    <p class="text-sm text-gray-500 font-medium">
                        Reference #EVT-{{ str_pad($event->id, 5, '0', STR_PAD_LEFT) }}
                    </p>
                </div>

                <span class="px-4 py-1.5 rounded-full text-sm font-bold
                    @switch($event->status)
                        @case('pending_approval') @case('pending_approvals') bg-yellow-100 text-yellow-800 @break
                        @case('approved') bg-blue-100 text-blue-800 @break
                        @case('published') bg-green-100 text-green-800 @break
                        @case('rejected') bg-red-100 text-red-800 @break
                        @default bg-gray-100 text-gray-700
                    @endswitch
                ">
                    {{ Str::headline($event->status) }}
                </span>
            </div>

            <div class="grid md:grid-cols-2 gap-6 p-6">
                <div>
                    <h4 class="text-xs font-bold uppercase text-indigo-600 mb-3">Event Details</h4>
                    <p class="text-sm text-gray-700 mb-4">{{ $event->description ?: 'No description provided.' }}</p>
                    <p class="text-sm"><span class="font-semibold">Requested By:</span> {{ $event->requestedBy->name ?? 'System' }}</p>
                    <p class="text-sm"><span class="font-semibold">Created:</span> {{ $event->created_at->format('M d, Y') }}</p>
                </div>

                <div>
                    <h4 class="text-xs font-bold uppercase text-indigo-600 mb-3">Schedule & Venue</h4>
                    <p class="text-sm font-semibold text-gray-900">{{ optional($event->venue)->name ?? 'No venue assigned' }}</p>
                    <p class="text-sm text-gray-600">{{ $event->start_at->format('F d, Y g:i A') }} – {{ $event->end_at->format('g:i A') }}</p>
                    <p class="text-sm text-gray-600 mt-3">
                        <span class="font-semibold">Expected:</span> {{ $event->number_of_participants ?? 0 }}
                        <span class="ml-2 font-semibold">Registered:</span> {{ $participantCount }}
                    </p>
                    <div class="mt-2 flex gap-2">
                        @if($attendedCount > 0)
                            <span class="bg-green-100 text-green-800 px-2 py-0.5 rounded-full text-xs font-medium">Attended: {{ $attendedCount }}</span>
                        @endif
                        @if($absentCount > 0)
                            <span class="bg-red-100 text-red-800 px-2 py-0.5 rounded-full text-xs font-medium">Absent: {{ $absentCount }}</span>
                        @endif
                    </div>
                </div>

               {{-- ✅ SELECTED VENUE LOCATIONS WITH AMENITIES --}}
        <div class="md:col-span-2 pt-2">
            <div class="border-t pt-6">

                <p class="text-xs font-bold uppercase text-gray-500 mb-4">
                    Selected Location(s)
                </p>

                @if($event->venueBookings->count())
                    <div class="grid md:grid-cols-2 gap-4">
                        @foreach($event->venueBookings as $booking)

                            @php
                                $location = $booking->venueLocation;
                                $amenitiesRaw = $location->amenities ?? null;

                                if (is_string($amenitiesRaw)) {
                                    $decoded = json_decode($amenitiesRaw, true);
                                    $amenities = is_array($decoded)
                                        ? $decoded
                                        : array_map('trim', explode(',', $amenitiesRaw));
                                } elseif (is_array($amenitiesRaw)) {
                                    $amenities = $amenitiesRaw;
                                } else {
                                    $amenities = [];
                                }
                            @endphp

                            <div class="border rounded-lg p-4 bg-gray-50 hover:shadow-sm transition">

                                {{-- Header --}}
                                <div class="flex justify-between items-center mb-2">
                                    <span class="px-3 py-1 bg-indigo-100 text-indigo-700 text-xs font-semibold rounded-full">
                                        {{ $location->name ?? 'Location #' . $booking->venue_location_id }}
                                    </span>

                                    @if($location && $location->capacity)
                                        <span class="text-xs text-gray-500 font-medium">
                                            Capacity: {{ $location->capacity }}
                                        </span>
                                    @endif
                                </div>

                                {{-- Amenities --}}
                                @if(count($amenities))
                                    <div class="flex flex-wrap gap-2 mt-2">
                                        @foreach($amenities as $amenity)
                                            <span class="px-2 py-1 bg-green-100 text-green-700 text-xs font-medium rounded-md">
                                                {{ $amenity }}
                                            </span>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-xs italic text-gray-400">
                                        No amenities listed.
                                    </p>
                                @endif

                            </div>

                        @endforeach
                    </div>
                @else
                    <p class="text-xs italic text-gray-400">
                        No specific locations selected.
                    </p>
                @endif

            </div>
        </div>

            </div>

            
        </div>

        {{-- ================= COMMITTEE MEMBERS CARD ================= --}}
        <div class="bg-white shadow rounded-lg border overflow-hidden border-purple-100">
            <div class="px-6 py-4 border-b bg-purple-50 flex items-center justify-between">
                <h3 class="text-lg font-bold text-purple-900 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                    Committee Members
                </h3>
                <span class="bg-purple-200 text-purple-800 text-xs font-bold px-2.5 py-0.5 rounded-full">
                    {{ $committees->count() }} Members
                </span>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Position</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Department</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Contact</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Role</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($committees as $member)
                            <tr class="hover:bg-purple-50/30 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-bold text-gray-900">{{ $member->employee->full_name ?? $member->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $member->employee->email ?? $member->email }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $member->employee->position_title ?? '—' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $member->employee->department ?? '—' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $member->employee->mobile_number ?? $member->employee->phone_number ?? '—' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="bg-purple-100 text-purple-700 px-2 py-1 rounded text-xs font-bold">
                                        {{ $member->role ?? 'General Committee' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('events.participants.show', [$event, $member]) }}" class="text-purple-600 hover:text-purple-900 text-sm font-medium">Details</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-sm text-gray-400 italic">No committee members assigned.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ================= REGULAR PARTICIPANTS CARD ================= --}}
        <!-- <div class="bg-white shadow rounded-lg border overflow-hidden">
            <div class="px-6 py-4 border-b bg-gray-50 flex items-center justify-between">
                <h3 class="text-lg font-bold text-gray-900">Registered Participants</h3>
                <div class="flex gap-2">
                    @if(auth()->user()->isAdmin() || auth()->user()->hasPermissionTo('manage participants'))
                        <a href="{{ route('events.participants.create', $event) }}" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-blue-700">
                            Add Participant
                        </a>
                    @endif
                    <a href="{{ route('events.participants.index', $event) }}" class="text-sm font-medium text-blue-600 hover:text-blue-900 flex items-center">
                        View All →
                    </a>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($standardParticipants as $participant)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $participant->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $participant->email }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold uppercase 
                                        @switch($participant->status)
                                            @case('attended') bg-green-100 text-green-700 @break
                                            @case('absent') bg-red-100 text-red-700 @break
                                            @case('confirmed') bg-blue-100 text-blue-700 @break
                                            @default bg-yellow-100 text-yellow-700
                                        @endswitch">
                                        {{ $participant->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('events.participants.show', [$event, $participant]) }}" class="text-blue-600 hover:text-blue-900 text-sm font-medium">View</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-6 py-8 text-center text-sm text-gray-400 italic">No participants found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div> -->

        {{-- ================= LOGISTICS, CUSTODIAN & FINANCE (Logic derived from your previous code) ================= --}}
        @php
            $custodianCount = $event->custodianRequests->count();
            $financeApproved = $event->financeRequest && $event->financeRequest->status === 'approved';
            $custodianApproved = $custodianCount === 0 || $event->custodianRequests->where('status', '!=', 'approved')->count() === 0;
            $canApproveEvent = $financeApproved && $custodianApproved;
        @endphp

        <div class="grid md:grid-cols-3 gap-6">
            {{-- LOGISTICS --}}
            <div class="bg-white border rounded-lg p-5 shadow">
                <h4 class="font-bold mb-3 text-gray-800 border-b pb-2">Logistics Items</h4>
                <div class="space-y-3">
                    @forelse($event->logisticsItems as $item)
                        <div class="bg-gray-50 p-3 rounded-lg">
                            <div class="flex justify-between items-start">
                                <div class="min-w-0">
                                    <div class="text-sm font-medium text-gray-900">{{ $item->quantity }}× {{ $item->description }}</div>
                                </div>
                                <div class="text-right">
                                    <div class="font-semibold text-gray-900">₱{{ number_format($item->subtotal, 2) }}</div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs italic text-gray-400">No logistics items.</p>
                    @endforelse
                </div>
            </div>

            {{-- CUSTODIAN --}}
            <div class="bg-white border rounded-lg p-5 shadow">
                <h4 class="font-bold mb-3 text-gray-800 border-b pb-2">Custodian Equipment</h4>
                <div class="space-y-2">
                    @forelse($event->custodianRequests as $req)
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">{{ $req->custodianMaterial->name }} ({{ $req->quantity }})</span>
                            <span class="text-[10px] font-bold uppercase {{ $req->status === 'approved' ? 'text-green-600' : 'text-yellow-600' }}">
                                {{ $req->status }}
                            </span>
                        </div>
                    @empty
                        <p class="text-xs italic text-gray-400">No custodian items.</p>
                    @endforelse
                </div>
            </div>

            {{-- FINANCE --}}
            <div class="bg-white border rounded-lg p-5 shadow">
                <h4 class="font-bold mb-3 text-gray-800 border-b pb-2">Finance Summary</h4>
                @if($event->financeRequest)
                    <div class="space-y-1">
                        <p class="text-sm text-gray-600">Logistics: ₱{{ number_format($event->financeRequest->logistics_total, 2) }}</p>
                        <p class="text-lg font-bold text-indigo-600">Total: ₱{{ number_format($event->financeRequest->grand_total, 2) }}</p>
                        <span class="inline-block mt-2 text-[10px] font-bold px-2 py-0.5 rounded {{ $financeApproved ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                            {{ strtoupper($event->financeRequest->status) }}
                        </span>
                    </div>
                @else
                    <p class="text-xs italic text-gray-400">No finance request.</p>
                @endif
            </div>
        </div>

        {{-- ================= APPROVAL STATUS & ACTIONS ================= --}}
        <div class="bg-white border rounded-lg p-6 shadow">
            <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                <div class="flex gap-4 items-center">
                    <div class="flex flex-col">
                        <span class="text-xs font-bold text-gray-500 uppercase">Approval Readiness</span>
                        @if($canApproveEvent)
                            <span class="text-green-600 font-bold text-sm">✓ READY FOR FINAL APPROVAL</span>
                        @else
                            <span class="text-red-500 font-bold text-sm">⚠ PENDING DEPARTMENTAL APPROVALS</span>
                        @endif
                    </div>
                </div>

                <div class="flex gap-3">
                    <a href="{{ route('events.index') }}" class="px-4 py-2 text-sm font-semibold text-gray-600 hover:text-gray-900">
                        ← Back
                    </a>

                    @can('adjust events')
                        @if(in_array($event->status, ['pending_approval', 'pending_approvals']))
                            <a href="{{ route('events.edit', $event) }}" class="px-4 py-2 border rounded text-sm font-bold bg-white hover:bg-gray-50">
                                Edit
                            </a>
                        @endif
                    @endcan

                    @can('manage approvals')
                        @if(in_array($event->status, ['pending_approval', 'pending_approvals']))
                            @if($canApproveEvent)
                                <form action="{{ route('events.approve', $event) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="px-6 py-2 bg-green-600 text-white text-sm font-bold rounded shadow hover:bg-green-700 transition">
                                        Approve Event
                                    </button>
                                </form>
                            @else
                                <button disabled class="px-6 py-2 bg-gray-300 text-gray-500 text-sm font-bold rounded cursor-not-allowed">
                                    Approval Locked
                                </button>
                            @endif
                        @endif
                    @endcan
                </div>
            </div>
        </div>

    </div>
    </x-app-layout>