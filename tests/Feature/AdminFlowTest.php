<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Domain\Listing\Enums\GenderType;
use App\Domain\Listing\Enums\ListingStatus;
use App\Domain\Listing\Models\Listing;
use App\Domain\Review\Models\Review;
use App\Domain\User\Enums\OwnerVerificationStatus;
use App\Domain\User\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminFlowTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $tenant;

    private User $owner;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => UserRole::ADMIN->value, 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => UserRole::OWNER->value, 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => UserRole::TENANT->value, 'guard_name' => 'web']);

        $this->admin = User::factory()->create([
            'email' => 'admin@unneskost.test',
        ]);
        $this->admin->assignRole(UserRole::ADMIN->value);

        $this->tenant = User::factory()->create();
        $this->tenant->assignRole(UserRole::TENANT->value);

        $this->owner = User::factory()->create([
            'owner_verification_status' => OwnerVerificationStatus::PENDING,
        ]);
        $this->owner->assignRole(UserRole::OWNER->value);
    }

    public function test_non_admin_cannot_access_admin_dashboard(): void
    {
        $this->get('/admin/dashboard')->assertRedirect('/login');

        $this->actingAs($this->tenant)
            ->get('/admin/dashboard')
            ->assertStatus(403);

        $this->actingAs($this->owner)
            ->get('/admin/dashboard')
            ->assertStatus(403);
    }

    public function test_admin_can_view_dashboard_and_metrics(): void
    {
        Listing::query()->create([
            'owner_id' => $this->owner->id,
            'name' => 'Kos Admin Test',
            'slug' => 'kos-admin-test',
            'description' => 'Deskripsi kos admin test',
            'gender_type' => GenderType::PUTRI,
            'address' => 'Sekaran',
            'area' => 'Sekaran',
            'status' => ListingStatus::PUBLISHED,
        ]);

        $response = $this->actingAs($this->admin)->get('/admin/dashboard');

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Admin/Dashboard')
            ->has('stats.total_listings')
            ->has('stats.total_owners')
            ->has('pending_owners')
        );
    }

    public function test_admin_can_view_owners_and_verify_or_reject(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/owners');
        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Admin/Owners/Index')
            ->has('owners.data')
        );

        // Verify owner
        $verifyRes = $this->actingAs($this->admin)->post("/admin/owners/{$this->owner->id}/verify");
        $verifyRes->assertRedirect();
        $this->owner->refresh();
        $this->assertSame(OwnerVerificationStatus::VERIFIED, $this->owner->owner_verification_status);

        // Reject owner
        $rejectRes = $this->actingAs($this->admin)->post("/admin/owners/{$this->owner->id}/reject");
        $rejectRes->assertRedirect();
        $this->owner->refresh();
        $this->assertSame(OwnerVerificationStatus::REJECTED, $this->owner->owner_verification_status);
    }

    public function test_admin_can_moderate_and_delete_reviews(): void
    {
        $listing = Listing::query()->create([
            'owner_id' => $this->owner->id,
            'name' => 'Kos Review Test',
            'slug' => 'kos-review-test',
            'description' => 'Deskripsi kos review test',
            'gender_type' => GenderType::PUTRA,
            'address' => 'Banaran',
            'area' => 'Banaran',
            'status' => ListingStatus::PUBLISHED,
        ]);

        $review = Review::query()->create([
            'user_id' => $this->tenant->id,
            'listing_id' => $listing->id,
            'rating' => 1,
            'comment' => 'Spam review to be deleted by admin',
        ]);

        $response = $this->actingAs($this->admin)->get('/admin/reviews');
        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Admin/Reviews/Index')
            ->has('reviews.data', 1)
        );

        // Delete review
        $deleteRes = $this->actingAs($this->admin)->delete("/admin/reviews/{$review->id}");
        $deleteRes->assertRedirect();
        $this->assertSoftDeleted('reviews', ['id' => $review->id]);
    }
}
