<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EventCustodianResource extends JsonResource
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
            'custodian_material' => [
                'id' => $this->custodianMaterial?->id,
                'name' => $this->custodianMaterial?->name,
                'category' => $this->custodianMaterial?->category,
                'stock' => $this->custodianMaterial?->stock,
            ],
            'quantity' => $this->quantity,
            'status' => $this->status,
            'formatted_status' => \Illuminate\Support\Str::headline($this->status),
            'notes' => $this->notes,
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
