<?php

declare(strict_types=1);

namespace Tests\Feature\Security;

use App\Domain\User\Enums\OwnerVerificationStatus;
use App\Domain\User\Enums\UserRole;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Database\Eloquent\MassAssignmentException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PrivilegeEscalationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);
    }

    public function test_registration_cannot_create_admin(): void
    {
        $response = $this->post('/register', [
            'name' => 'Hacker Admin',
            'email' => 'hacker@example.com',
            'phone' => '08123456789',
            'role' => 'admin',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors(['role']);
        $this->assertDatabaseMissing('users', ['email' => 'hacker@example.com']);
    }

    public function test_owner_registration_sets_verification_status_to_pending(): void
    {
        $response = $this->post('/register', [
            'name' => 'Mitra Baru',
            'email' => 'mitra@example.com',
            'phone' => '08123456789',
            'role' => UserRole::OWNER->value,
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect('/owner/kos/create');
        $user = User::where('email', 'mitra@example.com')->first();
        $this->assertNotNull($user);
        $this->assertTrue($user->isOwner());
        $this->assertSame(OwnerVerificationStatus::PENDING, $user->owner_verification_status);
        $this->assertFalse($user->isVerifiedOwner());
    }

    public function test_tenant_cannot_self_promote_via_profile_update(): void
    {
        $tenant = User::create([
            'name' => 'Tenant User',
            'email' => 'tenant@example.com',
            'password' => bcrypt('password'),
        ]);
        $tenant->assignRole(UserRole::TENANT->value);

        $response = $this->actingAs($tenant)->put('/profile', [
            'name' => 'Tenant Updated',
            'email' => 'tenant@example.com',
            'role' => 'admin',
        ]);

        $response->assertRedirect();
        $tenant->refresh();
        $this->assertTrue($tenant->isTenant());
        $this->assertFalse($tenant->isAdmin());
        $this->assertFalse($tenant->isOwner());
        $this->assertNull($tenant->owner_verification_status);
    }

    public function test_mass_assignment_protection_on_user_model(): void
    {
        $this->expectException(MassAssignmentException::class);

        User::create([
            'name' => 'Direct Create',
            'email' => 'direct@example.com',
            'password' => bcrypt('password'),
            'owner_verification_status' => OwnerVerificationStatus::VERIFIED->value,
        ]);
    }
}
