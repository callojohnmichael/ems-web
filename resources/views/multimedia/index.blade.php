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

        {{-- SUCCESS MESSAGE --}}
        @if(session('success'))
            <div class="rounded-xl border border-green-200 bg-green-50 p-4 text-green-800">
                <p class="text-sm font-semibold">{{ session('success') }}</p>
            </div>
        @endif

        {{-- POSTS --}}
        @forelse($posts as $post)
            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm space-y-3">

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

                {{-- CAPTION --}}
                @if($post->caption)
                    <p class="text-gray-800 whitespace-pre-line">
                        {{ $post->caption }}
                    </p>
                @else
                    <p class="text-gray-400 italic">
                        No caption yet.
                    </p>
                @endif

                {{-- MEDIA --}}
                @if($post->media->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        @foreach($post->media as $media)
                            @if($media->type === 'image')
                                <img src="{{ Storage::url($media->path) }}" alt="Post image" class="w-full h-48 object-cover rounded-lg">
                            @elseif($media->type === 'video')
                                <video controls class="w-full h-48 object-cover rounded-lg">
                                    <source src="{{ Storage::url($media->path) }}" type="video/mp4">
                                </video>
                            @endif
                        @endforeach
                    </div>
                @endif

                {{-- REACTIONS --}}
                <div class="flex items-center gap-4 pt-2 border-t border-gray-100">
                    <div class="flex items-center gap-2">
                        <form action="{{ route('multimedia.posts.reactions.store', $post->id) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" name="type" value="like" class="flex items-center gap-1 text-sm text-gray-500 hover:text-red-500 transition-colors">
                                <span>❤️</span>
                                <span>{{ $post->reactions()->where('type', 'like')->count() }}</span>
                            </button>
                        </form>
                        <form action="{{ route('multimedia.posts.reactions.store', $post->id) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" name="type" value="love" class="flex items-center gap-1 text-sm text-gray-500 hover:text-pink-500 transition-colors">
                                <span>😍</span>
                                <span>{{ $post->reactions()->where('type', 'love')->count() }}</span>
                            </button>
                        </form>
                        <form action="{{ route('multimedia.posts.reactions.store', $post->id) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" name="type" value="wow" class="flex items-center gap-1 text-sm text-gray-500 hover:text-yellow-500 transition-colors">
                                <span>😮</span>
                                <span>{{ $post->reactions()->where('type', 'wow')->count() }}</span>
                            </button>
                        </form>
                    </div>
                    <button onclick="toggleComments({{ $post->id }})" class="text-sm text-gray-500 hover:text-indigo-600 transition-colors">
                        💬 {{ $post->comments->count() }} comments
                    </button>
                </div>

                {{-- COMMENTS SECTION --}}
                <div id="comments-{{ $post->id }}" class="hidden space-y-3 pt-3 border-t border-gray-100">
                    {{-- Existing Comments --}}
                    @foreach($post->comments as $comment)
                        <div class="flex gap-3">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center">
                                    <span class="text-xs font-medium text-gray-600">
                                        {{ strtoupper(substr($comment->user->name ?? 'U', 0, 1)) }}
                                    </span>
                                </div>
                            </div>
                            <div class="flex-1">
                                <div class="bg-gray-50 rounded-lg p-3">
                                    <p class="text-sm font-medium text-gray-900">{{ $comment->user->name ?? 'Unknown User' }}</p>
                                    <p class="text-sm text-gray-700 mt-1">{{ $comment->content }}</p>
                                    <p class="text-xs text-gray-500 mt-1">{{ $comment->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach

                    {{-- Add Comment Form --}}
                    <form action="{{ route('multimedia.posts.comments.store', $post->id) }}" method="POST" class="flex gap-3">
                        @csrf
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-indigo-100 rounded-full flex items-center justify-center">
                                <span class="text-xs font-medium text-indigo-600">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                </span>
                            </div>
                        </div>
                        <div class="flex-1">
                            <textarea name="content" rows="2" placeholder="Add a comment..." 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 resize-none"
                                    required></textarea>
                            <button type="submit" class="mt-2 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors">
                                Post Comment
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @empty
            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                <p class="text-gray-500">No posts yet.</p>

                @can('create multimedia post')
                    <p class="text-sm text-gray-400 mt-2">
                        Create the first post for an event.
                    </p>
                @endcan
            </div>
        @endforelse

        {{-- PAGINATION --}}
        <div>
            {{ $posts->links() }}
        </div>

    </div>
</x-app-layout>

<script>
function toggleComments(postId) {
    const commentsSection = document.getElementById(`comments-${postId}`);
    if (commentsSection.classList.contains('hidden')) {
        commentsSection.classList.remove('hidden');
    } else {
        commentsSection.classList.add('hidden');
    }
}
</script>
