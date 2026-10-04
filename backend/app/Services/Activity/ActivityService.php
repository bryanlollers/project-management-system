<?php

namespace App\Services\Activity;

use App\Models\Activity;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class ActivityService
{
    public function record(User $actor, string $description, ?int $projectId = null, ?int $taskId = null): Activity
    {
        return Activity::create([
            'user_id' => $actor->id,
            'project_id' => $projectId,
            'task_id' => $taskId,
            'description' => $description,
        ]);
    }

    public function paginate(User $actor, array $filters): LengthAwarePaginator
    {
        $query = Activity::with('user');
        if (! $actor->manages()) {
            $query->whereHas('project.members', fn (Builder $members) => $members->where('users.id', $actor->id));
        }
        if (isset($filters['task_id'])) {
            $query->where('task_id', $filters['task_id']);
        }

        return $query->latest()->latest('id')->paginate($filters['per_page'] ?? 20);
    }
}
