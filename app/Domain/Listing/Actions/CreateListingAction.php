<?php

declare(strict_types=1);

namespace App\Domain\Listing\Actions;

use App\Domain\Listing\DTOs\ListingData;
use App\Domain\Listing\Enums\ListingStatus;
use App\Domain\Listing\Models\Listing;
use Illuminate\Support\Str;

final class CreateListingAction
{
    public function handle(ListingData $data): Listing
    {
        $baseSlug = Str::slug($data->name.' '.$data->area);
        $slug = $baseSlug;
        $counter = 1;

        while (Listing::where('slug', $slug)->exists()) {
            $slug = "{$baseSlug}-{$counter}";
            $counter++;
        }

        $listing = Listing::create([
            'owner_id' => $data->ownerId,
            'name' => $data->name,
            'slug' => $slug,
            'gender_type' => $data->genderType,
            'description' => $data->description,
            'address' => $data->address,
            'area' => $data->area,
            'latitude' => $data->latitude,
            'longitude' => $data->longitude,
            'rules' => $data->rules,
            'status' => ListingStatus::DRAFT,
        ]);

        if (! empty($data->facilityIds)) {
            $listing->facilities()->sync($data->facilityIds);
        }

        return $listing->load('facilities');
    }
}
