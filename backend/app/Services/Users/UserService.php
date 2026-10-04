<?php

namespace App\Services\Users;

use App\Models\User;
use App\Queries\Users\UserQuery;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class UserService
{
    public function __construct(private readonly UserQuery $query) {}

    public function paginate(User $actor, array $filters): LengthAwarePaginator
    {
        return $this->query->paginate($actor, $filters);
    }

    public function detail(User $actor, User $user): User
    {
        return $this->query->detail($actor, $user);
    }

    public function create(User $actor, array $data): User
    {
        return User::create($data);
    }

    public function update(User $actor, User $user, array $data): User
    {
        return DB::transaction(function () use ($user, $data): User {
            $admins = User::where('role', 'admin')->lockForUpdate()->get();
            abort_if($user->role === 'admin' && ($data['role'] ?? 'admin') !== 'admin' && $admins->count() === 1, 422, 'The last administrator cannot be demoted.');
            $user->update($data);

            return $user->fresh();
        });
    }

    public function delete(User $actor, User $user): void
    {
        abort_if($user->id === $actor->id, 422, 'You cannot delete your own account.');
        DB::transaction(function () use ($user): void {
            $admins = User::where('role', 'admin')->lockForUpdate()->get();
            abort_if($user->role === 'admin' && $admins->count() === 1, 422, 'The last administrator cannot be deleted.');
            $user->delete();
        });
    }
}
