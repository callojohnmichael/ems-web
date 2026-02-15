<x-app-layout>

{{-- ================= STYLES ================= --}}
@push('styles')
<style>
    [x-cloak] { display: none !important; }

    #calendar {
        min-height: 700px;
        background: #fff;
        padding: 15px;
        border-radius: 0.75rem;
    }

    .calendar-venue {
        min-height: 400px;
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

    .venue-calendar-card {
        border-left: 4px solid #4f46e5;
    }
    .venue-jump-link {
        scroll-margin-top: 6rem;
    }
</style>
@endpush

{{-- ================= CONTENT ================= --}}
<script>
window.__calendarPayload = @json($calendarPayload);
</script>
<div
    class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8"
    x-data="calendarData(window.__calendarPayload)"
    x-init="init(); scrollToVenue()"
>

    {{-- HEADER --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <h1 class="text-2xl font-bold text-gray-900">
            {{ auth()->user()->isAdmin() ? 'Event Administration Calendar' : 'Events Schedule' }}
        </h1>

        <div class="flex flex-wrap gap-2">
            <a href="{{ route('calendar.export', ['format' => 'csv']) }}"
               class="px-4 py-2 border rounded bg-white text-sm hover:bg-gray-50">
                Export all (CSV)
            </a>
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

    {{-- MAIN CALENDAR (all events) --}}
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-4 mb-10">
        <div id="calendar" wire:ignore></div>
    </div>

    {{-- CALENDARS BY VENUE --}}
    @if($venues->isNotEmpty())
    <div class="border-t border-gray-200 pt-8 mt-10">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <h2 class="text-xl font-semibold text-gray-900">Calendars by venue</h2>
                <p class="mt-1 text-sm text-gray-500">View and export events per venue below.</p>
            </div>
            <nav class="flex flex-wrap gap-2" aria-label="Jump to venue">
                @foreach($venues as $venue)
                    <a href="#venue-{{ $venue->id }}"
                       class="venue-jump-link inline-flex items-center rounded-full bg-indigo-50 px-3 py-1.5 text-sm font-medium text-indigo-700 hover:bg-indigo-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-1">
                        {{ $venue->name }}
                    </a>
                @endforeach
            </nav>
        </div>

        @foreach($venues as $venue)
        <section id="venue-{{ $venue->id }}" class="venue-jump-link venue-calendar-card mb-8 scroll-mt-24 bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 px-5 py-4 bg-gray-50 border-b border-gray-200">
                <div class="min-w-0">
                    <h3 class="text-lg font-semibold text-gray-900 truncate">{{ $venue->name }}</h3>
                    @if($venue->address)
                        <p class="mt-0.5 text-sm text-gray-500 truncate">{{ $venue->address }}</p>
                    @endif
                </div>
                <div class="flex flex-wrap items-center gap-2 shrink-0">
                    @can('viewAny', \App\Models\Venue::class)
                        <a href="{{ route('admin.venues.show', $venue) }}"
                           class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-1">
                            View events
                        </a>
                    @endcan
                    <a href="{{ route('calendar.export', ['format' => 'csv', 'venue' => $venue->id]) }}"
                       class="inline-flex items-center rounded-lg border border-indigo-200 bg-indigo-50 px-3 py-2 text-sm font-medium text-indigo-700 hover:bg-indigo-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-1">
                        Export CSV
                    </a>
                </div>
            </div>
            <div class="p-4">
                <div id="calendar-venue-{{ $venue->id }}" class="calendar-venue" wire:ignore></div>
            </div>
        </section>
        @endforeach
    </div>
    @else
        <div class="rounded-xl border border-gray-200 bg-gray-50 p-8 text-center">
            <p class="text-gray-600">No venues yet. Add venues in venue management to see calendars per venue.</p>
            @can('viewAny', \App\Models\Venue::class)
                <a href="{{ route('admin.venues.index') }}" class="mt-3 inline-flex items-center text-sm font-medium text-indigo-600 hover:text-indigo-800">Go to venue management →</a>
            @endcan
        </div>
    @endif

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
                    :href="event.url || ('/events/' + event.id)"
                    class="block mb-3 p-4 rounded border-l-4"
                    :style="'background:#f3f4f6;border-color:' + (event.color || '#9ca3af')">
                    <div class="flex justify-between">
                        <span class="font-semibold" x-text="event.title"></span>
                        <span class="text-xs uppercase font-bold" x-text="(event.status || '').replace('_',' ')"></span>
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
            <form method="POST" action="{{ route('admin.events.bulk-upload') }}" enctype="multipart/form-data">
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
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.20/index.global.min.js"></script>
<script>
function calendarData(payload) {
    const mainCalendarEvents = Array.isArray(payload.mainCalendarEvents) ? payload.mainCalendarEvents : [];
    const venues = Array.isArray(payload.venues) ? payload.venues : [];
    const eventsByVenue = payload.eventsByVenue && typeof payload.eventsByVenue === 'object' ? payload.eventsByVenue : {};

    return {
        showDayModal: false,
        showUploadModal: false,
        selectedDate: '',
        dayEvents: [],

        scrollToVenue() {
            const hash = window.location.hash;
            if (hash && hash.startsWith('#venue-')) {
                const el = document.querySelector(hash);
                if (el) el.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        },

        init() {
            this.$nextTick(() => this.renderCalendars());
        },

        renderCalendars() {
            const FC = window.FullCalendar;
            const CalendarCtor = (FC && FC.FullCalendar && FC.FullCalendar.Calendar) ? FC.FullCalendar.Calendar : (FC && FC.Calendar ? FC.Calendar : null);

            if (!CalendarCtor) {
                console.error('FullCalendar not found.');
                return;
            }

            const plugins = FC && FC.dayGridPlugin ? [FC.dayGridPlugin, FC.timeGridPlugin, FC.interactionPlugin] : [];

            const baseOpts = {
                initialView: 'dayGridMonth',
                headerToolbar: { left: 'prev,next today', center: 'title', right: 'dayGridMonth,timeGridWeek' },
                dayMaxEvents: 3
            };
            if (plugins.length) baseOpts.plugins = plugins;

            // Main calendar (all events)
            const mainEl = document.getElementById('calendar');
            if (mainEl) {
                const mainCal = new CalendarCtor(mainEl, {
                    ...baseOpts,
                    events: mainCalendarEvents,
                    dateClick: (info) => {
                        this.selectedDate = info.dateStr;
                        this.dayEvents = mainCalendarEvents.filter(e => e.start && String(e.start).startsWith(info.dateStr));
                        this.showDayModal = true;
                    },
                    eventClick: (info) => { window.location = info.event.url || ('/events/' + info.event.id); }
                });
                mainCal.render();
            }

            // Per-venue calendars
            venues.forEach(venue => {
                const el = document.getElementById('calendar-venue-' + venue.id);
                if (!el) return;
                const events = eventsByVenue[venue.id] || [];
                const venueId = venue.id;
                const cal = new CalendarCtor(el, {
                    ...baseOpts,
                    events: events,
                    dateClick: (info) => {
                        this.selectedDate = info.dateStr;
                        this.dayEvents = (eventsByVenue[venueId] || []).filter(e => e.start && String(e.start).startsWith(info.dateStr));
                        this.showDayModal = true;
                    },
                    eventClick: (info) => { window.location = info.event.url || ('/events/' + info.event.id); }
                });
                cal.render();
            });
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
    };
}
</script>
@endpush

</x-app-layout>
