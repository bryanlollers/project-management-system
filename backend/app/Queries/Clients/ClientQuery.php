<?php

namespace App\Queries\Clients;

use App\Models\Client;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClientQuery
{
    public function forUser(User $user): Builder
    {
        $query = Client::query();
        if (! $user->manages()) {
            $query->whereHas('projects.members', fn (Builder $members) => $members->where('users.id', $user->id));
        }

        return $query;
    }

    private function withSummary(Builder $query): Builder
    {
        $query->withCount('projects');

        return $query;
    }

    public function paginate(User $user, array $filters): LengthAwarePaginator
    {
        $query = $this->withSummary($this->forUser($user));
        if (isset($filters['search']) && $filters['search'] !== '') {
            $term = '%'.$filters['search'].'%';
            $query->where(function (Builder $search) use ($term): void {
                $search->where('name', 'like', $term);
                $search->orWhere('email', 'like', $term);
                $search->orWhere('company', 'like', $term);
                $search->orWhere('phone', 'like', $term);
            });
        }

        return $query->latest('id')->paginate($filters['per_page'] ?? 12);
    }

    public function detail(User $user, Client $client): Client
    {
        $record = $this->withSummary($this->forUser($user))->findOrFail($client->id);
        $record->load(['projects' => function (HasMany $projects) use ($user): void {
            $projects->withCount(['tasks', 'tasks as done_tasks_count' => fn (Builder $tasks) => $tasks->where('status', 'done')]);
            if (! $user->manages()) {
                $projects->whereHas('members', fn (Builder $members) => $members->where('users.id', $user->id));
            }
        }]);

        return $record;
    }
}
