<?php

declare(strict_types=1);

namespace App\Domain\Listing\Actions;

use App\Domain\Listing\Models\Listing;
use App\Domain\Listing\Models\ListingImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

final class UploadListingImageAction
{
    public function handle(Listing $listing, UploadedFile $file, bool $isPrimary = false, ?string $roomId = null): ListingImage
    {
        $path = Storage::disk('public')->putFile('listings', $file);

        if (! $path) {
            throw new RuntimeException('Gagal menyimpan file gambar ke storage disk.');
        }

        if ($isPrimary) {
            $listing->images()->where('is_primary', true)->update(['is_primary' => false]);
        } elseif ($listing->images()->count() === 0) {
            // First uploaded image is automatically set as primary
            $isPrimary = true;
        }

        $maxOrder = $listing->images()->max('sort_order');
        $highestOrder = is_numeric($maxOrder) ? (int) $maxOrder : 0;

        return ListingImage::create([
            'listing_id' => $listing->id,
            'room_id' => $roomId,
            'file_path' => $path,
            'is_primary' => $isPrimary,
            'sort_order' => $highestOrder + 1,
        ]);
    }
}
