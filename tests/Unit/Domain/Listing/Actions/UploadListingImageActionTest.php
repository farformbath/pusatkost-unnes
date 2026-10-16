<?php

declare(strict_types=1);

use App\Domain\Listing\Actions\UploadListingImageAction;
use App\Domain\Listing\Enums\GenderType;
use App\Domain\Listing\Models\Listing;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

test('it uploads image and automatically sets first image as primary', function () {
    Storage::fake('public');
    $owner = User::factory()->create();

    $listing = Listing::create([
        'owner_id' => $owner->id,
        'name' => 'Kost Asri',
        'slug' => 'kost-asri',
        'gender_type' => GenderType::PUTRA,
        'description' => 'Test',
        'address' => 'Jl. Sekaran',
        'area' => 'Sekaran',
    ]);

    $file = UploadedFile::fake()->image('facade.jpg', 800, 600);
    $action = new UploadListingImageAction;

    $image = $action->handle($listing, $file);

    expect($image->is_primary)->toBeTrue()
        ->and($image->sort_order)->toBe(1);

    Storage::disk('public')->assertExists($image->file_path);
});
