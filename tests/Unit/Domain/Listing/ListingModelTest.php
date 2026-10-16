<?php

declare(strict_types=1);

use App\Domain\Listing\Enums\GenderType;
use App\Domain\Listing\Enums\ListingStatus;
use App\Domain\Listing\Models\Facility;
use App\Domain\Listing\Models\Listing;
use App\Domain\Listing\Models\ListingImage;
use App\Domain\Listing\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('it creates listing with UUID and default status', function () {
    $owner = User::factory()->create();

    $listing = Listing::create([
        'owner_id' => $owner->id,
        'name' => 'Kost Putri Sekaran Asri',
        'slug' => 'kost-putri-sekaran-asri',
        'gender_type' => GenderType::PUTRI,
        'description' => 'Kost nyaman dekat kampus UNNES',
        'address' => 'Jl. Taman Siswa No. 12, Sekaran',
        'area' => 'Sekaran',
    ]);

    expect($listing->id)->toBeString()
        ->and($listing->status)->toBe(ListingStatus::DRAFT)
        ->and($listing->gender_type)->toBe(GenderType::PUTRI)
        ->and($listing->owner->id)->toBe($owner->id);
});

test('it computes starting price based on available rooms', function () {
    $owner = User::factory()->create();

    $listing = Listing::create([
        'owner_id' => $owner->id,
        'name' => 'Kost Putra Melati',
        'slug' => 'kost-putra-melati',
        'gender_type' => GenderType::PUTRA,
        'description' => 'Kost putra bersih',
        'address' => 'Jl. Banaran No. 5',
        'area' => 'Banaran',
    ]);

    Room::create([
        'listing_id' => $listing->id,
        'name' => 'Kamar Standar',
        'size' => '3x3 m',
        'price_monthly' => 650000,
        'price_deposit' => 100000,
    ]);

    Room::create([
        'listing_id' => $listing->id,
        'name' => 'Kamar VIP',
        'size' => '4x4 m',
        'price_monthly' => 950000,
        'price_deposit' => 200000,
    ]);

    expect($listing->starting_price)->toBe(650000)
        ->and($listing->formatted_starting_price)->toBe('Rp 650.000');
});

test('it associates facilities and images with listing', function () {
    $owner = User::factory()->create();

    $listing = Listing::create([
        'owner_id' => $owner->id,
        'name' => 'Kost Campur Harmoni',
        'slug' => 'kost-campur-harmoni',
        'gender_type' => GenderType::CAMPUR,
        'description' => 'Dekat Fakultas Bahasa dan Seni',
        'address' => 'Jl. Gang Kelapa No. 3, Sekaran',
        'area' => 'Sekaran',
    ]);

    $facility = Facility::create([
        'name' => 'WiFi Cepat 50Mbps',
        'slug' => 'wifi-cepat-50mbps',
        'icon' => 'wifi',
        'category' => 'property',
    ]);

    $listing->facilities()->attach($facility->id);

    $image = ListingImage::create([
        'listing_id' => $listing->id,
        'file_path' => 'listings/sample-facade.jpg',
        'is_primary' => true,
        'sort_order' => 1,
    ]);

    expect($listing->facilities)->toHaveCount(1)
        ->and($listing->facilities->first()->name)->toBe('WiFi Cepat 50Mbps')
        ->and($listing->primaryImage->id)->toBe($image->id);
});
