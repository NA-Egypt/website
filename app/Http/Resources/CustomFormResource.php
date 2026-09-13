<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomFormResource extends JsonResource
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
            'title'           => $this->title,
            'type'            => $this->type,
            'status'          => $this->status,
            'slug'            => $this->slug,
            'views'           => $this->views,
            'conversion_rate' => $this->conversion_rate,
            'settings'        => $this->settings,
            'fields'          => $this->fields->map(function ($field) {
                return [
                    'id'         => $field->id,
                    'label'      => $field->label,
                    'type'       => $field->type,
                    'required'   => (bool) $field->required,
                    'options'    => $field->options,
                    'sort_order' => $field->sort_order,
                ];
            }),
            'submissions_count' => $this->whenCounted('submissions', $this->submissions_count),
            'user'            => new UserResource($this->whenLoaded('user')),
            'created_at'      => $this->created_at,
            'updated_at'      => $this->updated_at,
        ];
    }
}
