<x-app-layout>
<div class="max-w-7xl mx-auto py-8 sm:px-6 lg:px-8"
     x-data="eventIndex()">

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

                        <!-- <span class="badge">
                            <a href="{{ route('events.participants.index', $event) }}" class="hover:underline">
                                Registered: {{ $committeeCount }}
                            </a>
                        </span> -->

                        <span class="badge">
                            Logistics: {{ $logisticsCount }}
                        </span>

                        <span class="badge">
                            Budget: {{ $budgetCount }}
                        </span>

                        @if($logisticsTotal > 0)
                            <span class="badge bg-green-50 text-green-700">
                                Logistics Total:
                                ₱{{ number_format($logisticsTotal, 2) }}
                            </span>
                        @endif

                        @if($budgetTotal > 0)
                            <span class="badge bg-indigo-50 text-indigo-700">
                                Budget Total:
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

                {{-- RIGHT ACTIONS --}}
                <div class="flex flex-wrap gap-2 lg:mt-0">

                    <a href="{{ route('events.show', $event) }}"
                       class="btn-secondary text-violet-600">
                        View Details
                    </a>

                        <!-- @if($canManageParticipants)
                            <a href="{{ route('events.participants.index', $event) }}"
                            class="btn-secondary text-purple-600">
                                👥 Participants ({{ $committeeCount }})
                            </a>
                        @endif -->

                    @role('admin')

                        {{-- ================= APPROVE/REJECT (PENDING ONLY) ================= --}}
                        @if($event->status === 'pending_approval' || $event->status === 'pending_approvals')

                            {{-- APPROVE BUTTON (BLOCKED UNTIL REQUESTS APPROVED) --}}
                            @if($canApproveEvent)

                                <button
                                    @click="setupModal(
                                        '{{ route('events.approve', $event) }}',
                                        'Approve Event Request',
                                        'Confirm approval? This will mark the event as approved and ready to publish.',
                                        'Approve',
                                        'green'
                                    )"
                                    class="btn-success">
                                    Approve
                                </button>

                            @else

                                <button
                                    @click="setupModal(
                                        '',
                                        'Approval Blocked',
                                        '{{ $blockedText ?: "Some required approvals are still pending." }}',
                                        'Understood',
                                        'gray',
                                        false,
                                        true
                                    )"
                                    class="btn-disabled">
                                    Approve
                                </button>

                            @endif


                            {{-- REJECT BUTTON ALWAYS ALLOWED --}}
                            <button
                                @click="setupModal(
                                    '{{ route('events.reject', $event) }}',
                                    'Reject Event',
                                    'This will cancel the request and remove associated logistics and budget records.',
                                    'Reject',
                                    'red'
                                )"
                                class="btn-danger">
                                Reject
                            </button>

                        @endif

                        {{-- ================= PUBLISH (APPROVED ONLY) ================= --}}
                        @if($event->status === 'approved')

                            <button
                                @click="setupModal(
                                    '{{ route('events.publish', $event) }}',
                                    'Publish Event',
                                    'This makes the event visible in the public calendar.',
                                    'Publish Now',
                                    'indigo'
                                )"
                                class="btn-primary">
                                Publish
                            </button>

                        @endif

                        {{-- ================= DELETE ================= --}}
                        @if($event->status === 'published')

                            <button
                                @click="setupModal(
                                    '',
                                    'Action Blocked',
                                    'Published events cannot be deleted. Unpublish first.',
                                    'Understood',
                                    'gray',
                                    false,
                                    true
                                )"
                                class="btn-disabled">
                                Delete
                            </button>

                        @else

                            <button
                                @click="setupModal(
                                    '{{ route('events.destroy', $event) }}',
                                    'Delete Event',
                                    'Are you sure? This will permanently delete all related logistics and budget data.',
                                    'Delete Permanently',
                                    'red',
                                    true
                                )"
                                class="btn-danger-outline text-red-600">
                                Delete
                            </button>

                        @endif

                    @endrole
                </div>
            </div>

        @empty
            <div class="text-center py-12 text-gray-500">
                No events found.
            </div>
        @endforelse

    </div>

    {{-- ================= MODAL ================= --}}
    <div x-show="showModal"
         x-transition
         class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/75 p-4"
         x-cloak>

        <div class="bg-white rounded-xl shadow-xl max-w-md w-full"
             @click.away="showModal = false">

            <div class="p-6">
                <h2 class="text-lg font-bold text-gray-900" x-text="modalTitle"></h2>
                <p class="mt-3 text-sm text-gray-600" x-text="modalBody"></p>
            </div>

            <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3">
                <button @click="showModal=false"
                        class="px-4 py-2 text-sm bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                    Cancel
                </button>

                <template x-if="!isInfoOnly">
                    <form :action="actionUrl" method="POST">
                        @csrf
                        <template x-if="isDelete">
                            <input type="hidden" name="_method" value="DELETE">
                        </template>

                        <button type="submit"
                                class="px-4 py-2 text-sm font-bold text-white rounded-lg"
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

{{-- ================= ALPINE ================= --}}
<script>
function eventIndex(){
    return {
        showModal:false,
        actionUrl:'',
        modalTitle:'',
        modalBody:'',
        modalButtonText:'',
        modalColor:'indigo',
        isDelete:false,
        isInfoOnly:false,

        setupModal(url,title,body,btnText,color,isDeleteAction=false,isInfo=false){
            this.actionUrl = url;
            this.modalTitle = title;
            this.modalBody = body;
            this.modalButtonText = btnText;
            this.modalColor = color;
            this.isDelete = isDeleteAction;
            this.isInfoOnly = isInfo;
            this.showModal = true;
        }
    }
}
</script>

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

</x-app-layout>
