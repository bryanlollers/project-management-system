<?php

namespace Tests\Concerns;

use App\Models\Client;
use App\Models\Project;
use App\Models\User;

trait CreatesWorkspaceFixtures
{
    private function person(string $role = 'admin'): User
    {
        return User::factory()->create(['role' => $role, 'password' => 'StrongPassword!2026']);
    }

    private function project(): Project
    {
        $client = Client::create(['name' => 'Test client', 'email' => 'hello@example.com']);

        return Project::create(['name' => 'Test project', 'client_id' => $client->id, 'status' => 'active']);
    }
}
