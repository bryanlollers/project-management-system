<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesWorkspaceFixtures;
use Tests\TestCase;

class AuthRegressionTest extends TestCase
{
    use CreatesWorkspaceFixtures, RefreshDatabase;

    public function test_login_logout_and_revoked_token(): void
    {
        $u = $this->person();
        $this->postJson('/api/login', ['email' => $u->email, 'password' => 'wrong'])->assertUnprocessable();
        $token = $this->postJson('/api/login', ['email' => $u->email, 'password' => 'StrongPassword!2026'])->assertOk()->json('token');
        $this->withToken($token)->getJson('/api/me')->assertOk()->assertJsonPath('user.role', 'admin');
        $this->withToken($token)->postJson('/api/logout')->assertNoContent();
        $this->assertDatabaseCount('personal_access_tokens', 0);
        $this->app['auth']->forgetGuards();
        $this->withToken($token)->getJson('/api/me')->assertUnauthorized();
    }

    public function test_guests_cannot_access_workspace(): void
    {
        $this->getJson('/api/projects')->assertUnauthorized();
    }
}
