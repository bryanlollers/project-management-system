<?php

namespace App\Http\Resources\Projects;

use App\Http\Resources\Clients\ClientResource;
use App\Http\Resources\Tasks\TaskResource;
use App\Http\Resources\Users\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;

class ProjectResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            ...Arr::only($this->resource->attributesToArray(), ['id', 'client_id', 'name', 'description', 'status', 'priority', 'start_date', 'end_date', 'tasks_count', 'done_tasks_count', 'created_at', 'updated_at']),
            'client' => new ClientResource($this->whenLoaded('client')),
            'members' => UserResource::collection($this->whenLoaded('members')),
            'tasks' => TaskResource::collection($this->whenLoaded('tasks')),
            'progress' => $this->when(isset($this->tasks_count) || $this->resource->relationLoaded('tasks'), fn () => $this->progress()),
        ];
    }

    private function progress(): int
    {
        $total = $this->tasks_count ?? $this->resource->getRelation('tasks')->count();
        $done = $this->done_tasks_count ?? $this->resource->getRelation('tasks')->where('status', 'done')->count();

        return $total ? (int) round($done / $total * 100) : 0;
    }
}
