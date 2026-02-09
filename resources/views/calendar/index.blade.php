<x-app-layout>

{{-- ================= STYLES ================= --}}
@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/main.min.css">

<style>
    [x-cloak] { display: none !important; }

    #calendar {
        min-height: 700px;
        background: #fff;
        padding: 15px;
        border-radius: 0.75rem;
    }

    .fc-daygrid-day { cursor: pointer; }
    .fc-daygrid-day:hover { background-color: rgba(243,244,246,.5); }

    .fc .fc-button-primary {
        background-color: #4f46e5;
        border-color: #4f46e5;
    }
    .fc .fc-button-primary:hover {
        background-color: #4338ca;
        border-color: #4338ca;
    }
</style>
@endpush

{{-- ================= DATA ================= --}}
@php
    $eventsData = $events->map(function ($e) {
        switch ($e->status) {
            case 'published':        $bg='#d1fae5'; $border='#10b981'; break;
            case 'approved':         $bg='#dbeafe'; $border='#3b82f6'; break;
            case 'pending_approval': $bg='#fef3c7'; $border='#f59e0b'; break;
            case 'rejected':         $bg='#fee2e2'; $border='#ef4444'; break;
            default:                 $bg='#f3f4f6'; $border='#9ca3af';
        }

        return [
            'id' => $e->id,
            'title' => $e->title,
            'start' => str_replace(' ', 'T', $e->start_at),
            'end'   => str_replace(' ', 'T', $e->end_at),
            'status'=> ucwords(str_replace('_',' ', $e->status)),
            'backgroundColor' => $bg,
            'borderColor'     => $border,
            'textColor'       => '#1f2937',
        ];
    });
@endphp

{{-- ================= CONTENT ================= --}}
<div
    class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8"
    x-data="calendarData()"
    x-init="init()"
>

    {{-- HEADER --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <h1 class="text-2xl font-bold text-gray-900">
            {{ auth()->user()->isAdmin() ? 'Event Administration Calendar' : 'Events Schedule' }}
        </h1>

        <div class="flex gap-2">
            @if(auth()->user()->isAdmin())
                <button
                    @click="downloadTemplate()"
                    class="px-4 py-2 border rounded bg-white text-sm hover:bg-gray-50">
                    CSV Template
                </button>

                <button
                    @click="showUploadModal = true"
                    class="px-4 py-2 rounded bg-indigo-600 text-white hover:bg-indigo-700">
                    Bulk Upload
                </button>
            @endif

            @if(auth()->user()->isUser())
                <a
                    href="{{ route('events.create') }}"
                    class="px-4 py-2 rounded bg-green-600 text-white hover:bg-green-700">
                    + Request Event
                </a>
            @endif
        </div>
    </div>

    {{-- LEGEND --}}
    <div class="bg-white border rounded-lg p-4 mb-6 flex flex-wrap gap-4 justify-center">
        <span class="flex items-center gap-2 text-xs">
            <span class="w-3 h-3 bg-yellow-100 border border-yellow-400 rounded"></span> Pending
        </span>
        <span class="flex items-center gap-2 text-xs">
            <span class="w-3 h-3 bg-blue-100 border border-blue-400 rounded"></span> Approved
        </span>
        <span class="flex items-center gap-2 text-xs">
            <span class="w-3 h-3 bg-green-100 border border-green-400 rounded"></span> Published
        </span>
        <span class="flex items-center gap-2 text-xs">
            <span class="w-3 h-3 bg-red-100 border border-red-400 rounded"></span> Rejected
        </span>
    </div>

    {{-- CALENDAR --}}
    <div class="bg-white border rounded-xl shadow p-4">
        <div id="calendar" wire:ignore></div>
    </div>

    {{-- DAY MODAL --}}
    <div x-show="showDayModal" x-cloak class="fixed inset-0 z-50">
        <div class="absolute inset-0 bg-black/50" @click="showDayModal=false"></div>

        <div class="relative bg-white max-w-lg mx-auto mt-24 rounded-xl shadow-xl p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="font-bold text-lg">
                    Events on <span x-text="selectedDate" class="text-indigo-600"></span>
                </h3>
                <button @click="showDayModal=false" class="text-gray-500">&times;</button>
            </div>

            <template x-for="event in dayEvents" :key="event.id">
                <a
                    :href="`/events/${event.id}`"
                    class="block mb-3 p-4 rounded border-l-4"
                    :style="`background:${event.backgroundColor};border-color:${event.borderColor}`">
                    <div class="flex justify-between">
                        <span class="font-semibold" x-text="event.title"></span>
                        <span class="text-xs uppercase font-bold" x-text="event.status"></span>
                    </div>
                </a>
            </template>

            <template x-if="dayEvents.length === 0">
                <p class="text-center text-gray-400 italic py-6">
                    No events scheduled.
                </p>
            </template>
        </div>
    </div>

    {{-- BULK UPLOAD --}}
    @if(auth()->user()->isAdmin())
    <div x-show="showUploadModal" x-cloak class="fixed inset-0 z-50">
        <div class="absolute inset-0 bg-black/60" @click="showUploadModal=false"></div>

        <div class="relative bg-white max-w-md mx-auto mt-24 rounded-xl shadow-xl">
            <form method="POST" action="{{ route('events.bulk-upload') }}" enctype="multipart/form-data">
                @csrf

                <div class="p-6">
                    <h3 class="font-bold text-lg mb-2">Import Events (CSV)</h3>
                    <input type="file" name="file" required accept=".csv" class="w-full">
                </div>

                <div class="bg-gray-50 px-6 py-4 flex justify-end gap-2">
                    <button type="button" @click="showUploadModal=false">Cancel</button>
                    <button class="bg-indigo-600 text-white px-4 py-2 rounded">Upload</button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>

{{-- ================= SCRIPTS ================= --}}
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>

<script>
function calendarData() {
    return {
        showDayModal: false,
        showUploadModal: false,
        selectedDate: '',
        dayEvents: [],

        init() {
            const calendar = new FullCalendar.Calendar(
                document.getElementById('calendar'), {
                    initialView: 'dayGridMonth',
                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'dayGridMonth,timeGridWeek'
                    },
                    dayMaxEvents: 3,
                    events: @json($eventsData),

                    dateClick: info => {
                        this.selectedDate = info.dateStr;
                        this.dayEvents = @json($eventsData)
                            .filter(e => e.start.startsWith(info.dateStr));
                        this.showDayModal = true;
                    },

                    eventClick: info => {
                        window.location = `/events/${info.event.id}`;
                    }
                }
            );

            calendar.render();
        },

        downloadTemplate() {
            const csv = [
                ['title','start_at','end_at','description','venue_id'],
                ['Sample Event','2026-03-20 09:00:00','2026-03-20 11:00:00','Description','1']
            ].map(r => r.join(',')).join('\n');

            const a = document.createElement('a');
            a.href = URL.createObjectURL(new Blob([csv], { type:'text/csv' }));
            a.download = 'event_template.csv';
            a.click();
        }
    }
}
</script>
@endpush

</x-app-layout>
