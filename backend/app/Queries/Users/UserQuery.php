<?php

namespace App\Queries\Users;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class UserQuery
{
    public function forUser(User $user): Builder
    {
        return User::query();
    }

    public function paginate(User $user, array $filters): LengthAwarePaginator
    {
        $query = $this->forUser($user);
        if (isset($filters['search']) && $filters['search'] !== '') {
            $term = '%'.$filters['search'].'%';
            $query->where(function (Builder $search) use ($term): void {
                $search->where('name', 'like', $term);
                $search->orWhere('email', 'like', $term);
            });
        }

        return $query->latest('id')->paginate($filters['per_page'] ?? 12);
    }

    public function detail(User $actor, User $user): User
    {
        return $user;
    }
}
