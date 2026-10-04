<?php

namespace App\Http\Resources\Dashboard;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DashboardResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'active_projects' => $this->resource['active_projects'],
            'completed_projects' => $this->resource['completed_projects'],
            'overdue_tasks' => $this->resource['overdue_tasks'],
            'total_tasks' => $this->resource['total_tasks'],
            'task_status' => $this->resource['task_status']->map(fn ($status) => [
                'status' => $status->status,
                'total' => $status->total,
            ]),
            'workload' => $this->resource['workload']->map(fn ($user) => [
                'id' => $user->id,
                'name' => $user->name,
                'tasks_count' => $user->tasks_count,
            ]),
        ];
    }
}
