<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EventFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Authorization is handled by Policy in controller
        return true;
    }

    public function rules(): array
    {
        $rules = [
            // ===================== BASIC EVENT DETAILS =====================
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'venue_id' => ['required', 'exists:venues,id'],

            'start_at' => ['required', 'date'],
            'end_at' => ['required', 'date', 'after:start_at'],
        ];

        /**
         * For CREATE (POST):
         * start_at must be in the future
         */
        if ($this->isMethod('POST')) {
            $rules['start_at'][] = 'after:now';
        }

        // ===================== LOGISTICS (RESOURCES) =====================
        // Example: resources[1] = 3
        $rules['resources'] = ['nullable', 'array'];
        $rules['resources.*'] = ['nullable', 'integer', 'min:0'];

        // ===================== CUSTODIAN EQUIPMENT =====================
        // Example: equipment[2] = 10 (chairs, tables, speakers)
        // You will connect this later to custodian_materials table.
        $rules['equipment'] = ['nullable', 'array'];
        $rules['equipment.*'] = ['nullable', 'integer', 'min:0'];

        // ===================== COMMITTEE =====================
        // committee[0][employee_id], committee[0][role]
        $rules['committee'] = ['nullable', 'array'];
        $rules['committee.*.employee_id'] = ['nullable', 'exists:employees,id'];
        $rules['committee.*.role'] = ['nullable', 'string', 'max:255'];

        // ===================== BUDGET ITEMS =====================
        // budget_items[0][description], budget_items[0][amount]
        $rules['budget_items'] = ['nullable', 'array'];
        $rules['budget_items.*.description'] = ['nullable', 'string', 'max:255'];
        $rules['budget_items.*.amount'] = ['nullable', 'numeric', 'min:0'];

        /**
         * Admin-only editing rules (PUT/PATCH)
         */
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules['status'] = [
                'nullable',
                'string',
                'in:' . implode(',', [
                    'pending_approvals',
                    'approved',
                    'rejected',
                    'published',
                    'cancelled',
                    'completed',
                    'deleted',
                ]),
            ];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Event title is required.',
            'description.required' => 'Event description is required.',

            'venue_id.required' => 'Venue is required.',
            'venue_id.exists' => 'Selected venue does not exist.',

            'start_at.required' => 'Start date and time is required.',
            'start_at.after' => 'Event start time must be in the future.',

            'end_at.required' => 'End date and time is required.',
            'end_at.after' => 'Event end time must be after start time.',

            'resources.array' => 'Invalid logistics resources format.',
            'resources.*.integer' => 'Logistics resource quantity must be a number.',
            'resources.*.min' => 'Logistics resource quantity cannot be negative.',

            'equipment.array' => 'Invalid equipment request format.',
            'equipment.*.integer' => 'Equipment quantity must be a number.',
            'equipment.*.min' => 'Equipment quantity cannot be negative.',

            'committee.array' => 'Invalid committee format.',
            'committee.*.employee_id.exists' => 'Selected committee employee is invalid.',
            'committee.*.role.max' => 'Committee role must not exceed 255 characters.',

            'budget_items.array' => 'Invalid budget format.',
            'budget_items.*.amount.numeric' => 'Budget amount must be a valid number.',
            'budget_items.*.amount.min' => 'Budget amount cannot be negative.',

            'status.in' => 'Invalid event status.',
        ];
    }

    /**
     * Optional cleanup:
     * Normalize empty strings in arrays to null
     */
    protected function prepareForValidation(): void
    {
        // If any empty arrays come in, normalize them
        $this->merge([
            'resources' => $this->resources ?? [],
            'equipment' => $this->equipment ?? [],
            'committee' => $this->committee ?? [],
            'budget_items' => $this->budget_items ?? [],
        ]);
    }
}
