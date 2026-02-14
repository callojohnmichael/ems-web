<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EventResource extends JsonResource
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
            'title' => $this->title,
            'description' => $this->description,
            'start_at' => $this->start_at,
            'end_at' => $this->end_at,
            'formatted_start_at' => $this->start_at->format('M d, Y h:i A'),
            'formatted_end_at' => $this->end_at->format('M d, Y h:i A'),
            'venue' => [
                'id' => $this->venue?->id,
                'name' => $this->venue?->name,
                'address' => $this->venue?->address,
                'capacity' => $this->venue?->capacity,
            ],
            'status' => $this->status,
            'formatted_status' => \Illuminate\Support\Str::headline($this->status),
            'number_of_participants' => $this->number_of_participants,
            'notes' => $this->notes,
            'requested_by' => [
                'id' => $this->requestedBy?->id,
                'name' => $this->requestedBy?->name,
                'email' => $this->requestedBy?->email,
            ],
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            
            // Relations (if loaded)
            'participants_count' => $this->whenLoaded('participants', fn() => $this->participants->count()),
            'logistics_items_count' => $this->whenLoaded('logisticsItems', fn() => $this->logisticsItems->count()),
            'budget_items_count' => $this->whenLoaded('budget', fn() => $this->budget->count()),
            'finance_request' => $this->whenLoaded('financeRequest'),
            'custodian_requests' => $this->whenLoaded('custodianRequests'),
            
            // Additional computed fields
            'is_upcoming' => $this->start_at->isFuture(),
            'is_ongoing' => $this->start_at->isPast() && $this->end_at->isFuture(),
            'is_past' => $this->end_at->isPast(),
        ];
    }
}
