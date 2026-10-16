<?php

declare(strict_types=1);

use App\Domain\Listing\Enums\GenderType;
use App\Domain\Listing\Enums\ListingStatus;
use App\Domain\Listing\Enums\RoomStatus;
use App\Domain\Listing\Models\Listing;
use App\Domain\Listing\Models\Room;
use App\Domain\User\Enums\OwnerVerificationStatus;
use App\Domain\User\Enums\UserRole;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(RoleAndPermissionSeeder::class);
});

test('owner can create new draft listing via POST /owner/kos', function () {
    $owner = User::factory()->create();
    $owner->owner_verification_status = OwnerVerificationStatus::VERIFIED;
    $owner->save();
    $owner->assignRole(UserRole::OWNER->value);

    $response = $this->actingAs($owner)->post('/owner/kos', [
        'name' => 'Kost Putri Sekaran Cantik',
        'gender_type' => 'putri',
        'description' => 'Kost khusus mahasiswi UNNES.',
        'address' => 'Jl. Taman Siswa No. 10',
        'area' => 'Sekaran',
        'rules' => 'Dilarang membawa lawan jenis masuk kamar.',
    ]);

    $listing = Listing::where('name', 'Kost Putri Sekaran Cantik')->first();

    expect($listing)->not->toBeNull()
        ->and($listing->status)->toBe(ListingStatus::DRAFT)
        ->and($listing->gender_type)->toBe(GenderType::PUTRI);

    $response->assertRedirect(route('owner.listings.edit', $listing->id));
});

test('owner can add rooms and toggle room availability', function () {
    $owner = User::factory()->create();
    $owner->owner_verification_status = OwnerVerificationStatus::VERIFIED;
    $owner->save();
    $owner->assignRole(UserRole::OWNER->value);

    $listing = Listing::create([
        'owner_id' => $owner->id,
        'name' => 'Kost Putra Banaran',
        'slug' => 'kost-putra-banaran',
        'gender_type' => GenderType::PUTRA,
        'description' => 'Dekat FT UNNES',
        'address' => 'Jl. Banaran No. 7',
        'area' => 'Banaran',
    ]);

    $response = $this->actingAs($owner)->post("/owner/kos/{$listing->id}/kamar", [
        'name' => 'Kamar Standard 01',
        'size' => '3x3 m',
        'price_monthly' => 600000,
        'price_deposit' => 100000,
        'total_rooms' => 3,
        'available_rooms' => 3,
    ]);

    $response->assertRedirect(route('owner.rooms.index', $listing->id));

    $room = Room::where('listing_id', $listing->id)->first();
    expect($room)->not->toBeNull()
        ->and($room->price_monthly)->toBe(600000)
        ->and($room->status)->toBe(RoomStatus::AVAILABLE);

    // Toggle availability
    $toggleResponse = $this->actingAs($owner)->patch("/owner/kamar/{$room->id}/toggle");
    $toggleResponse->assertRedirect();

    $room->refresh();
    expect($room->status)->toBe(RoomStatus::OCCUPIED)
        ->and($room->available_rooms)->toBe(0);
});

test('owner can upload listing images and publish listing', function () {
    Storage::fake('public');
    $owner = User::factory()->create();
    $owner->owner_verification_status = OwnerVerificationStatus::VERIFIED;
    $owner->save();
    $owner->assignRole(UserRole::OWNER->value);

    $listing = Listing::create([
        'owner_id' => $owner->id,
        'name' => 'Kost Lengkap',
        'slug' => 'kost-lengkap',
        'gender_type' => GenderType::CAMPUR,
        'description' => 'Lengkap',
        'address' => 'Jl. Sekaran',
        'area' => 'Sekaran',
    ]);

    Room::create([
        'listing_id' => $listing->id,
        'name' => 'Kamar Utama',
        'price_monthly' => 900000,
    ]);

    $file = UploadedFile::fake()->image('facade.jpg', 640, 480);

    $uploadResponse = $this->actingAs($owner)->post("/owner/kos/{$listing->id}/images", [
        'image' => $file,
        'is_primary' => true,
    ]);

    $uploadResponse->assertRedirect();
    expect($listing->images()->count())->toBe(1);

    // Publish listing
    $publishResponse = $this->actingAs($owner)->post("/owner/kos/{$listing->id}/publish");
    $publishResponse->assertRedirect(route('owner.listings.index'));

    $listing->refresh();
    expect($listing->status)->toBe(ListingStatus::PUBLISHED);
});
