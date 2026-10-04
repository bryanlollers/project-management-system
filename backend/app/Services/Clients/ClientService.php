<?php

namespace App\Services\Clients;

use App\Models\Client;
use App\Models\User;
use App\Queries\Clients\ClientQuery;
use App\Services\Activity\ActivityService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class ClientService
{
    public function __construct(private readonly ClientQuery $query, private readonly ActivityService $activity) {}

    public function paginate(User $actor, array $filters): LengthAwarePaginator
    {
        return $this->query->paginate($actor, $filters);
    }

    public function detail(User $actor, Client $client): Client
    {
        return $this->query->detail($actor, $client);
    }

    public function create(User $actor, array $data): Client
    {
        return DB::transaction(function () use ($actor, $data): Client {
            $client = Client::create($data);
            $this->activity->record($actor, 'Created '.$client->name);

            return $client;
        });
    }

    public function update(User $actor, Client $client, array $data): Client
    {
        return DB::transaction(function () use ($actor, $client, $data): Client {
            $client->update($data);
            $this->activity->record($actor, 'Updated '.$client->name);

            return $client->fresh();
        });
    }

    public function delete(User $actor, Client $client): void
    {
        abort_if($client->projects()->exists(), 422, 'Remove or reassign associated projects first.');
        $client->delete();
    }
}
