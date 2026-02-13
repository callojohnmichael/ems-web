<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Venue;
use Carbon\Carbon;

class EventFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Authorization handled in controller/policy
        return true;
    }

    public function rules(): array
    {
        $rules = [

            /**
             * =====================================================
             * BASIC EVENT DETAILS
             * =====================================================
             */
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'venue_id' => ['required', 'exists:venues,id'],
            'venue_location_ids' => ['nullable', 'array'],
            'venue_location_ids.*' => ['exists:venue_locations,id'],
            'venue_location_ids_json' => ['nullable', 'string'],

            'start_at' => ['required', 'date'],
            'end_at' => [
                'required',
                'date',
                // Fixed: Use Carbon to ensure end is after start
                function ($attribute, $value, $fail) {
                    try {
                        $start = Carbon::parse($this->input('start_at'));
                        $end = Carbon::parse($value);
                        if ($end->lessThanOrEqualTo($start)) {
                            $fail('The end date must be after the start date.');
                        }
                    } catch (\Exception $e) {
                        $fail('Invalid date format.');
                    }
                }
            ],

            'number_of_participants' => [
                'nullable',
                'integer',
                'min:0',
                function ($attribute, $value, $fail) {
                    // Check venue capacity
                    if ($value && $this->venue_id) {
                        $venue = Venue::find($this->venue_id);
                        if ($venue && $value > $venue->capacity) {
                            $fail("The {$attribute} cannot exceed the venue capacity of {$venue->capacity} persons.");
                        }
                    }
                }
            ],

            /**
             * =====================================================
             * LOGISTICS ITEMS
             * =====================================================
             */
            'logistics_items' => ['nullable', 'array'],
            'logistics_items.*.resource_id' => ['nullable', 'string'],
            'logistics_items.*.resource_name' => ['nullable', 'string', 'max:255'],
            'logistics_items.*.quantity' => ['nullable', 'integer', 'min:1'],
            'logistics_items.*.unit_price' => ['nullable', 'numeric', 'min:0'],

            /**
             * =====================================================
             * CUSTODIAN EQUIPMENT
             * =====================================================
             */
            'custodian_items' => ['nullable', 'array'],
            'custodian_items.*.material_id' => ['nullable', 'exists:custodian_materials,id'],
            'custodian_items.*.quantity' => ['nullable', 'integer', 'min:1'],

            /**
             * =====================================================
             * COMMITTEE MEMBERS
             * =====================================================
             */
            'committee' => ['nullable', 'array'],
            'committee.*.employee_id' => ['nullable', 'exists:employees,id'],
            'committee.*.role' => ['nullable', 'string', 'max:255'],

            /**
             * =====================================================
             * OPTIONAL BUDGET ITEMS
             * =====================================================
             */
            'budget_items' => ['nullable', 'array'],
            'budget_items.*.description' => ['nullable', 'string', 'max:255'],
            'budget_items.*.amount' => ['nullable', 'numeric', 'min:0'],
        ];

        // CREATE ONLY: start_at must be future
        if ($this->isMethod('POST')) {
            $rules['start_at'][] = 'after:now';
        }

        // UPDATE ONLY: status validation
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules['status'] = [
                'nullable',
                'string',
                'in:' . implode(',', [
                    'pending_approval',
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

            'logistics_items.array' => 'Invalid logistics format.',
            'logistics_items.*.quantity.integer' => 'Logistics quantity must be a number.',
            'logistics_items.*.quantity.min' => 'Logistics quantity must be at least 1.',
            'logistics_items.*.unit_price.numeric' => 'Unit price must be a valid number.',
            'logistics_items.*.unit_price.min' => 'Unit price cannot be negative.',

            'custodian_items.array' => 'Invalid custodian request format.',
            'custodian_items.*.material_id.exists' => 'Selected equipment is invalid.',
            'custodian_items.*.quantity.integer' => 'Equipment quantity must be a number.',
            'custodian_items.*.quantity.min' => 'Equipment quantity must be at least 1.',

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
     * Normalize empty inputs to arrays to avoid undefined indexes.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'logistics_items' => $this->logistics_items ?? [],
            'custodian_items' => $this->custodian_items ?? [],
            'committee' => $this->committee ?? [],
            'budget_items' => $this->budget_items ?? [],
        ]);
    }
}
