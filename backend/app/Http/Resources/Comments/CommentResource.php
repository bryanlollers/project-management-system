<?php

namespace App\Http\Resources\Comments;

use App\Http\Resources\Users\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;

class CommentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            ...Arr::only($this->resource->attributesToArray(), ['id', 'task_id', 'user_id', 'body', 'created_at', 'updated_at']),
            'user' => new UserResource($this->whenLoaded('user')),
        ];
    }
}
