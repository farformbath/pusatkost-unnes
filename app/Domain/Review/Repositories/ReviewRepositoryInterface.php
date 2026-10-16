<?php

declare(strict_types=1);

namespace App\Domain\Review\Repositories;

use App\Domain\Review\Models\Review;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ReviewRepositoryInterface
{
    /**
     * @return LengthAwarePaginator<int, Review>
     */
    public function getListingReviews(string $listingId, int $perPage = 10): LengthAwarePaginator;
}
