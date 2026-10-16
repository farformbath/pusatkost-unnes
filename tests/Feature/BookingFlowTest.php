<?php

declare(strict_types=1);

use App\Domain\Booking\Enums\BookingStatus;
use App\Domain\Booking\Enums\PaymentStatus;
use App\Domain\Booking\Models\Booking;
use App\Domain\Listing\Enums\GenderType;
use App\Domain\Listing\Enums\RoomStatus;
use App\Domain\Listing\Models\Listing;
use App\Domain\Listing\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('user can view checkout page for an available room', function () {
    $owner = User::factory()->create();

    $listing = Listing::create([
        'owner_id' => $owner->id,
        'name' => 'Kost Cantik Sekaran',
        'slug' => 'kost-cantik-sekaran',
        'gender_type' => GenderType::PUTRI,
        'description' => 'Dekat UNNES',
        'address' => 'Jl. Taman Siswa',
        'area' => 'Sekaran',
    ]);

    $room = Room::create([
        'listing_id' => $listing->id,
        'name' => 'Kamar 101',
        'price_monthly' => 800000,
        'price_deposit' => 200000,
        'total_rooms' => 2,
        'available_rooms' => 2,
        'status' => RoomStatus::AVAILABLE,
    ]);

    $response = $this->get("/booking/{$room->id}/checkout");
    $response->assertOk();
});

test('user can submit booking and be redirected to invoice show page', function () {
    $owner = User::factory()->create();

    $listing = Listing::create([
        'owner_id' => $owner->id,
        'name' => 'Kost Prima Sekaran',
        'slug' => 'kost-prima-sekaran',
        'gender_type' => GenderType::PUTRA,
        'description' => 'Deskripsi',
        'address' => 'Jl. Sekaran',
        'area' => 'Sekaran',
    ]);

    $room = Room::create([
        'listing_id' => $listing->id,
        'name' => 'Kamar VIP',
        'price_monthly' => 900000,
        'price_deposit' => 100000,
        'total_rooms' => 2,
        'available_rooms' => 2,
        'status' => RoomStatus::AVAILABLE,
    ]);

    $payload = [
        'listing_id' => $listing->id,
        'room_id' => $room->id,
        'tenant_name' => 'Ahmad Dahlan',
        'tenant_phone' => '08123456789',
        'start_date' => now()->addDays(5)->toDateString(),
        'duration_months' => 3,
        'notes' => 'Tolong disiapkan kunci kamar',
    ];

    $response = $this->post('/booking', $payload);

    $booking = Booking::where('room_id', $room->id)->first();
    expect($booking)->not->toBeNull()
        ->and($booking->grand_total)->toBe(2800000);

    $response->assertRedirect("/booking/{$booking->id}");

    $detailResponse = $this->get("/booking/{$booking->id}");
    $detailResponse->assertOk();
});

test('user can simulate payment and cancel booking', function () {
    $owner = User::factory()->create();
    $tenant = User::factory()->create();

    $listing = Listing::create([
        'owner_id' => $owner->id,
        'name' => 'Kost Baru',
        'slug' => 'kost-baru',
        'gender_type' => GenderType::CAMPUR,
        'description' => 'Test',
        'address' => 'Jl. Banaran',
        'area' => 'Banaran',
    ]);

    $room = Room::create([
        'listing_id' => $listing->id,
        'name' => 'Kamar 01',
        'price_monthly' => 500000,
        'total_rooms' => 1,
        'available_rooms' => 0,
        'status' => RoomStatus::OCCUPIED,
    ]);

    $booking = Booking::create([
        'booking_code' => 'BK-20260901-9999',
        'user_id' => $tenant->id,
        'listing_id' => $listing->id,
        'room_id' => $room->id,
        'tenant_name' => 'Dian',
        'tenant_phone' => '0811111111',
        'start_date' => '2026-10-01',
        'duration_months' => 1,
        'end_date' => '2026-11-01',
        'total_amount' => 500000,
        'deposit_amount' => 0,
        'grand_total' => 500000,
        'status' => BookingStatus::PENDING,
        'payment_status' => PaymentStatus::PENDING,
        'expired_at' => now()->addDay(),
    ]);

    // 1. Simulate Payment
    $payResponse = $this->post("/booking/{$booking->id}/payment", [
        'payment_channel' => 'bca_va',
    ]);
    $payResponse->assertRedirect();
    expect($booking->fresh()->payment_status)->toBe(PaymentStatus::PAID);

    // 2. User Cancel Booking
    $cancelResponse = $this->post("/booking/{$booking->id}/cancel");
    $cancelResponse->assertRedirect();
    expect($booking->fresh()->status)->toBe(BookingStatus::CANCELLED);

    // Ensure room availability restored
    $room->refresh();
    expect($room->available_rooms)->toBe(1)
        ->and($room->status)->toBe(RoomStatus::AVAILABLE);
});
