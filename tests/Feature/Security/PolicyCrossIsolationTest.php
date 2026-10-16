<?php

declare(strict_types=1);

namespace Tests\Feature\Security;

use App\Domain\Booking\Enums\BookingStatus;
use App\Domain\Booking\Models\Booking;
use App\Domain\Listing\Enums\GenderType;
use App\Domain\Listing\Enums\ListingStatus;
use App\Domain\Listing\Enums\RoomStatus;
use App\Domain\Listing\Models\Listing;
use App\Domain\Listing\Models\ListingImage;
use App\Domain\Listing\Models\Room;
use App\Domain\User\Enums\OwnerVerificationStatus;
use App\Domain\User\Enums\UserRole;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PolicyCrossIsolationTest extends TestCase
{
    use RefreshDatabase;

    private User $ownerA;

    private User $ownerB;

    private User $tenantA;

    private User $tenantB;

    private User $admin;

    private Listing $listingA;

    private Listing $listingB;

    private Room $roomB;

    private Booking $bookingB;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);

        $this->ownerA = User::create(['name' => 'Owner A', 'email' => 'ownerA@example.com', 'password' => bcrypt('password')]);
        $this->ownerA->owner_verification_status = OwnerVerificationStatus::VERIFIED;
        $this->ownerA->save();
        $this->ownerA->assignRole(UserRole::OWNER->value);

        $this->ownerB = User::create(['name' => 'Owner B', 'email' => 'ownerB@example.com', 'password' => bcrypt('password')]);
        $this->ownerB->owner_verification_status = OwnerVerificationStatus::PENDING;
        $this->ownerB->save();
        $this->ownerB->assignRole(UserRole::OWNER->value);

        $this->tenantA = User::create(['name' => 'Tenant A', 'email' => 'tenantA@example.com', 'password' => bcrypt('password')]);
        $this->tenantA->assignRole(UserRole::TENANT->value);

        $this->tenantB = User::create(['name' => 'Tenant B', 'email' => 'tenantB@example.com', 'password' => bcrypt('password')]);
        $this->tenantB->assignRole(UserRole::TENANT->value);

        $this->admin = User::create(['name' => 'Admin', 'email' => 'admin@example.com', 'password' => bcrypt('password')]);
        $this->admin->assignRole(UserRole::ADMIN->value);

        $this->listingA = Listing::create([
            'owner_id' => $this->ownerA->id,
            'name' => 'Kost A',
            'slug' => 'kost-a',
            'area' => 'Sekaran',
            'gender_type' => GenderType::CAMPUR,
            'status' => ListingStatus::DRAFT,
            'address' => 'Jl. A',
            'description' => 'Desc A',
        ]);

        $this->listingB = Listing::create([
            'owner_id' => $this->ownerB->id,
            'name' => 'Kost B',
            'slug' => 'kost-b',
            'area' => 'Banaran',
            'gender_type' => GenderType::PUTRI,
            'status' => ListingStatus::DRAFT,
            'address' => 'Jl. B',
            'description' => 'Desc B',
        ]);

        $this->roomB = Room::create([
            'listing_id' => $this->listingB->id,
            'name' => 'Room B1',
            'size' => '3x3',
            'price_monthly' => 700000,
            'price_deposit' => 100000,
            'total_rooms' => 2,
            'available_rooms' => 2,
            'status' => RoomStatus::AVAILABLE,
        ]);

        ListingImage::create([
            'listing_id' => $this->listingB->id,
            'file_path' => 'demo.jpg',
            'is_primary' => true,
            'sort_order' => 1,
        ]);

        $this->bookingB = Booking::create([
            'booking_code' => 'BK-B001',
            'user_id' => $this->tenantB->id,
            'listing_id' => $this->listingB->id,
            'room_id' => $this->roomB->id,
            'tenant_name' => $this->tenantB->name,
            'tenant_phone' => '08123456789',
            'start_date' => now()->addDays(7)->toDateString(),
            'end_date' => now()->addDays(37)->toDateString(),
            'duration_months' => 1,
            'total_amount' => 700000,
            'deposit_amount' => 100000,
            'grand_total' => 800000,
            'expired_at' => now()->addHours(24),
            'status' => BookingStatus::PENDING,
        ]);
    }

    public function test_owner_a_cannot_edit_or_update_listing_b(): void
    {
        $response = $this->actingAs($this->ownerA)->put("/owner/kos/{$this->listingB->id}", [
            'name' => 'Hacked Name',
            'area' => 'Banaran',
            'gender_type' => GenderType::PUTRI->value,
            'address' => 'Jl. B',
            'description' => 'Hacked',
        ]);

        $response->assertStatus(403);
    }

    public function test_owner_a_cannot_add_room_or_toggle_room_in_listing_b(): void
    {
        $response = $this->actingAs($this->ownerA)->post("/owner/kos/{$this->listingB->id}/kamar", [
            'name' => 'Illegal Room',
            'size' => '3x3',
            'price_monthly' => 600000,
            'price_deposit' => 0,
            'total_rooms' => 1,
            'available_rooms' => 1,
        ]);

        $response->assertStatus(403);

        $toggleResponse = $this->actingAs($this->ownerA)->patch("/owner/kamar/{$this->roomB->id}/toggle");
        $toggleResponse->assertStatus(403);
    }

    public function test_owner_a_cannot_approve_or_reject_booking_b(): void
    {
        $approveResponse = $this->actingAs($this->ownerA)->post("/owner/bookings/{$this->bookingB->id}/approve");
        $approveResponse->assertStatus(403);

        $rejectResponse = $this->actingAs($this->ownerA)->post("/owner/bookings/{$this->bookingB->id}/reject", [
            'reason' => 'Unauthorized rejection',
        ]);
        $rejectResponse->assertStatus(403);
    }

    public function test_tenant_a_cannot_view_or_cancel_booking_b(): void
    {
        $viewResponse = $this->actingAs($this->tenantA)->get("/booking/{$this->bookingB->id}");
        $viewResponse->assertStatus(403);

        $cancelResponse = $this->actingAs($this->tenantA)->post("/booking/{$this->bookingB->id}/cancel");
        $cancelResponse->assertStatus(403);
    }

    public function test_pending_owner_cannot_publish_listing_until_verified(): void
    {
        // Owner B is pending verification
        $publishResponse = $this->actingAs($this->ownerB)->post("/owner/kos/{$this->listingB->id}/publish");
        $publishResponse->assertStatus(403);

        // Admin verifies Owner B
        $verifyResponse = $this->actingAs($this->admin)->post("/admin/owners/{$this->ownerB->id}/verify");
        $verifyResponse->assertRedirect();
        $this->assertTrue($this->ownerB->fresh()->isVerifiedOwner());

        // Now Owner B can publish listing
        $publishResponse2 = $this->actingAs($this->ownerB->fresh())->post("/owner/kos/{$this->listingB->id}/publish");
        $publishResponse2->assertRedirect();
        $this->assertSame(ListingStatus::PUBLISHED, $this->listingB->fresh()->status);
    }
}
