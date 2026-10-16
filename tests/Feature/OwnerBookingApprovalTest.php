<?php

declare(strict_types=1);

use App\Domain\Booking\Enums\BookingStatus;
use App\Domain\Booking\Enums\PaymentStatus;
use App\Domain\Booking\Models\Booking;
use App\Domain\Listing\Enums\GenderType;
use App\Domain\Listing\Enums\RoomStatus;
use App\Domain\Listing\Models\Listing;
use App\Domain\Listing\Models\Room;
use App\Domain\User\Enums\OwnerVerificationStatus;
use App\Domain\User\Enums\UserRole;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(RoleAndPermissionSeeder::class);
});

test('owner can view incoming bookings and approve or reject them', function () {
    $owner = User::factory()->create();
    $owner->owner_verification_status = OwnerVerificationStatus::VERIFIED;
    $owner->save();
    $owner->assignRole(UserRole::OWNER->value);

    $tenant = User::factory()->create();
    $tenant->assignRole(UserRole::TENANT->value);

    $listing = Listing::create([
        'owner_id' => $owner->id,
        'name' => 'Kost Owner Sekaran',
        'slug' => 'kost-owner-sekaran',
        'gender_type' => GenderType::PUTRI,
        'description' => 'Test',
        'address' => 'Jl. Sekaran',
        'area' => 'Sekaran',
    ]);

    $room = Room::create([
        'listing_id' => $listing->id,
        'name' => 'Kamar 1',
        'price_monthly' => 700000,
        'total_rooms' => 1,
        'available_rooms' => 0,
        'status' => RoomStatus::OCCUPIED,
    ]);

    $booking = Booking::create([
        'booking_code' => 'BK-20260901-7777',
        'user_id' => $tenant->id,
        'listing_id' => $listing->id,
        'room_id' => $room->id,
        'tenant_name' => 'Sari',
        'tenant_phone' => '0822222222',
        'start_date' => '2026-10-01',
        'duration_months' => 1,
        'end_date' => '2026-11-01',
        'total_amount' => 700000,
        'deposit_amount' => 0,
        'grand_total' => 700000,
        'status' => BookingStatus::PENDING,
        'payment_status' => PaymentStatus::PENDING,
        'expired_at' => now()->addDay(),
    ]);

    $this->actingAs($owner);

    // 1. View Owner Bookings Dashboard
    $indexResponse = $this->get('/owner/bookings');
    $indexResponse->assertOk();

    // 2. Approve Booking
    $approveResponse = $this->post("/owner/bookings/{$booking->id}/approve");
    $approveResponse->assertRedirect();
    expect($booking->fresh()->status)->toBe(BookingStatus::APPROVED);

    // 3. Test Reject on another pending booking restores room
    $booking2 = Booking::create([
        'booking_code' => 'BK-20260901-8888',
        'user_id' => $tenant->id,
        'listing_id' => $listing->id,
        'room_id' => $room->id,
        'tenant_name' => 'Rina',
        'tenant_phone' => '0833333333',
        'start_date' => '2026-10-01',
        'duration_months' => 2,
        'end_date' => '2026-12-01',
        'total_amount' => 1400000,
        'deposit_amount' => 0,
        'grand_total' => 1400000,
        'status' => BookingStatus::PENDING,
        'payment_status' => PaymentStatus::PENDING,
        'expired_at' => now()->addDay(),
    ]);

    $rejectResponse = $this->post("/owner/bookings/{$booking2->id}/reject", [
        'reason' => 'Kamar sedang direnovasi',
    ]);
    $rejectResponse->assertRedirect();
    expect($booking2->fresh()->status)->toBe(BookingStatus::REJECTED);

    $room->refresh();
    expect($room->available_rooms)->toBe(1)
        ->and($room->status)->toBe(RoomStatus::AVAILABLE);
});
