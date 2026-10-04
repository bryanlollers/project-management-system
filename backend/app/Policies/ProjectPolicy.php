<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;

class ProjectPolicy
{
    public function create(User $user): bool
    {
        return $user->manages();
    }

    public function view(User $user, Project $project): bool
    {
        return $user->manages() || $project->members()->whereKey($user->id)->exists();
    }

    public function update(User $user, Project $project): bool
    {
        return $user->manages();
    }

    public function delete(User $user, Project $project): bool
    {
        return $user->manages();
    }
}
