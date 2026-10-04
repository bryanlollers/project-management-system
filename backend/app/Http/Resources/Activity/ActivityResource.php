<?php

namespace App\Http\Resources\Activity;

use App\Http\Resources\Users\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;

class ActivityResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            ...Arr::only($this->resource->attributesToArray(), ['id', 'user_id', 'project_id', 'task_id', 'description', 'created_at', 'updated_at']),
            'user' => new UserResource($this->whenLoaded('user')),
        ];
    }
}
