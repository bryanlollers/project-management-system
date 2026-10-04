<?php

namespace App\Http\Resources\Tasks;

use App\Http\Resources\Comments\CommentResource;
use App\Http\Resources\Projects\ProjectResource;
use App\Http\Resources\Users\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;

class TaskResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            ...Arr::only($this->resource->attributesToArray(), ['id', 'project_id', 'assignee_id', 'title', 'description', 'status', 'priority', 'due_date', 'comments_count', 'created_at', 'updated_at']),
            'project' => new ProjectResource($this->whenLoaded('project')),
            'assignee' => new UserResource($this->whenLoaded('assignee')),
            'comments' => CommentResource::collection($this->whenLoaded('comments')),
        ];
    }
}
