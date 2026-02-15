<x-app-layout>
    <div class="max-w-5xl mx-auto py-6 space-y-6">

        {{-- HEADER --}}
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">Multimedia</h2>
                    <p class="mt-1 text-sm text-gray-500">
                        Event posts, AI captions, images, and engagement.
                    </p>
                </div>

                {{-- CREATE POST BUTTON (Permission-based) --}}
                @can('create multimedia post')
                    <a href="{{ route('multimedia.posts.create') }}"
                       class="inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700">
                        + Create Post
                    </a>
                @endcan
            </div>
        </div>

        {{-- MULTIMEDIA INSIGHTS SUMMARY --}}
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
            <p class="text-sm text-gray-500">
                <a href="{{ route('reports.multimedia') }}" class="font-medium text-indigo-600 hover:text-indigo-700">View full report</a>
            </p>
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
            <input type="hidden" name="view" value="{{ request('view') }}">
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
                    <a href="{{ route('multimedia.index', array_merge(request()->query(), ['sort' => 'latest'])) }}" class="rounded px-2 py-1 text-sm {{ request('sort', 'latest') === 'latest' ? 'bg-indigo-100 font-medium text-indigo-800' : 'text-gray-600 hover:bg-gray-100' }}">Latest</a>
                    <a href="{{ route('multimedia.index', array_merge(request()->query(), ['sort' => 'oldest'])) }}" class="rounded px-2 py-1 text-sm {{ request('sort') === 'oldest' ? 'bg-indigo-100 font-medium text-indigo-800' : 'text-gray-600 hover:bg-gray-100' }}">Oldest</a>
                </div>
                <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">Apply</button>
                <a href="{{ route('multimedia.index') }}" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Clear</a>
            </div>
        </form>

        {{-- SUCCESS MESSAGE --}}
        @if(session('success'))
            <div class="rounded-xl border border-green-200 bg-green-50 p-4 text-green-800">
                <p class="text-sm font-semibold">{{ session('success') }}</p>
            </div>
        @endif

        {{-- POSTS --}}
        @forelse($posts as $post)
            <div id="post-{{ $post->id }}" class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm space-y-3 sm:p-6">

                {{-- POST HEADER --}}
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="font-semibold text-gray-900">
                            {{ $post->event?->title ?? 'Unknown Event' }}
                        </p>

                        <p class="text-sm text-gray-500">
                            Posted by {{ $post->user?->name ?? 'Unknown User' }}
                            • {{ $post->created_at->diffForHumans() }}
                        </p>
                    </div>

                    <span class="text-xs px-2 py-1 rounded bg-gray-100 text-gray-700">
                        {{ ucfirst($post->type) }}
                    </span>
                </div>

                {{-- CAPTION / AI NARRATIVE (structured) --}}
                @if($post->caption)
                    @php
                        $parsed = $post->parsed_caption;
                        $isLong = strlen($parsed['full']) > 280;
                        $showReadMore = $isLong && $parsed['has_narrative'];
                    @endphp
                    <div class="prose prose-sm max-w-none max-w-2xl">
                        <div class="{{ $post->ai_generated_content ? 'border-l-4 border-indigo-200 bg-indigo-50/50 pl-4 pr-3 py-3 rounded-r-lg' : '' }}">
                            @if($showReadMore)
                                <div class="caption-summary text-gray-700 text-sm" id="caption-summary-{{ $post->id }}">
                                    <p class="whitespace-pre-line">{{ Str::limit($parsed['summary'], 280) }}</p>
                                    @if(count($parsed['story_elements']) > 0)
                                        <p class="mt-2 font-medium text-gray-800">Story elements</p>
                                        <ul class="mt-1 list-disc list-inside space-y-0.5 text-gray-700">
                                            @foreach(array_slice($parsed['story_elements'], 0, 5) as $el)
                                                <li>{{ $el }}</li>
                                            @endforeach
                                            @if(count($parsed['story_elements']) > 5)
                                                <li class="text-gray-500">+ {{ count($parsed['story_elements']) - 5 }} more</li>
                                            @endif
                                        </ul>
                                    @endif
                                    <button type="button" class="mt-2 text-indigo-600 hover:text-indigo-700 text-sm font-medium js-caption-read-more" data-post-id="{{ $post->id }}" data-full="{{ e($parsed['full']) }}">
                                        Read more
                                    </button>
                                </div>
                                <div class="caption-full hidden text-gray-700 text-sm whitespace-pre-line" id="caption-full-{{ $post->id }}"></div>
                            @else
                                <p class="text-gray-700 whitespace-pre-line">{{ $parsed['summary'] }}</p>
                                @if(count($parsed['story_elements']) > 0)
                                    <p class="mt-2 font-medium text-gray-800">Story elements</p>
                                    <ul class="mt-1 list-disc list-inside space-y-0.5 text-gray-700">
                                        @foreach($parsed['story_elements'] as $el)
                                            <li>{{ $el }}</li>
                                        @endforeach
                                    </ul>
                                @endif
                            @endif
                        </div>
                    </div>
                @endif

                {{-- MEDIA --}}
                @if($post->media->count() > 0)
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4">
                        @foreach($post->media as $media)
                            @php
                                $mediaAlt = $post->event?->title ? ($media->type === 'image' ? 'Image for ' . $post->event->title : 'Video for ' . $post->event->title) : 'Post media';
                                $mediaSrc = ($media->type === 'image' && $media->thumbnail_path) ? asset('storage/' . $media->thumbnail_path) : asset('storage/' . $media->path);
                            @endphp
                            <div class="relative group aspect-video w-full overflow-hidden rounded-lg bg-black">
                                @if($media->type === 'image')
                                    <img src="{{ $mediaSrc }}" 
                                         alt="{{ $mediaAlt }}" 
                                         loading="lazy"
                                         class="h-full w-full object-cover rounded-lg js-lightbox-src"
                                         data-full-url="{{ asset('storage/' . $media->path) }}"
                                         data-media-type="image">
                                @elseif($media->type === 'video')
                                    <video class="h-full w-full object-cover js-lightbox-src" controls preload="metadata" data-full-url="{{ asset('storage/' . $media->path) }}" data-media-type="video">
                                        <source src="{{ asset('storage/' . $media->path) }}" type="video/mp4">
                                        Your browser does not support the video tag.
                                    </video>
                                    {{-- AI Generated Video Badge --}}
                                    @if($media->metadata && str_contains((string) $media->metadata, 'ai_generated'))
                                        <div class="absolute top-2 left-2 bg-gradient-to-r from-green-500 to-blue-500 text-white text-xs px-2 py-1 rounded-full font-medium">
                                            AI Generated
                                        </div>
                                    @endif
                                @endif

                                {{-- Media Actions (lightbox / full size) --}}
                                <div class="absolute inset-0 bg-black/0 group-hover:bg-black/50 transition-all duration-200 rounded-lg flex items-center justify-center opacity-0 group-hover:opacity-100">
                                    <button type="button" class="js-lightbox-open bg-white text-gray-800 px-3 py-1.5 rounded-lg text-sm font-medium hover:bg-gray-100 transition" data-full-url="{{ asset('storage/' . $media->path) }}" data-media-type="{{ $media->type }}">
                                        View Full Size
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                {{-- AI Content Indicators --}}
                @if($post->ai_generated_content || ($post->media->count() > 0 && $post->media->contains('metadata', 'like', '%ai_generated%')))
                    <div class="flex flex-wrap gap-2">
                        @if($post->ai_generated_content)
                            @if(strstr($post->caption, '✨ Story elements:') || strlen($post->caption) > 300)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gradient-to-r from-purple-100 to-pink-100 text-purple-800 border border-purple-200">
                                    <span class="mr-1">📖</span>
                                    AI Narrative
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gradient-to-r from-indigo-100 to-purple-100 text-indigo-800 border border-indigo-200">
                                    <span class="mr-1">✨</span>
                                    AI Caption
                                </span>
                            @endif
                        @endif
                        
                        @if($post->media->contains('metadata', 'like', '%ai_generated%'))
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gradient-to-r from-green-100 to-blue-100 text-green-800 border border-green-200">
                                <span class="mr-1">🎬</span>
                                AI Video
                            </span>
                        @endif
                        
                        @if($post->media->contains('metadata', 'like', '%ai_enhanced%'))
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gradient-to-r from-purple-100 to-pink-100 text-purple-800 border border-purple-200">
                                <span class="mr-1">🎨</span>
                                AI Enhanced
                            </span>
                        @endif
                    </div>
                @endif

                {{-- ENGAGEMENT --}}
                @php
                    $userReaction = $post->reactions->firstWhere('user_id', auth()->id());
                @endphp
                <div class="flex items-center justify-between pt-3 border-t border-gray-100">
                    <div class="flex items-center gap-4 text-sm text-gray-500">
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                            </svg>
                            <span id="engagement-reactions-count-{{ $post->id }}">{{ $post->reactions->count() }}</span>
                        </span>
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                            </svg>
                            <span id="engagement-comments-count-{{ $post->id }}">{{ $post->comments->count() }}</span>
                        </span>
                    </div>

                    <div class="flex items-center gap-2">
                        @can('react multimedia post')
                            <div class="js-reaction-container inline-flex items-center" data-post-id="{{ $post->id }}" data-has-reaction="{{ $userReaction ? '1' : '0' }}" data-store-url="{{ route('multimedia.posts.reactions.store', $post) }}" data-destroy-url="{{ route('multimedia.posts.reactions.destroy', $post) }}" data-csrf="{{ csrf_token() }}">
                                @if($userReaction)
                                    <button type="button" class="js-reaction-remove inline-flex items-center justify-center p-0 text-red-500 hover:text-red-600 transition" title="Remove reaction">
                                        <svg class="w-5 h-5 shrink-0" fill="currentColor" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                        </svg>
                                    </button>
                                @else
                                    <button type="button" class="js-reaction-add inline-flex items-center justify-center p-0 text-gray-400 hover:text-red-500 transition" title="Like">
                                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                        </svg>
                                    </button>
                                @endif
                            </div>
                        @endcan
                        <button type="button" onclick="toggleComments({{ $post->id }})" class="inline-flex items-center justify-center p-0 text-gray-400 hover:text-blue-500 transition" title="Comments">
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- COMMENTS SECTION --}}
                <div id="comments-{{ $post->id }}" class="hidden mt-3 pt-3 border-t border-gray-100 space-y-3">
                    @can('comment multimedia post')
                        <form method="POST" action="{{ route('multimedia.posts.comments.store', $post) }}" class="mb-3 js-comment-form" data-post-id="{{ $post->id }}">
                            @csrf
                            <div class="flex gap-2">
                                <input type="text" name="content" required maxlength="1000" placeholder="Write a comment..." class="flex-1 rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500 js-comment-input">
                                <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">Post</button>
                            </div>
                            @error('content')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </form>
                    @endcan
                    @php
                        $commentLimit = 10;
                        $commentsSorted = $post->comments->sortByDesc('created_at');
                        $initialComments = $commentsSorted->take($commentLimit);
                        $moreComments = $commentsSorted->skip($commentLimit);
                        $moreCount = $moreComments->count();
                    @endphp
                    <h3 class="mb-2 text-sm font-semibold text-gray-800">Comments (<span id="comments-count-{{ $post->id }}">{{ $post->comments->count() }}</span>)</h3>
                    <div id="comments-list-{{ $post->id }}" class="space-y-3">
                            @foreach($initialComments as $comment)
                                @php $isOwn = $comment->user_id === auth()->id(); @endphp
                                <div class="flex {{ $isOwn ? 'justify-end' : 'justify-start' }}">
                                    <div class="rounded-2xl border p-3 w-full {{ $isOwn ? 'border-indigo-200 bg-indigo-50/70' : 'border-gray-200 bg-white' }}">
                                        <div class="text-xs {{ $isOwn ? 'text-indigo-600 font-medium' : 'text-gray-500' }}">
                                            {{ $isOwn ? 'You' : ($comment->user->name ?? 'Unknown User') }} • {{ $comment->created_at?->diffForHumans() }}
                                        </div>
                                        <p class="mt-1 text-sm {{ $isOwn ? 'text-gray-800' : 'text-gray-700' }}">{{ $comment->body }}</p>
                                    </div>
                                </div>
                            @endforeach
                            @if($moreCount > 0)
                                <div id="comments-more-{{ $post->id }}" class="hidden space-y-3">
                                    @foreach($moreComments as $comment)
                                        @php $isOwn = $comment->user_id === auth()->id(); @endphp
                                        <div class="flex {{ $isOwn ? 'justify-end' : 'justify-start' }}">
                                            <div class="rounded-2xl border p-3 w-full {{ $isOwn ? 'border-indigo-200 bg-indigo-50/70' : 'border-gray-200 bg-white' }}">
                                                <div class="text-xs {{ $isOwn ? 'text-indigo-600 font-medium' : 'text-gray-500' }}">
                                                    {{ $isOwn ? 'You' : ($comment->user->name ?? 'Unknown User') }} • {{ $comment->created_at?->diffForHumans() }}
                                                </div>
                                                <p class="mt-1 text-sm {{ $isOwn ? 'text-gray-800' : 'text-gray-700' }}">{{ $comment->body }}</p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <button type="button" onclick="toggleCommentsMore({{ $post->id }}, {{ $moreCount }})" id="comments-more-btn-{{ $post->id }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-700">
                                    View more ({{ $moreCount }})
                                </button>
                            @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="rounded-xl border border-gray-200 bg-white p-12 text-center shadow-sm">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No posts found</h3>
                <p class="mt-1 text-sm text-gray-500">Get started by creating your first post.</p>
                @can('create multimedia post')
                    <div class="mt-6">
                        <a href="{{ route('multimedia.posts.create') }}"
                           class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 transition">
                            + Create Post
                        </a>
                    </div>
                @endcan
            </div>
        @endforelse

        {{-- PAGINATION --}}
        <div>
            {{ $posts->links() }}
        </div>

    </div>

    {{-- Lightbox overlay --}}
    <div id="lightbox" class="fixed inset-0 z-50 hidden bg-black/90 flex items-center justify-center p-4" role="dialog" aria-modal="true" aria-label="Media viewer">
        <button type="button" id="lightbox-close" class="absolute top-4 right-4 z-10 rounded-full bg-white/10 p-2 text-white hover:bg-white/20 focus:outline focus:ring-2 focus:ring-white" aria-label="Close">×</button>
        <div class="relative max-h-full max-w-full">
            <img id="lightbox-img" src="" alt="" class="max-h-[90vh] max-w-full rounded object-contain hidden">
            <video id="lightbox-video" class="max-h-[90vh] max-w-full rounded hidden" controls></video>
        </div>
    </div>

    @vite(['resources/js/multimedia-index.js'])
</x-app-layout>
