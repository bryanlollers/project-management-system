<?php

namespace App\Queries\Projects;

use App\Models\Project;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class ProjectQuery
{
    public function forUser(User $user): Builder
    {
        $query = Project::query();
        if (! $user->manages()) {
            $query->whereHas('members', fn (Builder $members) => $members->where('users.id', $user->id));
        }

        return $query;
    }

    private function withSummary(Builder $query): Builder
    {
        $query->with(['client', 'members'])->withCount(['tasks', 'tasks as done_tasks_count' => fn (Builder $tasks) => $tasks->where('status', 'done')]);

        return $query;
    }

    public function paginate(User $user, array $filters): LengthAwarePaginator
    {
        $query = $this->withSummary($this->forUser($user));
        if (isset($filters['search']) && $filters['search'] !== '') {
            $term = '%'.$filters['search'].'%';
            $query->where(function (Builder $search) use ($term): void {
                $search->where('name', 'like', $term);
            });
        }
        foreach (['status', 'priority', 'client_id'] as $field) {
            if (isset($filters[$field]) && $filters[$field] !== '') {
                $query->where($field, $filters[$field]);
            }
        }

        return $query->latest('id')->paginate($filters['per_page'] ?? 12);
    }

    public function detail(User $user, Project $project): Project
    {
        $record = $this->withSummary($this->forUser($user))->findOrFail($project->id);
        $record->load('tasks.assignee');

        return $record;
    }
}
