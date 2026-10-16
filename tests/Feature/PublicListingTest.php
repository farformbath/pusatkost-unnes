<?php

declare(strict_types=1);

use App\Domain\Listing\Enums\GenderType;
use App\Domain\Listing\Enums\ListingStatus;
use App\Domain\Listing\Models\Listing;
use App\Domain\Listing\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('public users can view published listings on /kos', function () {
    $owner = User::factory()->create();

    $publishedListing = Listing::create([
        'owner_id' => $owner->id,
        'name' => 'Kost Melati Sekaran',
        'slug' => 'kost-melati-sekaran',
        'gender_type' => GenderType::PUTRI,
        'description' => 'Kost asri',
        'address' => 'Jl. Taman Siswa',
        'area' => 'Sekaran',
        'status' => ListingStatus::PUBLISHED,
    ]);

    $draftListing = Listing::create([
        'owner_id' => $owner->id,
        'name' => 'Kost Draft Rahasia',
        'slug' => 'kost-draft-rahasia',
        'gender_type' => GenderType::PUTRA,
        'description' => 'Draft',
        'address' => 'Jl. Banaran',
        'area' => 'Banaran',
        'status' => ListingStatus::DRAFT,
    ]);

    $response = $this->get('/kos');

    $response->assertStatus(200);
});

test('public users can view detail of published listing by slug', function () {
    $owner = User::factory()->create();

    $listing = Listing::create([
        'owner_id' => $owner->id,
        'name' => 'Kost Graha Sekaran',
        'slug' => 'kost-graha-sekaran',
        'gender_type' => GenderType::CAMPUR,
        'description' => 'Dekat Rektorat UNNES',
        'address' => 'Jl. Raya Sekaran',
        'area' => 'Sekaran',
        'status' => ListingStatus::PUBLISHED,
    ]);

    Room::create([
        'listing_id' => $listing->id,
        'name' => 'Kamar Tipe A',
        'price_monthly' => 800000,
    ]);

    $response = $this->get('/kos/kost-graha-sekaran');

    $response->assertStatus(200);
});

test('returns 404 when viewing draft listing as public user', function () {
    $owner = User::factory()->create();

    Listing::create([
        'owner_id' => $owner->id,
        'name' => 'Kost Belum Siap',
        'slug' => 'kost-belum-siap',
        'gender_type' => GenderType::PUTRI,
        'description' => 'Draft',
        'address' => 'Jl. Sekaran',
        'area' => 'Sekaran',
        'status' => ListingStatus::DRAFT,
    ]);

    $response = $this->get('/kos/kost-belum-siap');

    $response->assertStatus(404);
});
