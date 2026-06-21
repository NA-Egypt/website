<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceBodyAgendaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = parent::toArray($request);
        if ($this->meeting_date) {
            $data['meeting_date'] = $this->meeting_date->format('Y-m-d');
        }
        if ($this->agenda_date) {
            $data['agenda_date'] = $this->agenda_date->format('Y-m-d');
        }
        return $data;
    }
}
