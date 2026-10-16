<?php

declare(strict_types=1);

use App\Domain\Listing\Actions\CreateRoomAction;
use App\Domain\Listing\DTOs\RoomData;
use App\Domain\Listing\Enums\GenderType;
use App\Domain\Listing\Enums\RoomStatus;
use App\Domain\Listing\Models\Listing;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('it creates room and validates price and availability invariants', function () {
    $owner = User::factory()->create();

    $listing = Listing::create([
        'owner_id' => $owner->id,
        'name' => 'Kost Pelangi',
        'slug' => 'kost-pelangi',
        'gender_type' => GenderType::CAMPUR,
        'description' => 'Test',
        'address' => 'Jl. Taman Siswa',
        'area' => 'Sekaran',
    ]);

    $action = new CreateRoomAction;

    $dto = new RoomData(
        listingId: $listing->id,
        name: 'Kamar A',
        size: '3x3 m',
        priceMonthly: 750000,
        priceDeposit: 150000,
        totalRooms: 4,
        availableRooms: 2,
    );

    $room = $action->handle($dto);

    expect($room->name)->toBe('Kamar A')
        ->and($room->price_monthly)->toBe(750000)
        ->and($room->status)->toBe(RoomStatus::AVAILABLE);
});

test('it throws exception on negative price', function () {
    $owner = User::factory()->create();

    $listing = Listing::create([
        'owner_id' => $owner->id,
        'name' => 'Kost Pelangi 2',
        'slug' => 'kost-pelangi-2',
        'gender_type' => GenderType::CAMPUR,
        'description' => 'Test',
        'address' => 'Jl. Taman Siswa',
        'area' => 'Sekaran',
    ]);

    $action = new CreateRoomAction;

    $dto = new RoomData(
        listingId: $listing->id,
        name: 'Kamar Negatif',
        size: '3x3 m',
        priceMonthly: -1000,
    );

    expect(fn () => $action->handle($dto))
        ->toThrow(InvalidArgumentException::class, 'Harga sewa bulanan tidak boleh bernilai negatif.');
});
