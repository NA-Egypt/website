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
            'name' => $this->name,
            'description' => $this->description,
            'date' => $this->date ? $this->date->format('Y-m-d') : null,
            'service_body_id' => $this->service_body_id,
            'service_body' => new ServiceBodyResource($this->whenLoaded('servicebody')),
            'day_id' => $this->day_id,
            'day' => new DayResource($this->whenLoaded('day')),
            'created_at' => $this->created_at ? $this->created_at->toIso8601String() : null,
            'updated_at' => $this->updated_at ? $this->updated_at->toIso8601String() : null,
        ];
    }
}

