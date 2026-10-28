<?php

namespace App\Http\Resources;

use App\Services\BotDetectionService;
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
        $canViewGsrData = ($request->user('sanctum') || auth('sanctum')->check() || auth()->check())
            && !BotDetectionService::isBotOrAi($request);

        return [
            'id'          => $this->id,
            'ar_name'     => $this->ar_name,
            'en_name'     => $this->en_name,
            'ar_gsr_name' => $this->when($canViewGsrData, $this->ar_gsr_name),
            'en_gsr_name' => $this->when($canViewGsrData, $this->en_gsr_name),
            'phone'       => $this->when($canViewGsrData, $this->phone),
            'location'    => $this->location,
            'user_id'     => $this->when($canViewGsrData, $this->user_id),
            'user'        => $this->when($canViewGsrData, fn () => new UserResource($this->whenLoaded('user'))),
            'meetings'    => MeetingResource::collection($this->whenLoaded('meetings')),
            'created_at'  => $this->created_at,
            'updated_at'  => $this->updated_at,
        ];
    }
}
