<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventPost;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class EventPostController extends Controller
{
    public function create(): View
    {
        $events = Event::query()
            ->whereNotIn('status', ['deleted'])
            ->orderByDesc('start_at')
            ->get();

        return view('multimedia.posts.create', compact('events'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'event_id' => ['required', 'exists:events,id'],
            'type' => ['required', 'in:invitation,announcement,highlight,thank_you,reminder,advertisement'],
            'caption' => ['nullable', 'string', 'max:5000'],
            'ai_prompt' => ['nullable', 'string', 'max:1000'],
            'media.*' => ['nullable', 'file', 'mimes:jpeg,png,jpg,gif,mp4,mov,avi', 'max:10240'], // 10MB max
            'generate_ai_content' => ['nullable', 'boolean'],
        ]);

        $event = Event::findOrFail($request->event_id);
        $caption = $request->caption;

        // Generate AI caption if requested and no caption provided
        if ($request->generate_ai_content && empty($caption)) {
            $caption = $this->generateAICaption($event, $request->type, $request->ai_prompt);
        }

        $post = EventPost::create([
            'event_id' => $request->event_id,
            'user_id' => Auth::id(),
            'type' => $request->type,
            'status' => 'draft',
            'caption' => $caption,
            'ai_prompt' => $request->ai_prompt,
        ]);

        // Handle media uploads
        if ($request->hasFile('media')) {
            foreach ($request->file('media') as $mediaFile) {
                $path = $mediaFile->store('post-media', 'public');
                
                $mediaType = str_starts_with($mediaFile->getMimeType(), 'image/') ? 'image' : 'video';
                
                $post->media()->create([
                    'url' => Storage::url($path),
                    'type' => $mediaType,
                    'file_name' => $mediaFile->getClientOriginalName(),
                    'file_size' => $mediaFile->getSize(),
                ]);
            }
        }

        return redirect()
            ->route('multimedia.index')
            ->with('success', 'Post created successfully!');
    }

    private function generateAICaption(Event $event, string $postType, ?string $customPrompt = null): string
    {
        // Build context from event details
        $context = [
            'title' => $event->title,
            'description' => $event->description,
            'start_date' => $event->start_at->format('F j, Y'),
            'start_time' => $event->start_at->format('g:i A'),
            'venue' => $event->venue?->name,
            'status' => $event->status,
        ];

        // Generate caption based on post type
        $basePrompt = match($postType) {
            'invitation' => "Create an engaging invitation post for an event. Be enthusiastic and include key details.",
            'announcement' => "Create an exciting announcement post about an upcoming event. Build anticipation.",
            'highlight' => "Create a highlight post showcasing an amazing event that happened. Focus on energy and success.",
            'thank_you' => "Create a heartfelt thank you post to participants and supporters of an event. Show genuine appreciation.",
            'reminder' => "Create a friendly reminder post about an upcoming event. Create urgency without being pushy.",
            'advertisement' => "Create a compelling advertisement post to promote an event. Focus on benefits and excitement.",
            default => "Create an engaging social media post about an event.",
        };

        $prompt = $customPrompt ?: $basePrompt;
        $prompt .= "\n\nEvent Details:\n";
        $prompt .= "Title: {$context['title']}\n";
        $prompt .= "Date: {$context['start_date']}\n";
        $prompt .= "Time: {$context['start_time']}\n";
        if ($context['venue']) {
            $prompt .= "Venue: {$context['venue']}\n";
        }
        if ($context['description']) {
            $prompt .= "Description: {$context['description']}\n";
        }

        // For now, return a template-based caption
        // In the future, this would integrate with an AI service like OpenAI
        return $this->generateTemplateCaption($context, $postType);
    }

    private function generateTemplateCaption(array $context, string $postType): string
    {
        return match($postType) {
            'invitation' => "🎉 You're invited to {$context['title']}! Join us on {$context['start_date']} at {$context['start_time']}" . 
                           ($context['venue'] ? " at {$context['venue']}" : "") . 
                           " for an amazing experience. Don't miss out! #Event #Invitation",
                           
            'announcement' => "📢 Exciting news! We're thrilled to announce {$context['title']} happening on {$context['start_date']}. " .
                           "Get ready for an unforgettable experience. Mark your calendars! #Announcement #Event",
                           
            'highlight' => "✨ What an incredible time at {$context['title']}! The energy, the people, the moments - absolutely unforgettable. " .
                           "Here are some highlights from our amazing event! #Highlights #Success",
                           
            'thank_you' => "🙏 A huge thank you to everyone who made {$context['title']} a massive success! " .
                           "To our participants, organizers, and supporters - you're the best! #ThankYou #Community",
                           
            'reminder' => "⏰ Friendly reminder! {$context['title']} is coming up on {$context['start_date']} at {$context['start_time']}" .
                           ($context['venue'] ? " at {$context['venue']}" : "") . 
                           ". We can't wait to see you there! #Reminder #Event",
                           
            'advertisement' => "🎬 Get ready for {$context['title']}! This is more than just an event - it's an experience you don't want to miss. " .
                           "Join us on {$context['start_date']} and be part of something extraordinary! #Advertisement #DontMissOut",
                           
            default => "📅 Join us for {$context['title']} on {$context['start_date']}! " .
                      "It's going to be an amazing event you won't want to miss. #Event",
        };
    }
}

