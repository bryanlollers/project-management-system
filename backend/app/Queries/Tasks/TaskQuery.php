<?php

namespace App\Queries\Tasks;

use App\Models\Task;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class TaskQuery
{
    public function forUser(User $user): Builder
    {
        $query = Task::query();
        if (! $user->manages()) {
            $query->whereHas('project.members', fn (Builder $members) => $members->where('users.id', $user->id));
        }

        return $query;
    }

    private function withSummary(Builder $query): Builder
    {
        $query->with(['project', 'assignee'])->withCount('comments');

        return $query;
    }

    public function paginate(User $user, array $filters): LengthAwarePaginator
    {
        $query = $this->withSummary($this->forUser($user));
        if (isset($filters['search']) && $filters['search'] !== '') {
            $term = '%'.$filters['search'].'%';
            $query->where(function (Builder $search) use ($term): void {
                $search->where('title', 'like', $term);
            });
        }
        foreach (['status', 'priority', 'project_id', 'assignee_id'] as $field) {
            if (isset($filters[$field]) && $filters[$field] !== '') {
                $query->where($field, $filters[$field]);
            }
        }

        return $query->latest('id')->paginate($filters['per_page'] ?? 12);
    }

    public function detail(User $user, Task $task): Task
    {
        $record = $this->withSummary($this->forUser($user))->findOrFail($task->id);
        $record->load('comments.user');

        return $record;
    }
}
