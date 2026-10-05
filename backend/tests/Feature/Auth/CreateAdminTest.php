<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CreateAdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_be_created_without_demo_seeding(): void
    {
        $this->artisan('app:create-admin')
            ->expectsQuestion('Name', 'First admin')
            ->expectsQuestion('Email', 'admin@example.com')
            ->expectsQuestion('Password (12-128 characters)', 'SecurePassword123!')
            ->expectsQuestion('Confirm password', 'SecurePassword123!')
            ->expectsOutput('Administrator created.')
            ->assertSuccessful();

        $admin = User::sole();
        $this->assertSame('admin', $admin->role);
        $this->assertTrue(Hash::check('SecurePassword123!', $admin->password));
    }

    public function test_invalid_credentials_do_not_create_an_account(): void
    {
        $this->artisan('app:create-admin')
            ->expectsQuestion('Name', 'First admin')
            ->expectsQuestion('Email', 'invalid-email')
            ->expectsQuestion('Password (12-128 characters)', 'short')
            ->expectsQuestion('Confirm password', 'different')
            ->assertFailed();

        $this->assertDatabaseCount('users', 0);
    }
}
