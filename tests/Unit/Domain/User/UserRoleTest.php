<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\User;

use App\Domain\User\Actions\RegisterUserAction;
use App\Domain\User\DTOs\RegisterUserData;
use App\Domain\User\Enums\UserRole;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserRoleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);
    }

    public function test_it_registers_tenant_and_assigns_tenant_role(): void
    {
        $action = new RegisterUserAction;
        $user = $action->execute(new RegisterUserData(
            name: 'Budi Mahasiswa',
            email: 'budi@students.unnes.ac.id',
            password: 'password123',
            role: UserRole::TENANT,
            phone: '08123456789'
        ));

        $this->assertTrue($user->isTenant());
        $this->assertFalse($user->isOwner());
        $this->assertTrue($user->can('book-rooms'));
        $this->assertFalse($user->can('manage-listings'));
    }

    public function test_it_registers_owner_and_assigns_owner_role(): void
    {
        $action = new RegisterUserAction;
        $user = $action->execute(new RegisterUserData(
            name: 'Haji Sukardi',
            email: 'sukardi@unneskost.com',
            password: 'password123',
            role: UserRole::OWNER,
            phone: '08987654321'
        ));

        $this->assertTrue($user->isOwner());
        $this->assertFalse($user->isTenant());
        $this->assertTrue($user->can('manage-listings'));
        $this->assertTrue($user->can('approve-bookings'));
    }
}
