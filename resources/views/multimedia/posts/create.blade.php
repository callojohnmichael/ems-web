<x-app-layout>
    <div class="max-w-4xl mx-auto py-6 space-y-6">

        {{-- HEADER --}}
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <div class="flex items-center gap-4">
                <a href="{{ route('multimedia.index') }}" class="text-gray-500 hover:text-gray-700">
                    ← Back
                </a>
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">Create Event Post</h2>
                    <p class="mt-1 text-sm text-gray-500">
                        Generate AI-powered posts for your events
                    </p>
                </div>
            </div>
        </div>

        <form action="{{ route('multimedia.posts.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            {{-- EVENT SELECTION --}}
            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                <h3 class="text-base font-semibold text-gray-900 mb-4">Select Event</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($events as $event)
                        <label class="relative flex cursor-pointer rounded-lg border p-4 hover:bg-gray-50 transition-colors
                            {{ $errors->has('event_id') && old('event_id') == $event->id ? 'border-red-500 bg-red-50' : 'border-gray-200' }}">
                            <input type="radio" name="event_id" value="{{ $event->id }}" 
                                   class="sr-only" 
                                   {{ old('event_id') == $event->id ? 'checked' : '' }}
                                   required>
                            <div class="flex-1">
                                <p class="font-medium text-gray-900">{{ $event->title }}</p>
                                <p class="text-sm text-gray-500 mt-1">
                                    {{ $event->start_at->format('M j, Y') }} 
                                    @if($event->end_at && $event->end_at->format('Y-m-d') !== $event->start_at->format('Y-m-d'))
                                        - {{ $event->end_at->format('M j, Y') }}
                                    @endif
                                </p>
                                <p class="text-sm text-gray-400 mt-1">{{ $event->venue?->name }}</p>
                            </div>
                            <div class="ml-4">
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                                    {{ $event->status === 'published' ? 'bg-green-100 text-green-800' : 
                                       ($event->status === 'ended' ? 'bg-gray-100 text-gray-800' : 
                                       'bg-yellow-100 text-yellow-800') }}">
                                    {{ ucfirst($event->status) }}
                                </span>
                            </div>
                            <div class="pointer-events-none absolute inset-0 rounded-lg border-2 
                                {{ old('event_id') == $event->id ? 'border-indigo-500' : 'border-transparent' }}">
                            </div>
                        </label>
                    @endforeach
                </div>
                
                @error('event_id')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- POST TYPE --}}
            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                <h3 class="text-base font-semibold text-gray-900 mb-4">Post Type</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <label class="relative flex cursor-pointer rounded-lg border p-4 hover:bg-gray-50 transition-colors
                        {{ $errors->has('type') && old('type') === 'invitation' ? 'border-red-500 bg-red-50' : 'border-gray-200' }}">
                        <input type="radio" name="type" value="invitation" class="sr-only" 
                               {{ old('type') === 'invitation' ? 'checked' : '' }} required>
                        <div class="text-center">
                            <div class="text-2xl mb-2">📧</div>
                            <p class="font-medium text-gray-900">Invitation</p>
                            <p class="text-xs text-gray-500 mt-1">Invite people to upcoming event</p>
                        </div>
                        <div class="pointer-events-none absolute inset-0 rounded-lg border-2 
                            {{ old('type') === 'invitation' ? 'border-indigo-500' : 'border-transparent' }}">
                        </div>
                    </label>

                    <label class="relative flex cursor-pointer rounded-lg border p-4 hover:bg-gray-50 transition-colors
                        {{ $errors->has('type') && old('type') === 'announcement' ? 'border-red-500 bg-red-50' : 'border-gray-200' }}">
                        <input type="radio" name="type" value="announcement" class="sr-only" 
                               {{ old('type') === 'announcement' ? 'checked' : '' }} required>
                        <div class="text-center">
                            <div class="text-2xl mb-2">📢</div>
                            <p class="font-medium text-gray-900">Announcement</p>
                            <p class="text-xs text-gray-500 mt-1">Share news about the event</p>
                        </div>
                        <div class="pointer-events-none absolute inset-0 rounded-lg border-2 
                            {{ old('type') === 'announcement' ? 'border-indigo-500' : 'border-transparent' }}">
                        </div>
                    </label>

                    <label class="relative flex cursor-pointer rounded-lg border p-4 hover:bg-gray-50 transition-colors
                        {{ $errors->has('type') && old('type') === 'highlight' ? 'border-red-500 bg-red-50' : 'border-gray-200' }}">
                        <input type="radio" name="type" value="highlight" class="sr-only" 
                               {{ old('type') === 'highlight' ? 'checked' : '' }} required>
                        <div class="text-center">
                            <div class="text-2xl mb-2">✨</div>
                            <p class="font-medium text-gray-900">Highlight</p>
                            <p class="text-xs text-gray-500 mt-1">Showcase event highlights</p>
                        </div>
                        <div class="pointer-events-none absolute inset-0 rounded-lg border-2 
                            {{ old('type') === 'highlight' ? 'border-indigo-500' : 'border-transparent' }}">
                        </div>
                    </label>

                    <label class="relative flex cursor-pointer rounded-lg border p-4 hover:bg-gray-50 transition-colors
                        {{ $errors->has('type') && old('type') === 'thank_you' ? 'border-red-500 bg-red-50' : 'border-gray-200' }}">
                        <input type="radio" name="type" value="thank_you" class="sr-only" 
                               {{ old('type') === 'thank_you' ? 'checked' : '' }} required>
                        <div class="text-center">
                            <div class="text-2xl mb-2">🙏</div>
                            <p class="font-medium text-gray-900">Thank You</p>
                            <p class="text-xs text-gray-500 mt-1">Thank participants</p>
                        </div>
                        <div class="pointer-events-none absolute inset-0 rounded-lg border-2 
                            {{ old('type') === 'thank_you' ? 'border-indigo-500' : 'border-transparent' }}">
                        </div>
                    </label>

                    <label class="relative flex cursor-pointer rounded-lg border p-4 hover:bg-gray-50 transition-colors
                        {{ $errors->has('type') && old('type') === 'reminder' ? 'border-red-500 bg-red-50' : 'border-gray-200' }}">
                        <input type="radio" name="type" value="reminder" class="sr-only" 
                               {{ old('type') === 'reminder' ? 'checked' : '' }} required>
                        <div class="text-center">
                            <div class="text-2xl mb-2">⏰</div>
                            <p class="font-medium text-gray-900">Reminder</p>
                            <p class="text-xs text-gray-500 mt-1">Remind about upcoming event</p>
                        </div>
                        <div class="pointer-events-none absolute inset-0 rounded-lg border-2 
                            {{ old('type') === 'reminder' ? 'border-indigo-500' : 'border-transparent' }}">
                        </div>
                    </label>

                    <label class="relative flex cursor-pointer rounded-lg border p-4 hover:bg-gray-50 transition-colors
                        {{ $errors->has('type') && old('type') === 'advertisement' ? 'border-red-500 bg-red-50' : 'border-gray-200' }}">
                        <input type="radio" name="type" value="advertisement" class="sr-only" 
                               {{ old('type') === 'advertisement' ? 'checked' : '' }} required>
                        <div class="text-center">
                            <div class="text-2xl mb-2">🎬</div>
                            <p class="font-medium text-gray-900">Advertisement</p>
                            <p class="text-xs text-gray-500 mt-1">Promote the event</p>
                        </div>
                        <div class="pointer-events-none absolute inset-0 rounded-lg border-2 
                            {{ old('type') === 'advertisement' ? 'border-indigo-500' : 'border-transparent' }}">
                        </div>
                    </label>
                </div>
                
                @error('type')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- MEDIA UPLOAD --}}
            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                <h3 class="text-base font-semibold text-gray-900 mb-4">Media (Optional)</h3>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Upload Images or Videos</label>
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-gray-400 transition-colors">
                            <input type="file" name="media[]" multiple accept="image/*,video/*" class="hidden" id="media-upload">
                            <label for="media-upload" class="cursor-pointer">
                                <div class="text-gray-400">
                                    <svg class="mx-auto h-12 w-12" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </div>
                                <p class="mt-2 text-sm text-gray-600">Click to upload images or videos</p>
                                <p class="text-xs text-gray-500">PNG, JPG, MP4 up to 10MB each</p>
                            </label>
                        </div>
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" name="generate_ai_content" id="generate_ai_content" value="1" checked class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        <label for="generate_ai_content" class="ml-2 text-sm text-gray-700">
                            Generate AI caption based on event details
                        </label>
                    </div>
                </div>
            </div>

            {{-- AI PROMPT (Optional) --}}
            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                <h3 class="text-base font-semibold text-gray-900 mb-4">AI Instructions (Optional)</h3>
                
                <textarea name="ai_prompt" rows="3" placeholder="Specific instructions for AI content generation..." 
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">{{ old('ai_prompt') }}</textarea>
                <p class="mt-1 text-xs text-gray-500">
                    Provide specific instructions for the AI to generate content. Leave empty for automatic generation.
                </p>
            </div>

            {{-- CAPTION --}}
            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                <h3 class="text-base font-semibold text-gray-900 mb-4">Caption</h3>
                
                <textarea name="caption" rows="4" placeholder="Write your caption here or let AI generate it..." 
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">{{ old('caption') }}</textarea>
                
                <div class="mt-3 flex items-center justify-between">
                    <p class="text-xs text-gray-500">
                        <span id="char-count">0</span> / 5000 characters
                    </p>
                    <button type="button" onclick="generateAICaption()" class="px-3 py-1 bg-indigo-100 text-indigo-700 text-sm font-medium rounded-lg hover:bg-indigo-200 transition-colors">
                        ✨ Generate with AI
                    </button>
                </div>
                
                @error('caption')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- SUBMIT BUTTONS --}}
            <div class="flex justify-end gap-4">
                <a href="{{ route('multimedia.index') }}" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 transition-colors">
                    Create Post
                </button>
            </div>
        </form>
    </div>
</x-app-layout>

<script>
// Character counter
const captionTextarea = document.querySelector('textarea[name="caption"]');
const charCount = document.getElementById('char-count');

if (captionTextarea && charCount) {
    captionTextarea.addEventListener('input', function() {
        charCount.textContent = this.value.length;
    });
}

// Generate AI caption (placeholder for future implementation)
function generateAICaption() {
    const button = event.target;
    const originalText = button.textContent;
    
    button.textContent = 'Generating...';
    button.disabled = true;
    
    // This would make an AJAX call to AI endpoint
    setTimeout(() => {
        // Simulate AI response
        const eventTitle = document.querySelector('input[name="event_id"]:checked')?.parentElement?.querySelector('.font-medium')?.textContent || 'this event';
        const postType = document.querySelector('input[name="type"]:checked')?.value || 'announcement';
        
        let aiCaption = '';
        switch(postType) {
            case 'invitation':
                aiCaption = `🎉 You're invited to ${eventTitle}! Join us for an amazing experience filled with fun, learning, and great memories. Don't miss out on this incredible opportunity!`;
                break;
            case 'announcement':
                aiCaption = `📢 Exciting news! We're thrilled to announce ${eventTitle}. Get ready for an unforgettable event that will inspire and entertain. Mark your calendars!`;
                break;
            case 'highlight':
                aiCaption = `✨ What an incredible time at ${eventTitle}! The energy, the people, the moments - absolutely unforgettable. Here are some highlights from our amazing event!`;
                break;
            case 'thank_you':
                aiCaption = `🙏 A huge thank you to everyone who made ${eventTitle} a massive success! To our participants, organizers, and supporters - you're the best!`;
                break;
            case 'reminder':
                aiCaption = `⏰ Friendly reminder! ${eventTitle} is just around the corner. Make sure you're ready for an amazing experience. We can't wait to see you there!`;
                break;
            case 'advertisement':
                aiCaption = `🎬 Get ready for ${eventTitle}! This is more than just an event - it's an experience you don't want to miss. Join us and be part of something extraordinary!`;
                break;
        }
        
        captionTextarea.value = aiCaption;
        charCount.textContent = aiCaption.length;
        
        button.textContent = originalText;
        button.disabled = false;
    }, 1500);
}
</script>
