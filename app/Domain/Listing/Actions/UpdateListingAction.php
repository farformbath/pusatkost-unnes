<?php

declare(strict_types=1);

namespace App\Domain\Listing\Actions;

use App\Domain\Listing\DTOs\ListingData;
use App\Domain\Listing\Models\Listing;
use Illuminate\Support\Str;

final class UpdateListingAction
{
    public function handle(Listing $listing, ListingData $data): Listing
    {
        $slug = $listing->slug;

        if ($listing->name !== $data->name || $listing->area !== $data->area) {
            $baseSlug = Str::slug($data->name.' '.$data->area);
            $slug = $baseSlug;
            $counter = 1;

            while (Listing::where('slug', $slug)->where('id', '!=', $listing->id)->exists()) {
                $slug = "{$baseSlug}-{$counter}";
                $counter++;
            }
        }

        $listing->update([
            'name' => $data->name,
            'slug' => $slug,
            'gender_type' => $data->genderType,
            'description' => $data->description,
            'address' => $data->address,
            'area' => $data->area,
            'latitude' => $data->latitude,
            'longitude' => $data->longitude,
            'rules' => $data->rules,
        ]);

        $listing->facilities()->sync($data->facilityIds);

        return $listing->load('facilities');
    }
}
