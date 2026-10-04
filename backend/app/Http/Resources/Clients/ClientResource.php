<?php

namespace App\Http\Resources\Clients;

use App\Http\Resources\Projects\ProjectResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;

class ClientResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            ...Arr::only($this->resource->attributesToArray(), ['id', 'name', 'company', 'email', 'phone', 'notes', 'contacts', 'projects_count', 'created_at', 'updated_at']),
            'projects' => ProjectResource::collection($this->whenLoaded('projects')),
        ];
    }
}
