<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Domain\User\Enums\UserRole;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);
    }

    public function test_user_can_register_as_tenant(): void
    {
        $response = $this->post('/register', [
            'name' => 'Budi Santoso',
            'email' => 'budi@example.com',
            'phone' => '081234567890',
            'role' => UserRole::TENANT->value,
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect('/kos');
        $this->assertAuthenticated();

        $user = User::query()->where('email', 'budi@example.com')->first();
        $this->assertNotNull($user);
        $this->assertTrue($user->isTenant());
    }

    public function test_user_can_register_as_owner(): void
    {
        $response = $this->post('/register', [
            'name' => 'H. Sukardi',
            'email' => 'sukardi@example.com',
            'phone' => '08987654321',
            'role' => UserRole::OWNER->value,
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect('/owner/kos/create');
        $this->assertAuthenticated();

        $user = User::query()->where('email', 'sukardi@example.com')->first();
        $this->assertNotNull($user);
        $this->assertTrue($user->isOwner());
    }

    public function test_user_can_login_and_logout(): void
    {
        $user = User::query()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);
        $user->assignRole(UserRole::TENANT->value);

        // Login
        $loginResponse = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $loginResponse->assertRedirect('/kos');
        $this->assertAuthenticatedAs($user);

        // Logout
        $logoutResponse = $this->post('/logout');
        $logoutResponse->assertRedirect('/');
        $this->assertGuest();
    }

    public function test_user_can_update_profile_and_password(): void
    {
        $user = User::query()->create([
            'name' => 'Old Name',
            'email' => 'old@example.com',
            'password' => Hash::make('oldpassword'),
        ]);
        $user->assignRole(UserRole::TENANT->value);

        // Update Profile
        $profileResponse = $this->actingAs($user)->put('/profile', [
            'name' => 'New Name',
            'email' => 'new@example.com',
            'phone' => '08111222333',
        ]);

        $profileResponse->assertRedirect();
        $user->refresh();
        $this->assertSame('New Name', $user->name);
        $this->assertSame('new@example.com', $user->email);

        // Update Password
        $passwordResponse = $this->actingAs($user)->put('/profile/password', [
            'current_password' => 'oldpassword',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $passwordResponse->assertRedirect();
        $user->refresh();
        $this->assertTrue(Hash::check('newpassword123', $user->password));
    }
}
