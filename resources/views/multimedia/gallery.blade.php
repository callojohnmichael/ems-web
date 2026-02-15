<x-app-layout>
    <div class="max-w-5xl mx-auto py-6 space-y-6">
        {{-- HEADER --}}
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">Multimedia</h2>
                    <p class="mt-1 text-sm text-gray-500">Event posts, AI captions, images, and engagement.</p>
                </div>
                @can('create multimedia post')
                    <a href="{{ route('multimedia.posts.create') }}" class="inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700">+ Create Post</a>
                @endcan
            </div>
        </div>

        {{-- SUMMARY --}}
        <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
            <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Posts (30d)</p>
                <p class="mt-1 text-xl font-semibold text-gray-900">{{ $multimediaSummary['total_posts'] }}</p>
            </div>
            <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                <p class="text-xs font-medium uppercase tracking-wide text-gray-500">With AI</p>
                <p class="mt-1 text-xl font-semibold text-gray-900">{{ $multimediaSummary['posts_with_ai'] }}</p>
            </div>
            <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Images / Videos</p>
                <p class="mt-1 text-xl font-semibold text-gray-900">{{ $multimediaSummary['media_images'] }} / {{ $multimediaSummary['media_videos'] }}</p>
            </div>
            <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Top event</p>
                <p class="mt-1 truncate text-lg font-semibold text-gray-900" title="{{ $multimediaSummary['top_event_name'] ?? '—' }}">{{ $multimediaSummary['top_event_name'] ?? '—' }}</p>
                @if($multimediaSummary['top_event_posts'] > 0)
                    <p class="text-xs text-gray-500">{{ $multimediaSummary['top_event_posts'] }} posts</p>
                @endif
            </div>
        </div>
        @can('view reports')
            <p class="text-sm text-gray-500"><a href="{{ route('reports.multimedia') }}" class="font-medium text-indigo-600 hover:text-indigo-700">View full report</a></p>
        @endcan

        {{-- VIEW TOGGLE --}}
        <div class="flex items-center gap-3 rounded-xl border border-gray-200 bg-white px-4 py-3 shadow-sm">
            <span class="text-sm font-medium text-gray-700">View:</span>
            <div class="flex gap-2">
                <a href="{{ route('multimedia.index', array_merge(request()->query(), ['view' => null])) }}" class="rounded-lg px-4 py-2 text-sm font-medium transition {{ request('view') !== 'gallery' ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">Feed</a>
                <a href="{{ route('multimedia.index', array_merge(request()->query(), ['view' => 'gallery'])) }}" class="rounded-lg px-4 py-2 text-sm font-medium transition {{ request('view') === 'gallery' ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">Gallery</a>
            </div>
        </div>

        {{-- FILTER BAR --}}
        <form method="GET" action="{{ route('multimedia.index') }}" class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
            <input type="hidden" name="view" value="gallery">
            <div class="flex flex-wrap items-end gap-3">
                <div class="min-w-[120px]">
                    <label for="filter_event_id" class="block text-xs font-medium text-gray-500">Event</label>
                    <select id="filter_event_id" name="event_id" class="mt-1 w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">All events</option>
                        @foreach($eventsForFilter as $ev)
                            <option value="{{ $ev->id }}" {{ request('event_id') == $ev->id ? 'selected' : '' }}>{{ Str::limit($ev->title, 40) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="min-w-[120px]">
                    <label for="filter_type" class="block text-xs font-medium text-gray-500">Type</label>
                    <select id="filter_type" name="type" class="mt-1 w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">All types</option>
                        @foreach(['invitation','announcement','highlight','thank_you','reminder','advertisement'] as $t)
                            <option value="{{ $t }}" {{ request('type') === $t ? 'selected' : '' }}>{{ ucfirst($t) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="filter_date_from" class="block text-xs font-medium text-gray-500">From</label>
                    <input type="date" id="filter_date_from" name="date_from" value="{{ request('date_from') }}" class="mt-1 rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div>
                    <label for="filter_date_to" class="block text-xs font-medium text-gray-500">To</label>
                    <input type="date" id="filter_date_to" name="date_to" value="{{ request('date_to') }}" class="mt-1 rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-xs text-gray-500">Sort</span>
                    <a href="{{ route('multimedia.index', array_merge(request()->query(), ['sort' => 'latest', 'view' => 'gallery'])) }}" class="rounded px-2 py-1 text-sm {{ request('sort', 'latest') === 'latest' ? 'bg-indigo-100 font-medium text-indigo-800' : 'text-gray-600 hover:bg-gray-100' }}">Latest</a>
                    <a href="{{ route('multimedia.index', array_merge(request()->query(), ['sort' => 'oldest', 'view' => 'gallery'])) }}" class="rounded px-2 py-1 text-sm {{ request('sort') === 'oldest' ? 'bg-indigo-100 font-medium text-indigo-800' : 'text-gray-600 hover:bg-gray-100' }}">Oldest</a>
                </div>
                <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">Apply</button>
                <a href="{{ route('multimedia.index', ['view' => 'gallery']) }}" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Clear</a>
            </div>
        </form>

        {{-- GALLERY GRID --}}
        <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5">
            @forelse($posts as $post)
                @foreach($post->media as $media)
                    @php
                        $thumbSrc = ($media->type === 'image' && $media->thumbnail_path) ? asset('storage/' . $media->thumbnail_path) : asset('storage/' . $media->path);
                        $eventTitle = $post->event ? $post->event->title : null;
                        $mediaAlt = $eventTitle ? ($media->type === 'image' ? 'Image for ' . $eventTitle : 'Video for ' . $eventTitle) : 'Post media';
                    @endphp
                    <a href="{{ route('multimedia.index', array_merge(request()->except('view'), ['view' => null])) . '#post-' . $post->id }}" class="group relative block aspect-square w-full overflow-hidden rounded-lg bg-gray-100">
                        @if($media->type === 'image')
                            <img src="{{ $thumbSrc }}" alt="{{ $mediaAlt }}" loading="lazy" class="h-full w-full object-cover transition group-hover:scale-105">
                        @else
                            <video class="h-full w-full object-cover" muted preload="metadata" poster="">
                                <source src="{{ asset('storage/' . $media->path) }}" type="video/mp4">
                            </video>
                            <span class="absolute bottom-1 right-1 rounded bg-black/60 px-1.5 py-0.5 text-xs text-white">Video</span>
                        @endif
                        <span class="absolute inset-0 bg-black/0 group-hover:bg-black/30 transition-all"></span>
                    </a>
                @endforeach
            @empty
                <div class="col-span-full rounded-xl border border-gray-200 bg-white p-12 text-center">
                    <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <h3 class="mt-4 text-base font-medium text-gray-900">No gallery items</h3>
                    <p class="mt-2 text-sm text-gray-500">No media found for the selected filters. Try adjusting your filters or create a new post with images or videos.</p>
                    @can('create multimedia post')
                        <a href="{{ route('multimedia.posts.create') }}" class="mt-6 inline-block rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">+ Create Post</a>
                    @endcan
                </div>
            @endforelse
        </div>

        <div>{{ $posts->links() }}</div>
    </div>
    @vite(['resources/js/multimedia-index.js'])
</x-app-layout>
