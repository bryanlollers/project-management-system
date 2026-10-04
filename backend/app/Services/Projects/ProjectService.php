<?php

namespace App\Services\Projects;

use App\Models\Project;
use App\Models\User;
use App\Queries\Projects\ProjectQuery;
use App\Services\Activity\ActivityService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ProjectService
{
    public function __construct(private readonly ProjectQuery $query, private readonly ActivityService $activity) {}

    public function paginate(User $actor, array $filters): LengthAwarePaginator
    {
        return $this->query->paginate($actor, $filters);
    }

    public function detail(User $actor, Project $project): Project
    {
        return $this->query->detail($actor, $project);
    }

    public function create(User $actor, array $data): Project
    {
        return DB::transaction(function () use ($actor, $data): Project {
            $project = Project::create(Arr::except($data, 'member_ids'));
            $project->members()->sync($data['member_ids'] ?? []);
            $this->activity->record($actor, 'Created '.$project->name, $project->id);

            return $this->detail($actor, $project);
        });
    }

    public function update(User $actor, Project $project, array $data): Project
    {
        return DB::transaction(function () use ($actor, $project, $data): Project {
            if (isset($data['member_ids'])) {
                $assigned = $project->tasks()->whereNotNull('assignee_id')->pluck('assignee_id')->unique();
                if ($assigned->diff($data['member_ids'])->isNotEmpty()) {
                    throw ValidationException::withMessages(['member_ids' => 'Reassign tasks before removing their team members.']);
                }
                $project->members()->sync($data['member_ids']);
            }
            $project->update(Arr::except($data, 'member_ids'));
            $this->activity->record($actor, 'Updated '.$project->name, $project->id);

            return $this->detail($actor, $project);
        });
    }

    public function delete(User $actor, Project $project): void
    {
        $project->delete();
    }
}
