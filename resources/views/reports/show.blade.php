@php
    $tabs = [
        ['label' => 'Overview', 'route' => 'reports.index', 'section' => 'overview'],
        ['label' => 'Pipeline', 'route' => 'reports.pipeline', 'section' => 'pipeline'],
        ['label' => 'Participants', 'route' => 'reports.participants', 'section' => 'participants'],
        ['label' => 'Venues', 'route' => 'reports.venues', 'section' => 'venues'],
        ['label' => 'Finance', 'route' => 'reports.finance', 'section' => 'finance'],
        ['label' => 'Engagement', 'route' => 'reports.engagement', 'section' => 'engagement'],
        ['label' => 'Multimedia', 'route' => 'reports.multimedia', 'section' => 'multimedia'],
        ['label' => 'Support', 'route' => 'reports.support', 'section' => 'support'],
    ];
    $currentRoute = match ($section) {
        'overview' => 'reports.index',
        'pipeline' => 'reports.pipeline',
        'participants' => 'reports.participants',
        'venues' => 'reports.venues',
        'finance' => 'reports.finance',
        'engagement' => 'reports.engagement',
        'multimedia' => 'reports.multimedia',
        'support' => 'reports.support',
        default => 'reports.index',
    };
@endphp

<x-app-layout>
    <div class="space-y-6">
        <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">{{ $title }}</h2>
                    <p class="text-sm text-gray-500">Date range: {{ $startDate }} to {{ $endDate }}</p>
                </div>

                <form method="GET" action="{{ route($currentRoute) }}" class="flex flex-wrap items-end gap-3">
                    <div>
                        <label for="start_date" class="block text-xs font-medium uppercase tracking-wide text-gray-500">Start</label>
                        <input id="start_date" name="start_date" type="date" value="{{ $startDate }}" class="rounded-lg border-gray-300 text-sm focus:border-violet-500 focus:ring-violet-500">
                    </div>
                    <div>
                        <label for="end_date" class="block text-xs font-medium uppercase tracking-wide text-gray-500">End</label>
                        <input id="end_date" name="end_date" type="date" value="{{ $endDate }}" class="rounded-lg border-gray-300 text-sm focus:border-violet-500 focus:ring-violet-500">
                    </div>
                    <button type="submit" class="rounded-lg bg-violet-600 px-4 py-2 text-sm font-medium text-white hover:bg-violet-700">
                        Apply
                    </button>
                    <a href="{{ route('reports.export', ['section' => $section, 'start_date' => $startDate, 'end_date' => $endDate]) }}" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100">
                        Export CSV
                    </a>
                </form>
            </div>

            <div class="mt-4 flex flex-wrap gap-2">
                @foreach ($tabs as $tab)
                    <a
                        href="{{ route($tab['route'], ['start_date' => $startDate, 'end_date' => $endDate]) }}"
                        class="rounded-full px-3 py-1.5 text-sm font-medium {{ $section === $tab['section'] ? 'bg-violet-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}"
                    >
                        {{ $tab['label'] }}
                    </a>
                @endforeach
            </div>
        </div>

        @if (!empty($interpretations))
            <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                <h3 class="text-sm font-semibold text-gray-900">Key insights</h3>
                <ul class="mt-3 space-y-2">
                    @foreach ($interpretations as $insight)
                        @php
                            $type = $insight['type'] ?? 'info';
                            $bgClass = match ($type) {
                                'success' => 'bg-emerald-50 text-emerald-800 border-emerald-200',
                                'warning' => 'bg-amber-50 text-amber-800 border-amber-200',
                                default => 'bg-violet-50 text-violet-800 border-violet-200',
                            };
                        @endphp
                        <li class="flex items-start gap-2 rounded-lg border px-3 py-2 text-sm {{ $bgClass }}">
                            <span class="mt-0.5 shrink-0" aria-hidden="true">
                                @if ($type === 'success')
                                    <svg class="h-4 w-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                @elseif ($type === 'warning')
                                    <svg class="h-4 w-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                @else
                                    <svg class="h-4 w-4 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                @endif
                            </span>
                            <span>{{ $insight['text'] }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($cards as $label => $value)
                <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                    <p class="text-xs font-medium uppercase tracking-wide text-gray-500">{{ $label }}</p>
                    <p class="mt-2 text-2xl font-semibold text-gray-900">{{ $value }}</p>
                </div>
            @endforeach
        </div>

        @if (!empty($charts))
            @php
                $barCharts = collect($charts)->filter(fn ($chart) => ($chart['type'] ?? '') === 'bar')->values();
                $otherCharts = collect($charts)->reject(fn ($chart) => ($chart['type'] ?? '') === 'bar')->values();
                $singleOtherChart = $otherCharts->count() === 1;
            @endphp

            @if ($barCharts->isNotEmpty())
                <div class="grid grid-cols-1 gap-6">
                    @foreach ($barCharts as $chart)
                        <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm" x-data="{ mode: 'chart' }">
                            <div class="flex items-center justify-between gap-3">
                                <h3 class="text-sm font-semibold text-gray-900">{{ $chart['title'] }}</h3>
                                <div class="inline-flex rounded-lg border border-gray-200 bg-gray-50 p-1">
                                    <button type="button" @click="mode = 'chart'" :class="mode === 'chart' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-600'" class="rounded-md px-3 py-1.5 text-xs font-medium">
                                        Chart
                                    </button>
                                    <button type="button" @click="mode = 'table'" :class="mode === 'table' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-600'" class="rounded-md px-3 py-1.5 text-xs font-medium">
                                        Table
                                    </button>
                                </div>
                            </div>

                            <div x-show="mode === 'chart'" class="mt-4 h-72">
                                <canvas id="{{ $chart['id'] }}"></canvas>
                            </div>

                            <div x-show="mode === 'table'" class="mt-4 overflow-x-auto" style="display: none;">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-2 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Label</th>
                                            <th class="px-4 py-2 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">{{ $chart['datasetLabel'] }}</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100 bg-white">
                                        @forelse (($chart['labels'] ?? []) as $index => $label)
                                            <tr>
                                                <td class="whitespace-nowrap px-4 py-2 text-sm text-gray-700">{{ $label }}</td>
                                                <td class="whitespace-nowrap px-4 py-2 text-sm text-gray-700">{{ $chart['data'][$index] ?? 0 }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="2" class="px-4 py-6 text-center text-sm text-gray-500">No records found.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            @if ($otherCharts->isNotEmpty())
                <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-2">
                    @foreach ($otherCharts as $chart)
                        <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm {{ ($chart['fullWidth'] ?? false) ? 'lg:col-span-2' : ($singleOtherChart ? 'lg:col-span-2' : '') }}" x-data="{ mode: 'chart' }">
                            <div class="flex items-center justify-between gap-3">
                                <h3 class="text-sm font-semibold text-gray-900">{{ $chart['title'] }}</h3>
                                <div class="inline-flex rounded-lg border border-gray-200 bg-gray-50 p-1">
                                    <button type="button" @click="mode = 'chart'" :class="mode === 'chart' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-600'" class="rounded-md px-3 py-1.5 text-xs font-medium">
                                        Chart
                                    </button>
                                    <button type="button" @click="mode = 'table'" :class="mode === 'table' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-600'" class="rounded-md px-3 py-1.5 text-xs font-medium">
                                        Table
                                    </button>
                                </div>
                            </div>

                            <div x-show="mode === 'chart'" class="mt-4 h-72">
                                <canvas id="{{ $chart['id'] }}"></canvas>
                            </div>

                            <div x-show="mode === 'table'" class="mt-4 overflow-x-auto" style="display: none;">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-2 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Label</th>
                                            <th class="px-4 py-2 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">{{ $chart['datasetLabel'] }}</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100 bg-white">
                                        @forelse (($chart['labels'] ?? []) as $index => $label)
                                            <tr>
                                                <td class="whitespace-nowrap px-4 py-2 text-sm text-gray-700">{{ $label }}</td>
                                                <td class="whitespace-nowrap px-4 py-2 text-sm text-gray-700">{{ $chart['data'][$index] ?? 0 }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="2" class="px-4 py-6 text-center text-sm text-gray-500">No records found.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        @endif

    </div>

    @if (!empty($charts))
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
        <script>
            const reportCharts = @json($charts);
            const palette = ['#7c3aed', '#2563eb', '#10b981', '#f59e0b', '#ef4444', '#14b8a6', '#6366f1', '#84cc16'];

            reportCharts.forEach((chart) => {
                const canvas = document.getElementById(chart.id);
                if (!canvas) {
                    return;
                }

                const parent = canvas.parentElement;
                const numericData = (chart.data || []).map(value => Number(value) || 0);
                const hasData = (chart.labels || []).length > 0 && numericData.some(value => value > 0);
                if (!hasData) {
                    canvas.classList.add('hidden');
                    const placeholder = document.createElement('div');
                    placeholder.className = 'flex h-full items-center justify-center rounded-lg border border-dashed border-gray-300 bg-gray-50 text-sm font-medium text-gray-500';
                    placeholder.textContent = 'No data available for selected range.';
                    parent.appendChild(placeholder);
                    return;
                }

                const colors = chart.labels.map((_, idx) => palette[idx % palette.length]);
                const backgroundColor = ['line'].includes(chart.type)
                    ? 'rgba(37, 99, 235, 0.15)'
                    : colors;
                const borderColor = chart.type === 'line' ? '#2563eb' : colors;

                new Chart(canvas, {
                    type: chart.type,
                    data: {
                        labels: chart.labels,
                        datasets: [{
                            label: chart.datasetLabel,
                            data: chart.data,
                            backgroundColor,
                            borderColor,
                            borderWidth: 2,
                            fill: chart.type === 'line',
                            tension: chart.type === 'line' ? 0.35 : 0,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        indexAxis: chart.horizontal ? 'y' : 'x',
                        plugins: {
                            legend: {
                                display: chart.type !== 'bar'
                            }
                        }
                    }
                });
            });
        </script>
    @endif
</x-app-layout>
