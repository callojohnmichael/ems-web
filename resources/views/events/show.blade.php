    <x-app-layout>
    <div class="max-w-6xl mx-auto py-6 sm:px-6 lg:px-8 space-y-6"
         x-data="{ toastOpen: {{ session('toast') ? 'true' : 'false' }} }"
         x-init="if (toastOpen) { setTimeout(() => toastOpen = false, 4000) }">

        {{-- TOAST (e.g. after suggest reschedule) --}}
        @if(session('toast'))
            <div x-show="toastOpen"
                 x-transition
                 x-cloak
                 class="fixed bottom-6 right-6 z-50 max-w-sm rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800 shadow-lg">
                <div class="flex items-start justify-between gap-3">
                    <div class="flex items-center gap-2">
                        <svg class="h-5 w-5 flex-shrink-0 text-green-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                        <span>{{ session('toast') }}</span>
                    </div>
                    <button type="button" @click="toastOpen = false" class="text-green-700 hover:text-green-900">
                        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                    </button>
                </div>
            </div>
        @endif

        {{-- SUCCESS MESSAGE --}}
        @if(session('success'))
            <div class="rounded-md bg-green-50 p-4 mb-6 border border-green-200">
                <p class="text-sm font-semibold text-green-800">
                    {{ session('success') }}
                </p>
            </div>
        @endif
        @if($errors->any())
            <div class="rounded-md bg-red-50 p-4 mb-6 border border-red-200">
                <ul class="text-sm text-red-700 list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
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

                <div class="flex items-center gap-2">
                    <a href="{{ route('support.index', ['event_id' => $event->id]) }}" class="rounded-lg border border-violet-200 bg-violet-50 px-3 py-1.5 text-sm font-semibold text-violet-700 hover:bg-violet-100">
                        Need Help?
                    </a>
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

        {{-- ================= RESCHEDULE REQUESTS (suggestions + status) ================= --}}
        @php
            $allRescheduleSuggestions = $event->rescheduleSuggestions;
            $pendingRescheduleSuggestions = $allRescheduleSuggestions->where('status', 'pending');
        @endphp
        <div class="bg-white shadow rounded-lg border overflow-hidden border-gray-200">
            <div class="px-6 py-4 border-b bg-gray-50 flex items-center justify-between flex-wrap gap-2">
                <h3 class="text-lg font-bold text-gray-900">Reschedule requests</h3>
                <div class="flex items-center gap-3">
                    @if($allRescheduleSuggestions->isNotEmpty())
                        <span class="text-xs text-gray-500">
                            {{ $allRescheduleSuggestions->count() }} total
                            @if($pendingRescheduleSuggestions->isNotEmpty())
                                · <span class="font-semibold text-amber-700">{{ $pendingRescheduleSuggestions->count() }} pending</span>
                            @endif
                        </span>
                    @endif
                    @can('suggestReschedule', $event)
                        <a href="{{ route('events.reschedule-suggestions.create', $event) }}" class="rounded-lg border border-amber-200 bg-amber-50 px-3 py-1.5 text-sm font-semibold text-amber-800 hover:bg-amber-100">
                            Suggest reschedule
                        </a>
                    @endcan
                </div>
            </div>
            <div class="overflow-x-auto">
                @if($allRescheduleSuggestions->isEmpty())
                    <div class="px-6 py-8 text-center text-sm text-gray-500">
                        No reschedule requests yet.
                        @can('suggestReschedule', $event)
                            <a href="{{ route('events.reschedule-suggestions.create', $event) }}" class="ml-1 text-indigo-600 hover:text-indigo-800 font-medium">Suggest reschedule</a>
                        @endcan
                    </div>
                @else
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Suggested by</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Suggested dates</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Reason</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                                @can('update', $event)
                                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Actions</th>
                                @endcan
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($allRescheduleSuggestions as $suggestion)
                                <tr class="hover:bg-gray-50/50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $suggestion->requestedBy->name ?? '—' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                        {{ $suggestion->suggested_start_at->format('M j, Y g:i A') }} – {{ $suggestion->suggested_end_at->format('g:i A') }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600 max-w-xs">
                                        {{ Str::limit($suggestion->reason, 80) ?: '—' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($suggestion->status === 'pending')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">Pending</span>
                                        @elseif($suggestion->status === 'accepted')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Accepted</span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Declined</span>
                                        @endif
                                    </td>
                                    @can('update', $event)
                                        <td class="px-6 py-4 text-right whitespace-nowrap">
                                            @if($suggestion->status === 'pending')
                                                <form action="{{ route('reschedule-suggestions.accept', $suggestion) }}" method="POST" class="inline reschedule-accept-form">
                                                    @csrf
                                                    <button type="button" class="reschedule-accept-btn text-green-600 hover:text-green-800 text-sm font-semibold mr-3">Accept</button>
                                                </form>
                                                <form action="{{ route('reschedule-suggestions.decline', $suggestion) }}" method="POST" class="inline reschedule-decline-form">
                                                    @csrf
                                                    <button type="button" class="reschedule-decline-btn text-red-600 hover:text-red-800 text-sm font-semibold">Decline</button>
                                                </form>
                                            @else
                                                <span class="text-gray-400 text-sm">—</span>
                                            @endif
                                        </td>
                                    @endcan
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
        @can('update', $event)
            @if($pendingRescheduleSuggestions->isNotEmpty())
                @push('styles')
                <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
                @endpush
                @push('scripts')
                <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        document.querySelectorAll('.reschedule-accept-btn').forEach(function(btn) {
                            btn.addEventListener('click', function() {
                                var form = this.closest('form');
                                if (typeof Swal === 'undefined') {
                                    if (confirm('Accept this reschedule suggestion? The event dates will be updated and the requester will be notified.')) form.submit();
                                    return;
                                }
                                Swal.fire({
                                    title: 'Accept reschedule suggestion?',
                                    text: 'The event dates will be updated and the requester will be notified.',
                                    icon: 'question',
                                    showCancelButton: true,
                                    confirmButtonColor: '#16a34a',
                                    cancelButtonColor: '#6b7280',
                                    confirmButtonText: 'Yes, accept'
                                }).then(function(result) { if (result.isConfirmed) form.submit(); });
                            });
                        });
                        document.querySelectorAll('.reschedule-decline-btn').forEach(function(btn) {
                            btn.addEventListener('click', function() {
                                var form = this.closest('form');
                                if (typeof Swal === 'undefined') {
                                    if (confirm('Decline this reschedule suggestion?')) form.submit();
                                    return;
                                }
                                Swal.fire({
                                    title: 'Decline reschedule suggestion?',
                                    text: 'The suggestion will be marked as declined.',
                                    icon: 'warning',
                                    showCancelButton: true,
                                    confirmButtonColor: '#dc2626',
                                    cancelButtonColor: '#6b7280',
                                    confirmButtonText: 'Yes, decline'
                                }).then(function(result) { if (result.isConfirmed) form.submit(); });
                            });
                        });
                    });
                </script>
                @endpush
            @endif
        @endcan

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

        {{-- ================= DOCUMENTS SECTION ================= --}}
        <div class="bg-white border rounded-lg p-6 shadow">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                    Event Documents
                </h3>
                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.documents.create') }}?event_id={{ $event->id }}" 
                       class="px-3 py-1.5 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors">
                        ➕ Upload Document
                    </a>
                    <a href="{{ route('admin.documents.index') }}?event_id={{ $event->id }}" 
                       class="px-3 py-1.5 border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">
                        View All
                    </a>
                </div>
            </div>

            @php
                $eventDocuments = \App\Models\Document::where('event_id', $event->id)
                    ->with('user')
                    ->orderByDesc('created_at')
                    ->limit(5)
                    ->get();
            @endphp

            @if($eventDocuments->count() > 0)
                <div class="space-y-3">
                    @foreach($eventDocuments as $document)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                            <div class="flex items-center space-x-3">
                                <!-- Document Icon -->
                                <div class="p-2 bg-white rounded-lg border">
                                    @switch($document->type)
                                        @case('attendance')
                                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            @break
                                        @case('event')
                                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                            @break
                                        @case('policy')
                                            <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                            @break
                                        @case('report')
                                            <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                            @break
                                        @default
                                            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                            </svg>
                                    @endswitch
                                </div>

                                <!-- Document Info -->
                                <div>
                                    <a href="{{ route('admin.documents.show', $document) }}" 
                                       class="font-medium text-gray-900 hover:text-indigo-600 transition-colors">
                                        {{ $document->title }}
                                    </a>
                                    <div class="flex items-center space-x-2 text-xs text-gray-500 mt-1">
                                        <span>{{ $document->formatted_file_size }}</span>
                                        <span>•</span>
                                        <span>{{ $document->created_at->diffForHumans() }}</span>
                                        @if($document->user)
                                            <span>•</span>
                                            <span>{{ $document->user->name }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="flex items-center space-x-2">
                                @if($document->is_attendance_document)
                                    <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium bg-green-100 text-green-800">
                                        Attendance Report
                                    </span>
                                @endif
                                <a href="{{ route('admin.documents.download', $document) }}" 
                                   class="p-1.5 text-gray-400 hover:text-indigo-600 transition-colors"
                                   title="Download">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>

                @if($eventDocuments->count() >= 5)
                    <div class="mt-4 text-center">
                        <a href="{{ route('admin.documents.index') }}?event_id={{ $event->id }}" 
                           class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                            View all documents →
                        </a>
                    </div>
                @endif
            @else
                <div class="text-center py-8">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No documents yet</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        Upload documents related to this event such as presentations, reports, or attendance records.
                    </p>
                    <div class="mt-4">
                        <a href="{{ route('admin.documents.create') }}?event_id={{ $event->id }}" 
                           class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                            ➕ Upload First Document
                        </a>
                    </div>
                </div>
            @endif
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
