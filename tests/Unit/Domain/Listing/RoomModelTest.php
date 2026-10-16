<?php

declare(strict_types=1);

use App\Domain\Listing\Enums\GenderType;
use App\Domain\Listing\Enums\RoomStatus;
use App\Domain\Listing\Models\Listing;
use App\Domain\Listing\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('it creates room with UUID, status enum and formatted prices', function () {
    $owner = User::factory()->create();

    $listing = Listing::create([
        'owner_id' => $owner->id,
        'name' => 'Kost Sekaran Indah',
        'slug' => 'kost-sekaran-indah',
        'gender_type' => GenderType::PUTRI,
        'description' => 'Kost tenang dekat GSG UNNES',
        'address' => 'Jl. Taman Siswa',
        'area' => 'Sekaran',
    ]);

    $room = Room::create([
        'listing_id' => $listing->id,
        'name' => 'Kamar Deluxe 01',
        'size' => '3x4 m',
        'price_monthly' => 850000,
        'price_deposit' => 200000,
        'status' => RoomStatus::AVAILABLE,
        'total_rooms' => 5,
        'available_rooms' => 3,
    ]);

    expect($room->id)->toBeString()
        ->and($room->status)->toBe(RoomStatus::AVAILABLE)
        ->and($room->formatted_price_monthly)->toBe('Rp 850.000')
        ->and($room->formatted_price_deposit)->toBe('Rp 200.000')
        ->and($room->listing->id)->toBe($listing->id);
});

test('it filters available rooms via scope', function () {
    $owner = User::factory()->create();

    $listing = Listing::create([
        'owner_id' => $owner->id,
        'name' => 'Kost Cempaka',
        'slug' => 'kost-cempaka',
        'gender_type' => GenderType::PUTRI,
        'description' => 'Kost aman',
        'address' => 'Jl. Menoreh',
        'area' => 'Sekaran',
    ]);

    Room::create([
        'listing_id' => $listing->id,
        'name' => 'Kamar Kosong',
        'price_monthly' => 700000,
        'status' => RoomStatus::AVAILABLE,
        'total_rooms' => 2,
        'available_rooms' => 1,
    ]);

    Room::create([
        'listing_id' => $listing->id,
        'name' => 'Kamar Penuh',
        'price_monthly' => 700000,
        'status' => RoomStatus::OCCUPIED,
        'total_rooms' => 2,
        'available_rooms' => 0,
    ]);

    expect(Room::available()->count())->toBe(1);
});
