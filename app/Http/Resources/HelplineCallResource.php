<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HelplineCallResource extends JsonResource
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
            'duration' => $this->duration,
            'duration_label' => $this->duration_label,
            'call_date' => $this->call_date ? $this->call_date->format('Y-m-d') : null,
            'call_time_shift' => $this->call_time_shift,
            'caller_type' => $this->caller_type,
            'caller_type_other' => $this->caller_type_other,
            'effective_caller_type' => $this->effective_caller_type,
            'referral_source' => $this->referral_source,
            'referral_source_other' => $this->referral_source_other,
            'effective_referral_source' => $this->effective_referral_source,
            'volunteer_id' => $this->volunteer_id,
            'volunteer_name' => $this->volunteer_name,
            'volunteer_name_other' => $this->volunteer_name_other,
            'effective_volunteer_name' => $this->effective_volunteer_name,
            'is_step_12' => (bool) $this->is_step_12,
            'call_brief' => $this->call_brief,
            'discuss_in_meeting' => (bool) $this->discuss_in_meeting,
            'additional_info' => $this->additional_info,
            'entry_time' => $this->entry_time ? $this->entry_time->toIso8601String() : null,
            'created_at' => $this->created_at ? $this->created_at->toIso8601String() : null,
        ];
    }
}
