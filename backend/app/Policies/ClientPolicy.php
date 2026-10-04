<?php

namespace App\Policies;

use App\Models\Client;
use App\Models\User;

class ClientPolicy
{
    public function view(User $user, Client $client): bool
    {
        return $user->manages() || $client->projects()->whereHas('members', fn ($query) => $query->where('users.id', $user->id))->exists();
    }

    public function create(User $user): bool
    {
        return $user->manages();
    }

    public function update(User $user, Client $client): bool
    {
        return $user->manages();
    }

    public function delete(User $user, Client $client): bool
    {
        return $user->manages();
    }
}
