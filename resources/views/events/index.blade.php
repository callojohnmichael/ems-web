<x-app-layout>
<div class="max-w-7xl mx-auto py-8 sm:px-6 lg:px-8">

    {{-- ================= HEADER ================= --}}
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
                    View your requested events and upcoming schedules.
                @endrole
            </p>
        </div>

        <div class="flex gap-2">
            @if($canManageVenues)
                <a href="{{ route('admin.venues.index') }}"
                   class="inline-flex items-center rounded-lg bg-gray-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-gray-700 transition">
                    🏢 Manage Venues
                </a>
            @endif

            @if($canManageParticipants)
                <a href="{{ route('admin.participants.index') }}"
                   class="inline-flex items-center rounded-lg bg-purple-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-purple-700 transition">
                    👥 Manage Participants
                </a>
            @endif

            <a href="{{ route('events.create') }}"
               class="inline-flex items-center rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 transition">
                + Request New Event
            </a>
        </div>
    </div>

    {{-- ================= SEARCH BAR ================= --}}
    <div class="mb-6">
        <form action="{{ route('events.index') }}" method="GET" class="flex gap-2">
            <input type="text" 
                   name="search" 
                   value="{{ $search ?? '' }}"
                   placeholder="Search events by title, description, or venue..."
                   class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            <button type="submit" 
                    class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                Search
            </button>
            @if($search ?? false)
                <a href="{{ route('events.index') }}" 
                   class="px-6 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">
                    Clear
                </a>
            @endif
        </form>
    </div>

    {{-- ================= EVENTS LIST ================= --}}
    <div class="bg-white shadow-sm ring-1 ring-gray-900/5 sm:rounded-xl overflow-hidden">

        @forelse($events as $event)

            @php
                // ================= COUNTS =================
                $committeeCount = $event->participants->count();
                $logisticsCount = $event->logisticsItems->count();
                $budgetCount = $event->budget->count();

                // ================= TOTALS =================
                $logisticsTotal = $event->logisticsItems->sum('subtotal');
                $budgetTotal = $event->budget->sum('estimated_amount');

                $grandTotal = $logisticsTotal + $budgetTotal;

                // ================= APPROVAL CONDITIONS =================

                // Finance request must exist AND be approved
                $financeApproved =
                    $event->financeRequest &&
                    strtolower(trim($event->financeRequest->status)) === 'approved';

                // Custodian requests:
                // - If none exists, treat as approved
                // - If exists, all must be approved
                $custodianCount = $event->custodianRequests->count();

                $custodianApproved = true;
                if ($custodianCount > 0) {
                    $custodianApproved = $event->custodianRequests
                        ->where('status', '!=', 'approved')
                        ->count() === 0;
                }

                // Final requirement before event can be approved (only Finance + Custodian)
                $canApproveEvent = $financeApproved && $custodianApproved;

                // Build a readable blocked message
                $blockedReasons = [];

                if (!$event->financeRequest) {
                    $blockedReasons[] = "Finance request is missing.";
                } elseif (!$financeApproved) {
                    $blockedReasons[] = "Finance request is not yet approved.";
                }
                if ($custodianCount > 0 && !$custodianApproved) {
                    $blockedReasons[] = "Custodian request(s) are not yet approved.";
                }

                $blockedText = implode(" ", $blockedReasons);
            @endphp

            <div class="relative flex flex-col gap-6 px-6 py-6 border-b last:border-0 hover:bg-gray-50 lg:flex-row lg:items-center">

                {{-- LEFT CONTENT --}}
                <div class="flex-1 min-w-0">

                    {{-- TITLE + STATUS --}}
                    <div class="flex items-center gap-3">
                        <h2 class="text-lg font-bold text-gray-900">
                            {{ $event->title }}
                        </h2>

                        <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset
                            @switch($event->status)
                                @case('pending_approval') bg-yellow-50 text-yellow-800 ring-yellow-600/20 @break
                                @case('pending_approvals') bg-yellow-50 text-yellow-800 ring-yellow-600/20 @break
                                @case('approved') bg-blue-50 text-blue-700 ring-blue-700/10 @break
                                @case('published') bg-green-50 text-green-700 ring-green-600/20 @break
                                @case('completed') bg-gray-50 text-gray-700 ring-gray-600/20 @break
                                @case('cancelled') bg-red-50 text-red-700 ring-red-600/20 @break
                                @default bg-gray-50 text-gray-600 ring-gray-500/10
                            @endswitch">
                            {{ Str::headline($event->status) }}
                        </span>
                    </div>

                    {{-- META INFO --}}
                    <div class="mt-2 space-y-1 text-sm text-gray-500">
                        <div>
                            📍 {{ optional($event->venue)->name ?? 'Venue TBD' }}
                        </div>
                        <div>
                            🗓 {{ $event->start_at->format('M d, Y') }}
                            |
                            {{ $event->start_at->format('h:i A') }} –
                            {{ $event->end_at->format('h:i A') }}
                        </div>
                    </div>

                    {{-- STATS --}}
                    <div class="mt-4 flex flex-wrap gap-2">

                        <span class="badge">
                            Expected Participants: {{ $event->number_of_participants ?? 0 }}
                        </span>

                        <span class="badge">
                            <a href="{{ route('events.participants.index', $event) }}" class="hover:underline">
                                Registered: {{ $committeeCount }}
                            </a>
                        </span>

                        <span class="badge">
                            Logistics: {{ $logisticsCount }}
                        </span>

                        <span class="badge">
                            Budget: {{ $budgetCount }}
                        </span>

                        @if($logisticsTotal > 0)
                            <span class="badge bg-orange-50 text-orange-700 font-semibold">
                                Logistics:
                                ₱{{ number_format($logisticsTotal, 2) }}
                            </span>
                        @endif

                        @if($budgetTotal > 0)
                            <span class="badge bg-indigo-50 text-indigo-800 font-semibold">
                                Budget:
                                ₱{{ number_format($budgetTotal, 2) }}
                            </span>
                        @endif

                        @if($grandTotal > 0)
                            <span class="badge bg-purple-50 text-purple-700 font-semibold">
                                Grand Total:
                                ₱{{ number_format($grandTotal, 2) }}
                            </span>
                        @endif
                    </div>

                    {{-- ================= APPROVAL STATUS ================= --}}
                    <div class="mt-3 flex flex-wrap gap-2 text-xs">

                        <span class="badge {{ $financeApproved ? 'bg-green-50 text-green-700' : 'bg-yellow-50 text-yellow-700' }}">
                            Finance Request: {{ $financeApproved ? 'Approved' : 'Pending' }}
                        </span>

                        <span class="badge {{ $custodianApproved ? 'bg-green-50 text-green-700' : 'bg-yellow-50 text-yellow-700' }}">
                            Custodian: {{ $custodianApproved ? 'Approved' : 'Pending' }}
                        </span>
                    </div>

                </div>

                {{-- RIGHT CONTENT --}}
                <div class="flex flex-col items-end gap-3 lg:ml-6">
                    <div class="flex gap-2">
                        <a href="{{ route('events.show', $event) }}" 
                           class="inline-flex items-center px-3 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50 transition">
                            View
                        </a>
                        
                        @if(auth()->user()->isAdmin())
                            @if($event->status === 'pending_approvals' && $canApproveEvent)
                                <form action="{{ route('events.approve', $event) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" 
                                            class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-white bg-green-600 hover:bg-green-700 transition">
                                        Approve
                                    </button>
                                </form>
                            @endif

                            @if($event->status === 'approved')
                                <form action="{{ route('events.publish', $event) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit"
                                            class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-white bg-blue-600 hover:bg-blue-700 transition">
                                        Publish
                                    </button>
                                </form>
                            @endif
                        @endif
                    </div>
                </div>

            </div>

        @empty

            <div class="p-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No events found</h3>
                <p class="mt-1 text-sm text-gray-500">Get started by creating a new event request.</p>
                <div class="mt-6">
                    <a href="{{ route('events.create') }}"
                       class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 transition">
                        + Request New Event
                    </a>
                </div>
            </div>

        @endforelse

    </div>
</div>
</x-app-layout>

{{-- ================= REUSABLE BUTTON STYLES ================= --}}
<style>
.badge{ @apply px-2 py-1 text-xs font-medium bg-gray-100 text-gray-600 rounded-md; }
.btn-primary{ @apply px-3 py-1.5 bg-indigo-600 text-white text-xs rounded-md hover:bg-indigo-500; }
.btn-success{ @apply px-3 py-1.5 bg-green-600 text-white text-xs rounded-md hover:bg-green-500; }
.btn-danger{ @apply px-3 py-1.5 bg-red-600 text-white text-xs rounded-md hover:bg-red-500; }
.btn-danger-outline{ @apply px-3 py--1.5 border border-red-500 text-red-600 text-xs rounded-md hover:bg-red-50; }
.btn-secondary{ @apply px-3 py-1.5 bg-gray-100 text-gray-700 text-xs rounded-md hover:bg-gray-200; }
.btn-disabled{ @apply px-3 py-1.5 bg-gray-100 text-gray-400 text-xs rounded-md cursor-not-allowed; }
</style>
