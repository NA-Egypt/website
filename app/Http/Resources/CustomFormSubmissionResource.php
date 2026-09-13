<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomFormSubmissionResource extends JsonResource
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
            'custom_form_id' => $this->custom_form_id,
            'user_id'        => $this->user_id,
            'responses'      => $this->data,
            'user'           => new UserResource($this->whenLoaded('user')),
            'created_at'     => $this->created_at,
            'updated_at'     => $this->updated_at,
        ];
    }
}
