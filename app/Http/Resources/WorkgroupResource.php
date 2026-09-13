<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WorkgroupResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'parent_id'      => $this->parent_id,
            'parent'         => new ServiceCommitteeResource($this->whenLoaded('parent')),
            'workgroup_type' => $this->workgroup_type,
            'status'         => $this->status,
            'start_date'     => $this->start_date?->format('Y-m-d'),
            'end_date'       => $this->end_date?->format('Y-m-d'),
            'ar_name'        => $this->ar_name,
            'en_name'        => $this->en_name,
            'chairman_name'  => $this->chairman_name,
            'chairman_phone' => $this->chairman_phone,
            'email'          => $this->email,
            'location'       => $this->location,
            'ar_address'     => $this->ar_address,
            'en_address'     => $this->en_address,
            'notes'          => $this->notes,
            'user_id'        => $this->user_id,
            'user'           => new UserResource($this->whenLoaded('user')),
            'meetings'       => ScMeetingResource::collection($this->whenLoaded('meetings')),
            'created_at'     => $this->created_at,
            'updated_at'     => $this->updated_at,
        ];
    }
}
