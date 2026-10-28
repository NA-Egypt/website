<?php

namespace App\Http\Resources;

use App\Services\BotDetectionService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GroupResource extends JsonResource
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

        if (!$canViewGsrData) {
            unset(
                $data['ar_gsr_name'],
                $data['en_gsr_name'],
                $data['phone'],
                $data['user_id'],
                $data['user']
            );
        }

        return $data;
    }
}
