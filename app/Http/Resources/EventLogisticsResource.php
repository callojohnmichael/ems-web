<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EventLogisticsResource extends JsonResource
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
            'resource' => [
                'id' => $this->resource?->id,
                'name' => $this->resource?->name,
                'type' => $this->resource?->type,
            ],
            'description' => $this->description,
            'quantity' => $this->quantity,
            'unit_price' => $this->unit_price,
            'formatted_unit_price' => '₱' . number_format($this->unit_price, 2),
            'subtotal' => $this->subtotal,
            'formatted_subtotal' => '₱' . number_format($this->subtotal, 2),
            'notes' => $this->notes,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
