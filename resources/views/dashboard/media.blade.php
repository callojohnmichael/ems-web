<x-app-layout>
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="px-4 py-6 sm:px-0">
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Multimedia Dashboard</h1>
                <p class="mt-2 text-gray-600">Manage posts, media, and view upcoming events</p>
            </div>

            {{-- Multimedia insights summary (30d) --}}
            <div class="mb-6 rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                <h3 class="text-sm font-semibold text-gray-900">Multimedia insights (last 30 days)</h3>
                <div class="mt-3 grid grid-cols-2 gap-3 sm:grid-cols-4">
                    <div>
                        <p class="text-xs text-gray-500">Posts</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $multimediaSummary['total_posts'] }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">With AI</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $multimediaSummary['posts_with_ai'] }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Images / Videos</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $multimediaSummary['media_images'] }} / {{ $multimediaSummary['media_videos'] }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Top event</p>
                        <p class="truncate text-lg font-semibold text-gray-900" title="{{ $multimediaSummary['top_event_name'] ?? '—' }}">{{ $multimediaSummary['top_event_name'] ?? '—' }}</p>
                        @if($multimediaSummary['top_event_posts'] > 0)
                            <p class="text-xs text-gray-500">{{ $multimediaSummary['top_event_posts'] }} posts</p>
                        @endif
                    </div>
                </div>
                @can('view reports')
                    <p class="mt-3 text-sm text-gray-500">
                        <a href="{{ route('reports.multimedia') }}" class="font-medium text-indigo-600 hover:text-indigo-700">View full report</a>
                    </p>
                @endcan
            </div>

            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-8">
                <div class="bg-white overflow-hidden shadow rounded-lg border border-gray-100">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Upcoming Events</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ $upcomingEvents->count() }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-5 py-3">
                        <div class="text-sm">
                            <a href="{{ route('calendar.index') }}" class="font-medium text-blue-700 hover:text-blue-600">
                                View calendar
                            </a>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow rounded-lg border border-gray-100">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-green-500 rounded-md flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Recent Events</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ $recentEvents->count() }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-5 py-3">
                        <div class="text-sm">
                            <a href="{{ route('events.index') }}" class="font-medium text-green-700 hover:text-green-600">
                                View all
                            </a>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow rounded-lg border border-gray-100">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-purple-500 rounded-md flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Media Gallery</dt>
                                    <dd class="text-lg font-medium text-gray-900">Active</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-5 py-3">
                        <div class="text-sm">
                            <a href="{{ route('multimedia.index') }}" class="font-medium text-purple-700 hover:text-purple-600">
                                Manage media
                            </a>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow rounded-lg border border-gray-100">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-indigo-500 rounded-md flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z" />
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Posts</dt>
                                    <dd class="text-lg font-medium text-gray-900">Manage</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-5 py-3">
                        <div class="text-sm">
                            <a href="{{ route('media.posts') }}" class="font-medium text-indigo-700 hover:text-indigo-600">
                                Manage posts
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            @if($upcomingEvents->count() > 0)
                <div class="bg-white shadow rounded-lg mb-8">
                    <div class="px-4 py-5 sm:p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">Upcoming Events</h3>
                            <a href="{{ route('calendar.index') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">
                                View calendar
                            </a>
                        </div>
                        <div class="flow-root">
                            <ul class="-my-5 divide-y divide-gray-200">
                                @foreach($upcomingEvents->take(5) as $event)
                                    <li class="py-4">
                                        <div class="flex items-center space-x-4">
                                            <div class="flex-shrink-0">
                                                <div class="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center">
                                                    <svg class="h-4 w-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                    </svg>
                                                </div>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-medium text-gray-900 truncate">
                                                    <a href="{{ route('events.show', $event) }}" class="hover:underline">{{ $event->title }}</a>
                                                </p>
                                                <p class="text-sm text-gray-500">
                                                    {{ $event->requestedBy->name }} • {{ $event->start_at->format('M j, Y g:i A') }}
                                                </p>
                                            </div>
                                            <div>
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                    {{ Str::title(str_replace('_', ' ', $event->status)) }}
                                                </span>
                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            @if($recentEvents->count() > 0)
                <div class="bg-white shadow rounded-lg mb-8">
                    <div class="px-4 py-5 sm:p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">Recent Events</h3>
                            <a href="{{ route('events.index') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">
                                View all
                            </a>
                        </div>
                        <div class="flow-root">
                            <ul class="-my-5 divide-y divide-gray-200">
                                @foreach($recentEvents as $event)
                                    <li class="py-4">
                                        <div class="flex items-center space-x-4">
                                            <div class="flex-shrink-0">
                                                <div class="h-8 w-8 rounded-full bg-green-100 flex items-center justify-center">
                                                    <svg class="h-4 w-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                </div>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-medium text-gray-900 truncate">
                                                    <a href="{{ route('events.show', $event) }}" class="hover:underline">{{ $event->title }}</a>
                                                </p>
                                                <p class="text-sm text-gray-500">
                                                    {{ $event->requestedBy->name }} • {{ $event->end_at->format('M j, Y') }}
                                                </p>
                                            </div>
                                            <div>
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    Completed
                                                </span>
                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Quick Actions</h3>
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                        <a href="{{ route('calendar.index') }}" class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            View Calendar
                        </a>
                        
                        <a href="{{ route('multimedia.index') }}" class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            Media Gallery
                        </a>
                        
                        <a href="{{ route('media.posts') }}" class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2h2v4l.586-.586z" />
                            </svg>
                            Manage Posts
                        </a>
                        
                        <a href="{{ route('support.index') }}" class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            Get Support
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>