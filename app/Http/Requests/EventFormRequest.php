<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class EventFormRequest extends FormRequest
{
    // public function authorize(): bool
    // {
    //     if ($this->isMethod('POST')) {
    //         // Users can create events (requests)
    //         return Auth::user()->isUser();
    //     }

    //     if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
    //         // Only admins can edit events
    //         return Auth::user()->isAdmin();
    //     }

    //     return false;
    // }

    public function rules(): array
    {
        $rules = [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'start_at' => ['required', 'date', 'after:now'],
            'end_at' => ['required', 'date', 'after:start_at'],
        ];

        // Admin-specific rules for editing
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules['status'] = ['required', 'string', 'in:' . implode(',', [
                \App\Models\Event::STATUS_PENDING_APPROVAL,
                \App\Models\Event::STATUS_APPROVED,
                \App\Models\Event::STATUS_REJECTED,
                \App\Models\Event::STATUS_PUBLISHED,
                \App\Models\Event::STATUS_CANCELLED,
                \App\Models\Event::STATUS_COMPLETED,
            ])];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Event title is required.',
            'description.required' => 'Event description is required.',
            'start_at.after' => 'Event start time must be in the future.',
            'end_at.after' => 'Event end time must be after start time.',
            'status.in' => 'Invalid event status.',
        ];
    }
}
