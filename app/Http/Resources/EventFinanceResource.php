<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EventFinanceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'event_id' => $this->event_id,
            'logistics_total' => $this->logistics_total,
            'formatted_logistics_total' => '₱' . number_format($this->logistics_total, 2),
            'equipment_total' => $this->equipment_total,
            'formatted_equipment_total' => '₱' . number_format($this->equipment_total, 2),
            'other_total' => $this->other_total,
            'formatted_other_total' => '₱' . number_format($this->other_total, 2),
            'grand_total' => $this->grand_total,
            'formatted_grand_total' => '₱' . number_format($this->grand_total, 2),
            'status' => $this->status,
            'formatted_status' => \Illuminate\Support\Str::headline($this->status),
            'notes' => $this->notes,
            'submitted_by' => [
                'id' => $this->submittedBy?->id,
                'name' => $this->submittedBy?->name,
                'email' => $this->submittedBy?->email,
            ],
            'approved_by' => $this->when($this->approved_by, [
                'id' => $this->approver?->id,
                'name' => $this->approver?->name,
                'email' => $this->approver?->email,
            ]),
            'approved_at' => $this->approved_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
