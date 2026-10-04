<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function view(User $actor, User $user): bool
    {
        return true;
    }

    public function create(User $actor): bool
    {
        return $actor->role === 'admin';
    }

    public function update(User $actor, User $user): bool
    {
        return $actor->role === 'admin';
    }

    public function delete(User $actor, User $user): bool
    {
        return $actor->role === 'admin';
    }
}
