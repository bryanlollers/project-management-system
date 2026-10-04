<?php

namespace App\Http\Resources\Users;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            ...Arr::only($this->resource->attributesToArray(), ['id', 'name', 'email', 'email_verified_at', 'role', 'tasks_count', 'created_at', 'updated_at']),
            'pivot' => $this->whenLoaded('pivot'),
        ];
    }
}
