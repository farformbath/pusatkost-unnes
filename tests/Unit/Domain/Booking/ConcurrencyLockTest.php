<?php

declare(strict_types=1);

use App\Domain\Booking\Actions\CreateBookingAction;
use App\Domain\Booking\DTOs\BookingData;
use App\Domain\Booking\Exceptions\RoomUnavailableException;
use App\Domain\Booking\Models\Booking;
use App\Domain\Booking\Models\RoomStatusAudit;
use App\Domain\Listing\Enums\GenderType;
use App\Domain\Listing\Enums\RoomStatus;
use App\Domain\Listing\Models\Listing;
use App\Domain\Listing\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('concurrency lock: only one booking succeeds when two requests compete for the last available room', function () {
    $owner = User::factory()->create();
    $userA = User::factory()->create();
    $userB = User::factory()->create();

    $listing = Listing::create([
        'owner_id' => $owner->id,
        'name' => 'Kost Terakhir Sekaran',
        'slug' => 'kost-terakhir-sekaran',
        'gender_type' => GenderType::PUTRI,
        'description' => 'Test Concurrency',
        'address' => 'Jl. Taman Siswa',
        'area' => 'Sekaran',
    ]);

    // Room only has 1 unit left
    $room = Room::create([
        'listing_id' => $listing->id,
        'name' => 'Kamar Rebutan 101',
        'price_monthly' => 750000,
        'price_deposit' => 150000,
        'total_rooms' => 1,
        'available_rooms' => 1,
        'status' => RoomStatus::AVAILABLE,
    ]);

    $action = app(CreateBookingAction::class);

    $dtoA = new BookingData(
        userId: $userA->id,
        listingId: $listing->id,
        roomId: $room->id,
        tenantName: 'Pemesan A',
        tenantPhone: '0811111111',
        startDate: '2026-10-01',
        durationMonths: 3
    );

    $dtoB = new BookingData(
        userId: $userB->id,
        listingId: $listing->id,
        roomId: $room->id,
        tenantName: 'Pemesan B',
        tenantPhone: '0822222222',
        startDate: '2026-10-01',
        durationMonths: 6
    );

    // Request A executes inside transaction lock
    $bookingA = $action->execute($dtoA);

    expect($bookingA)->toBeInstanceOf(Booking::class)
        ->and($bookingA->tenant_name)->toBe('Pemesan A');

    // Request B attempts to book the same room
    $exceptionThrown = false;
    try {
        $action->execute($dtoB);
    } catch (RoomUnavailableException $e) {
        $exceptionThrown = true;
    }

    expect($exceptionThrown)->toBeTrue();

    // Final Database-level Invariant Assertions
    $room->refresh();
    expect($room->available_rooms)->toBe(0)
        ->and($room->status)->toBe(RoomStatus::OCCUPIED);

    // Ensure strictly 1 booking was created
    expect(Booking::where('room_id', $room->id)->count())->toBe(1);

    // Ensure audit log is accurate
    $audits = RoomStatusAudit::where('room_id', $room->id)->get();
    expect($audits)->toHaveCount(1)
        ->and($audits->first()->previous_available_count)->toBe(1)
        ->and($audits->first()->new_available_count)->toBe(0);
});
