<?php

namespace Tests\Feature\Users;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\Concerns\CreatesWorkspaceFixtures;
use Tests\TestCase;

class UsersRegressionTest extends TestCase
{
    use CreatesWorkspaceFixtures, RefreshDatabase;

    public function test_manager_cannot_manage_accounts_and_last_admin_is_protected(): void
    {
        $admin = $this->person();
        $manager = $this->person('manager');
        Sanctum::actingAs($manager);
        $this->postJson('/api/users', ['name' => 'User', 'email' => 'new@example.com', 'password' => 'StrongPassword!2026', 'role' => 'staff'])->assertForbidden();
        Sanctum::actingAs($admin);
        $this->patchJson('/api/users/'.$admin->id, ['role' => 'staff'])->assertUnprocessable();
        $this->deleteJson('/api/users/'.$admin->id)->assertUnprocessable();
    }
}
