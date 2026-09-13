<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DirectOnlineGroupResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'ar_name'     => $this->ar_name,
            'en_name'     => $this->en_name,
            'ar_gsr_name' => $this->ar_gsr_name,
            'en_gsr_name' => $this->en_gsr_name,
            'phone'       => $this->phone,
            'location'    => $this->location,
            'user_id'     => $this->user_id,
            'user'        => new UserResource($this->whenLoaded('user')),
            'meetings'    => MeetingResource::collection($this->whenLoaded('meetings')),
            'created_at'  => $this->created_at,
            'updated_at'  => $this->updated_at,
        ];
    }
}
