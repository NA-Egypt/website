<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MeetingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $group = $this->group;
        $directOnlineGroup = $this->directOnlineGroup;
        $groupOrDirect = $group ?: $directOnlineGroup;

        return [
            'id'                     => $this->id,
            'day_id'                 => $this->day_id,
            'group_id'               => $this->group_id,
            'direct_online_group_id' => $this->direct_online_group_id,
            'type'                   => $this->type,
            'lang'                   => $this->lang,
            'status'                 => $this->status,
            'start_time'             => $this->start_time,
            'end_time'               => $this->end_time,
            'formatted_start_time'   => $this->formatted_start_time,
            'formatted_end_time'     => $this->formatted_end_time,
            'duration'               => $this->duration,
            'notes'                  => $this->notes,
            'recurrence'             => $this->recurrence ?? ['weekly'],
            
            // Group & Location metadata
            'group_name_ar'          => $groupOrDirect?->ar_name,
            'group_name_en'          => $groupOrDirect?->en_name,
            'group_type'             => $group?->group_type ?? ($directOnlineGroup ? 'online' : 'in_person'),
            'address_ar'             => $group?->ar_address,
            'address_en'             => $group?->en_address,
            'location_url'           => $group?->location,
            'meeting_url'            => $directOnlineGroup?->meeting_url,

            // Geographical metadata
            'neighborhood_id'        => $group?->neighborhood_id,
            'neighborhood_name_ar'   => $group?->neighborhood?->ar_name,
            'neighborhood_name_en'   => $group?->neighborhood?->en_name,
            'city_id'                => $group?->neighborhood?->city_id,
            'city_name_ar'           => $group?->neighborhood?->city?->ar_name,
            'city_name_en'           => $group?->neighborhood?->city?->en_name,

            // Relations
            'day'                    => $this->whenLoaded('day'),
            'topics'                 => $this->whenLoaded('topics'),
            'options'                => $this->whenLoaded('options'),
        ];
    }
}

