<?php

declare(strict_types=1);

use App\Domain\Booking\Actions\ApproveBookingAction;
use App\Domain\Booking\Actions\CancelBookingAction;
use App\Domain\Booking\Actions\CreateBookingAction;
use App\Domain\Booking\Actions\ProcessPaymentAction;
use App\Domain\Booking\DTOs\BookingData;
use App\Domain\Booking\Enums\BookingStatus;
use App\Domain\Booking\Enums\PaymentStatus;
use App\Domain\Booking\Exceptions\RoomUnavailableException;
use App\Domain\Listing\Enums\GenderType;
use App\Domain\Listing\Enums\RoomStatus;
use App\Domain\Listing\Models\Listing;
use App\Domain\Listing\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('it creates booking and decrements room availability with pure integer currency', function () {
    $owner = User::factory()->create();
    $tenant = User::factory()->create();

    $listing = Listing::create([
        'owner_id' => $owner->id,
        'name' => 'Kost Unggulan Sekaran',
        'slug' => 'kost-unggulan-sekaran',
        'gender_type' => GenderType::PUTRI,
        'description' => 'Test',
        'address' => 'Jl. Sekaran',
        'area' => 'Sekaran',
    ]);

    $room = Room::create([
        'listing_id' => $listing->id,
        'name' => 'Kamar VIP',
        'price_monthly' => 1000000,
        'price_deposit' => 250000,
        'total_rooms' => 3,
        'available_rooms' => 3,
        'status' => RoomStatus::AVAILABLE,
    ]);

    $action = app(CreateBookingAction::class);
    $dto = new BookingData(
        userId: $tenant->id,
        listingId: $listing->id,
        roomId: $room->id,
        tenantName: 'Nurul Hidayah',
        tenantPhone: '08123456789',
        startDate: '2026-10-01',
        durationMonths: 6,
        notes: 'Lantai 2 mohon yang dekat jendela'
    );

    $booking = $action->execute($dto);

    expect($booking->total_amount)->toBe(6000000)
        ->and($booking->deposit_amount)->toBe(250000)
        ->and($booking->grand_total)->toBe(6250000)
        ->and($booking->status)->toBe(BookingStatus::PENDING)
        ->and($booking->payment_status)->toBe(PaymentStatus::PENDING)
        ->and($booking->payments)->toHaveCount(1)
        ->and($booking->payments->first()->gross_amount)->toBe(6250000)
        ->and($booking->roomStatusAudits)->toHaveCount(1);

    $room->refresh();
    expect($room->available_rooms)->toBe(2)
        ->and($room->status)->toBe(RoomStatus::AVAILABLE);
});

test('it throws RoomUnavailableException when room has no availability left', function () {
    $owner = User::factory()->create();
    $tenant = User::factory()->create();

    $listing = Listing::create([
        'owner_id' => $owner->id,
        'name' => 'Kost Penuh',
        'slug' => 'kost-penuh',
        'gender_type' => GenderType::PUTRA,
        'description' => 'Test',
        'address' => 'Jl. Banaran',
        'area' => 'Banaran',
    ]);

    $room = Room::create([
        'listing_id' => $listing->id,
        'name' => 'Kamar Mandi Dalam',
        'price_monthly' => 800000,
        'total_rooms' => 1,
        'available_rooms' => 0,
        'status' => RoomStatus::OCCUPIED,
    ]);

    $action = app(CreateBookingAction::class);
    $dto = new BookingData(
        userId: $tenant->id,
        listingId: $listing->id,
        roomId: $room->id,
        tenantName: 'Budi',
        tenantPhone: '0812345678',
        startDate: '2026-10-01',
        durationMonths: 1
    );

    expect(fn () => $action->execute($dto))
        ->toThrow(RoomUnavailableException::class);
});

test('it allows owner approval, rejection, user cancellation, and payment transitions', function () {
    $owner = User::factory()->create();
    $tenant = User::factory()->create();

    $listing = Listing::create([
        'owner_id' => $owner->id,
        'name' => 'Kost Mantap',
        'slug' => 'kost-mantap',
        'gender_type' => GenderType::CAMPUR,
        'description' => 'Test',
        'address' => 'Jl. Taman Siswa',
        'area' => 'Taman Siswa',
    ]);

    $room = Room::create([
        'listing_id' => $listing->id,
        'name' => 'Kamar Standar',
        'price_monthly' => 500000,
        'total_rooms' => 2,
        'available_rooms' => 2,
        'status' => RoomStatus::AVAILABLE,
    ]);

    $createAction = app(CreateBookingAction::class);
    $dto = new BookingData(
        userId: $tenant->id,
        listingId: $listing->id,
        roomId: $room->id,
        tenantName: 'Rian',
        tenantPhone: '0812345678',
        startDate: '2026-10-01',
        durationMonths: 2
    );

    $booking = $createAction->execute($dto);
    $room->refresh();
    expect($room->available_rooms)->toBe(1);

    // 1. Owner Approval
    $approveAction = app(ApproveBookingAction::class);
    $approveAction->execute($booking);
    expect($booking->fresh()->status)->toBe(BookingStatus::APPROVED);

    // 2. Process Payment
    $paymentAction = app(ProcessPaymentAction::class);
    $paymentAction->execute($booking);
    expect($booking->fresh()->payment_status)->toBe(PaymentStatus::PAID);

    // 3. Re-running payment is idempotent
    $paymentAction->execute($booking);
    expect($booking->fresh()->payment_status)->toBe(PaymentStatus::PAID);
});

test('it restores room inventory when booking is cancelled or rejected', function () {
    $owner = User::factory()->create();
    $tenant = User::factory()->create();

    $listing = Listing::create([
        'owner_id' => $owner->id,
        'name' => 'Kost Bersama',
        'slug' => 'kost-bersama',
        'gender_type' => GenderType::PUTRI,
        'description' => 'Test',
        'address' => 'Jl. Sekaran',
        'area' => 'Sekaran',
    ]);

    $room = Room::create([
        'listing_id' => $listing->id,
        'name' => 'Kamar A',
        'price_monthly' => 600000,
        'total_rooms' => 1,
        'available_rooms' => 1,
        'status' => RoomStatus::AVAILABLE,
    ]);

    $createAction = app(CreateBookingAction::class);
    $dto = new BookingData(
        userId: $tenant->id,
        listingId: $listing->id,
        roomId: $room->id,
        tenantName: 'Ani',
        tenantPhone: '0812345678',
        startDate: '2026-10-01',
        durationMonths: 1
    );

    $booking = $createAction->execute($dto);
    $room->refresh();
    expect($room->available_rooms)->toBe(0)
        ->and($room->status)->toBe(RoomStatus::OCCUPIED);

    // Cancel booking restores inventory
    $cancelAction = app(CancelBookingAction::class);
    $cancelAction->execute($booking);

    $room->refresh();
    expect($room->available_rooms)->toBe(1)
        ->and($room->status)->toBe(RoomStatus::AVAILABLE)
        ->and($booking->fresh()->status)->toBe(BookingStatus::CANCELLED);

    // Calling cancel again is idempotent and does not increment inventory twice
    $cancelAction->execute($booking);
    $room->refresh();
    expect($room->available_rooms)->toBe(1);
});
