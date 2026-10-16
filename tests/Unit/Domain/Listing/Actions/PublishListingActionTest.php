<?php

declare(strict_types=1);

use App\Domain\Listing\Actions\PublishListingAction;
use App\Domain\Listing\Enums\GenderType;
use App\Domain\Listing\Enums\ListingStatus;
use App\Domain\Listing\Models\Listing;
use App\Domain\Listing\Models\ListingImage;
use App\Domain\Listing\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('it prevents publishing listing without rooms', function () {
    $owner = User::factory()->create();

    $listing = Listing::create([
        'owner_id' => $owner->id,
        'name' => 'Kost Tanpa Kamar',
        'slug' => 'kost-tanpa-kamar',
        'gender_type' => GenderType::PUTRA,
        'description' => 'Test',
        'address' => 'Jl. Sekaran',
        'area' => 'Sekaran',
    ]);

    $action = new PublishListingAction;

    expect(fn () => $action->handle($listing))
        ->toThrow(InvalidArgumentException::class, 'Kos wajib memiliki minimal 1 tipe kamar');
});

test('it prevents publishing listing without primary image', function () {
    $owner = User::factory()->create();

    $listing = Listing::create([
        'owner_id' => $owner->id,
        'name' => 'Kost Ada Kamar Tanpa Foto',
        'slug' => 'kost-ada-kamar-tanpa-foto',
        'gender_type' => GenderType::PUTRA,
        'description' => 'Test',
        'address' => 'Jl. Sekaran',
        'area' => 'Sekaran',
    ]);

    Room::create([
        'listing_id' => $listing->id,
        'name' => 'Kamar Standar',
        'price_monthly' => 600000,
    ]);

    $action = new PublishListingAction;

    expect(fn () => $action->handle($listing))
        ->toThrow(InvalidArgumentException::class, 'Kos wajib memiliki minimal 1 foto utama');
});

test('it successfully publishes valid listing', function () {
    $owner = User::factory()->create();

    $listing = Listing::create([
        'owner_id' => $owner->id,
        'name' => 'Kost Lengkap Siap Publish',
        'slug' => 'kost-lengkap-siap-publish',
        'gender_type' => GenderType::PUTRI,
        'description' => 'Test Lengkap',
        'address' => 'Jl. Sekaran Raya',
        'area' => 'Sekaran',
    ]);

    Room::create([
        'listing_id' => $listing->id,
        'name' => 'Kamar Deluxe',
        'price_monthly' => 850000,
    ]);

    ListingImage::create([
        'listing_id' => $listing->id,
        'file_path' => 'listings/primary.jpg',
        'is_primary' => true,
    ]);

    $action = new PublishListingAction;
    $published = $action->handle($listing);

    expect($published->status)->toBe(ListingStatus::PUBLISHED);
});
