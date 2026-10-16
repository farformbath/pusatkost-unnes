<?php

declare(strict_types=1);

namespace App\Domain\Wishlist\Repositories;

use App\Domain\Wishlist\Models\Wishlist;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface WishlistRepositoryInterface
{
    /**
     * @return LengthAwarePaginator<int, Wishlist>
     */
    public function getUserWishlists(string|int $userId, int $perPage = 12): LengthAwarePaginator;

    public function isWishlisted(string|int $userId, string $listingId): bool;
}
