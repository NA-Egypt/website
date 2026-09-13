<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ChangeRequestResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'user_id'         => $this->user_id,
            'request_type'    => $this->request_type,
            'subject'         => $this->subject,
            'description'     => $this->description,
            'attachment_url'  => $this->attachment_path ? Storage::url($this->attachment_path) : null,
            'status'          => $this->status,
            'user'            => new UserResource($this->whenLoaded('user')),
            'created_at'      => $this->created_at,
            'updated_at'      => $this->updated_at,
        ];
    }
}
