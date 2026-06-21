<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AgendaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = parent::toArray($request);
        if ($this->agenda_date) {
            $data['agenda_date'] = $this->agenda_date->format('Y-m-d');
        }
        if ($this->next_business_meeting) {
            $data['next_business_meeting'] = $this->next_business_meeting->format('Y-m-d H:i:s');
        }
        return $data;
    }
}
