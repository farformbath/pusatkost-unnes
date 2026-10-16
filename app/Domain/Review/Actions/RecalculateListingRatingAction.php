<?php

declare(strict_types=1);

namespace App\Domain\Review\Actions;

use App\Domain\Listing\Models\Listing;
use App\Domain\Review\Models\Review;

class RecalculateListingRatingAction
{
    public function execute(string $listingId): void
    {
        $listing = Listing::query()->find($listingId);

        if (! $listing) {
            return;
        }

        $reviewCount = Review::query()->where('listing_id', $listingId)->count();
        $avgRating = $reviewCount > 0
            ? (float) Review::query()->where('listing_id', $listingId)->avg('rating')
            : 0.0;

        $listing->update([
            'rating_avg' => round($avgRating, 2),
            'review_count' => $reviewCount,
        ]);
    }
}
