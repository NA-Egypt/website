<?php

namespace App\Http\Resources;

use App\Services\BotDetectionService;
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
        $canViewGsrData = ($request->user('sanctum') || auth('sanctum')->check() || auth()->check())
            && !BotDetectionService::isBotOrAi($request);

        $data = parent::toArray($request);
        if ($this->agenda_date) {
            $data['agenda_date'] = $this->agenda_date->format('Y-m-d');
        }
        if ($this->next_business_meeting) {
            $data['next_business_meeting'] = $this->next_business_meeting->format('Y-m-d H:i:s');
        }

        if (!$canViewGsrData) {
            unset($data['submitter_name'], $data['alt_gsr_name']);
        }

        return $data;
    }
}
