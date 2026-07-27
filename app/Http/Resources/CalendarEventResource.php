<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CalendarEventResource extends JsonResource
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
            'start' => $this->start ? $this->start->toIso8601String() : null,
            'end' => $this->end ? $this->end->toIso8601String() : null,
            'description' => $this->description,
            'user_id' => $this->user_id,
            'color' => $this->color ?? '#00698f',
            'organizer' => $this->organizer,
            'location' => $this->location,
            'recurrence' => $this->recurrence ?? ['once'],
            'formatted_recurrence' => $this->formatted_recurrence,
            'is_featured' => (bool)$this->is_featured,
            'created_at' => $this->created_at ? $this->created_at->toIso8601String() : null,
            'updated_at' => $this->updated_at ? $this->updated_at->toIso8601String() : null,
        ];
    }
}
