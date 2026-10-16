<?php

declare(strict_types=1);

namespace App\Domain\Review\Repositories;

use App\Domain\Review\Models\Review;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EloquentReviewRepository implements ReviewRepositoryInterface
{
    /**
     * @return LengthAwarePaginator<int, Review>
     */
    public function getListingReviews(string $listingId, int $perPage = 10): LengthAwarePaginator
    {
        return Review::query()
            ->where('listing_id', $listingId)
            ->with(['user', 'booking'])
            ->latest()
            ->paginate($perPage);
    }
}
