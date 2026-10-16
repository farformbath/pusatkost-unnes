<?php

declare(strict_types=1);

namespace App\Domain\Wishlist\Repositories;

use App\Domain\Listing\Enums\RoomStatus;
use App\Domain\Wishlist\Models\Wishlist;
use Illuminate\Contracts\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EloquentWishlistRepository implements WishlistRepositoryInterface
{
    /**
     * @return LengthAwarePaginator<int, Wishlist>
     */
    public function getUserWishlists(string|int $userId, int $perPage = 12): LengthAwarePaginator
    {
        return Wishlist::query()
            ->where('user_id', $userId)
            ->with([
                'listing.primaryImage',
                'listing.images',
                'listing.facilities',
                'listing.rooms' => function (EloquentBuilder $q): void {
                    $q->where('status', RoomStatus::AVAILABLE);
                },
            ])
            ->latest()
            ->paginate($perPage);
    }

    public function isWishlisted(string|int $userId, string $listingId): bool
    {
        return Wishlist::query()
            ->where('user_id', $userId)
            ->where('listing_id', $listingId)
            ->exists();
    }
}
