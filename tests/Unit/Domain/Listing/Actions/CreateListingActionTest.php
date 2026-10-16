<?php

declare(strict_types=1);

use App\Domain\Listing\Actions\CreateListingAction;
use App\Domain\Listing\DTOs\ListingData;
use App\Domain\Listing\Enums\GenderType;
use App\Domain\Listing\Enums\ListingStatus;
use App\Domain\Listing\Models\Facility;
use App\Domain\Listing\Models\Listing;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('it creates listing and generates unique slug', function () {
    $owner = User::factory()->create();

    $action = new CreateListingAction;

    $dto1 = new ListingData(
        ownerId: $owner->id,
        name: 'Kost Melati Indah',
        genderType: GenderType::PUTRI,
        description: 'Kost asri dekat Fakultas Ilmu Keolahragaan',
        address: 'Jl. Banaran No. 1',
        area: 'Banaran',
    );

    $listing1 = $action->handle($dto1);

    expect($listing1->slug)->toBe('kost-melati-indah-banaran')
        ->and($listing1->status)->toBe(ListingStatus::DRAFT);

    // Create a second listing with same name and area to test collision
    $dto2 = new ListingData(
        ownerId: $owner->id,
        name: 'Kost Melati Indah',
        genderType: GenderType::PUTRI,
        description: 'Cabang kedua',
        address: 'Jl. Banaran No. 2',
        area: 'Banaran',
    );

    $listing2 = $action->handle($dto2);

    expect($listing2->slug)->toBe('kost-melati-indah-banaran-1');
});

test('it syncs facilities during creation', function () {
    $owner = User::factory()->create();
    $facility = Facility::create([
        'name' => 'WiFi Cepat',
        'slug' => 'wifi-cepat',
        'icon' => 'wifi',
    ]);

    $action = new CreateListingAction;

    $dto = new ListingData(
        ownerId: $owner->id,
        name: 'Kost Mahasiswa',
        genderType: GenderType::PUTRA,
        description: 'Dekat rektorat',
        address: 'Jl. Sekaran',
        area: 'Sekaran',
        facilityIds: [$facility->id],
    );

    $listing = $action->handle($dto);

    expect($listing->facilities)->toHaveCount(1)
        ->and($listing->facilities->first()->id)->toBe($facility->id);
});
