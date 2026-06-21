<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommitteeReportResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = parent::toArray($request);

        $user = auth()->user();
        $isRestricted = !$user || $user->hasRole('ServiceBody') || $user->hasRole('gsr');

        if ($isRestricted) {
            unset($data['review_notes']);
        }

        return $data;
    }
}
