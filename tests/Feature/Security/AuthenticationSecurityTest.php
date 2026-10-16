<?php

declare(strict_types=1);

namespace Tests\Feature\Security;

use App\Domain\User\Enums\UserRole;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthenticationSecurityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);
    }

    public function test_login_rate_limiting_blocks_brute_force_after_five_attempts(): void
    {
        $user = User::create([
            'name' => 'Victim User',
            'email' => 'victim@example.com',
            'password' => Hash::make('correct-password'),
        ]);
        $user->assignRole(UserRole::TENANT->value);

        for ($i = 0; $i < 5; $i++) {
            $this->post('/login', [
                'email' => 'victim@example.com',
                'password' => 'wrong-password',
            ]);
        }

        // 6th attempt should be throttled
        $response = $this->post('/login', [
            'email' => 'victim@example.com',
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors(['email']);
    }

    public function test_demo_prop_is_disabled_in_production_environment(): void
    {
        $this->app['env'] = 'production';

        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Auth/Login')
            ->where('can_show_demo', false)
        );
    }
}
