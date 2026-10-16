<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\User;

use App\Domain\Listing\Enums\GenderType;
use App\Domain\Listing\Enums\ListingStatus;
use App\Domain\Listing\Models\Listing;
use App\Domain\Listing\Policies\ListingPolicy;
use App\Domain\User\Enums\UserRole;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PolicyAccessControlTest extends TestCase
{
    use RefreshDatabase;

    private User $owner1;

    private User $owner2;

    private User $tenant;

    private Listing $listing;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);

        $this->owner1 = User::query()->create([
            'name' => 'Owner 1',
            'email' => 'owner1@example.com',
            'password' => bcrypt('password'),
        ]);
        $this->owner1->assignRole(UserRole::OWNER->value);

        $this->owner2 = User::query()->create([
            'name' => 'Owner 2',
            'email' => 'owner2@example.com',
            'password' => bcrypt('password'),
        ]);
        $this->owner2->assignRole(UserRole::OWNER->value);

        $this->tenant = User::query()->create([
            'name' => 'Tenant',
            'email' => 'tenant@example.com',
            'password' => bcrypt('password'),
        ]);
        $this->tenant->assignRole(UserRole::TENANT->value);

        $this->listing = Listing::query()->create([
            'owner_id' => $this->owner1->id,
            'name' => 'Kost Melati',
            'slug' => 'kost-melati',
            'area' => 'Sekaran',
            'gender_type' => GenderType::PUTRI,
            'status' => ListingStatus::PUBLISHED,
            'address' => 'Jl. Sekaran',
            'description' => 'Desc',
        ]);
    }

    public function test_listing_policy_enforces_ownership(): void
    {
        $policy = new ListingPolicy;

        // Owner 1 can update own listing
        $this->assertTrue($policy->update($this->owner1, $this->listing));

        // Owner 2 cannot update Owner 1's listing
        $this->assertFalse($policy->update($this->owner2, $this->listing));

        // Tenant cannot update listing
        $this->assertFalse($policy->update($this->tenant, $this->listing));
    }
}
