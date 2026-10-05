<?php

namespace Tests\Feature\Users;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UserApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_account_crud_preserves_password_on_partial_update_and_validates_unique_email(): void
    {
        $admin = User::factory()->create([
            'name' => 'Workspace Administrator',
            'email' => 'admin@example.com',
            'role' => 'admin',
            'password' => 'StrongPassword!2026',
        ]);
        Sanctum::actingAs($admin);
        $response = $this->postJson('/api/users', ['name' => 'Pat', 'email' => 'pat@example.com', 'role' => 'staff', 'password' => 'StrongPassword!2026'])
            ->assertCreated()->assertJsonMissingPath('data.password');
        $id = $response->json('data.id');
        $this->patchJson('/api/users/'.$id, ['email' => 'pat@example.com', 'name' => 'Pat Updated'])->assertOk();
        $this->assertTrue(Hash::check('StrongPassword!2026', User::findOrFail($id)->password));
        $this->patchJson('/api/users/'.$id, ['email' => $admin->email])->assertUnprocessable();
        $this->getJson('/api/users?search=Pat')->assertOk()
            ->assertJsonPath('meta.total', 1)
            ->assertJsonPath('data.0.id', $id)
            ->assertJsonMissingPath('data.0.password');
        $this->deleteJson('/api/users/'.$id)->assertNoContent();
        $this->assertDatabaseMissing('users', ['id' => $id]);
    }
}
