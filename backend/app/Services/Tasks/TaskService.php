<?php

namespace App\Services\Tasks;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Queries\Tasks\TaskQuery;
use App\Services\Activity\ActivityService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TaskService
{
    public function __construct(private readonly TaskQuery $query, private readonly ActivityService $activity) {}

    public function paginate(User $actor, array $filters): LengthAwarePaginator
    {
        return $this->query->paginate($actor, $filters);
    }

    public function detail(User $actor, Task $task): Task
    {
        return $this->query->detail($actor, $task);
    }

    public function create(User $actor, array $data): Task
    {
        return DB::transaction(function () use ($actor, $data): Task {
            $this->checkAssignee($data);
            $task = Task::create($data);
            $this->activity->record($actor, 'Created '.$task->title, $task->project_id, $task->id);

            return $task;
        });
    }

    public function update(User $actor, Task $task, array $data): Task
    {
        return DB::transaction(function () use ($actor, $task, $data): Task {
            $this->checkAssignee($data, $task);
            $task->update($data);
            $this->activity->record($actor, 'Updated '.$task->title, $task->project_id, $task->id);

            return $task->fresh();
        });
    }

    public function delete(User $actor, Task $task): void
    {
        $task->delete();
    }

    private function checkAssignee(array $data, ?Task $task = null): void
    {
        $projectId = $data['project_id'] ?? $task?->project_id;
        $assigneeId = array_key_exists('assignee_id', $data) ? $data['assignee_id'] : $task?->assignee_id;
        if ($assigneeId && ! Project::findOrFail($projectId)->members()->whereKey($assigneeId)->exists()) {
            throw ValidationException::withMessages(['assignee_id' => 'The assignee must be a project team member.']);
        }
    }
}
