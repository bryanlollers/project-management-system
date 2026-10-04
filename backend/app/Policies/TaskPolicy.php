<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;

class TaskPolicy
{
    public function create(User $user): bool
    {
        return $user->manages();
    }

    public function view(User $user, Task $task): bool
    {
        return $user->can('view', $task->project);
    }

    public function update(User $user, Task $task): bool
    {
        return $user->manages() || ($task->assignee_id === $user->id && $this->view($user, $task));
    }

    public function delete(User $user, Task $task): bool
    {
        return $user->manages();
    }
}
