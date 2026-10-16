<?php

declare(strict_types=1);

namespace App\Domain\Wishlist\Actions;

use App\Domain\Wishlist\Models\Wishlist;
use Illuminate\Support\Facades\DB;

class ToggleWishlistAction
{
    public function execute(string|int $userId, string $listingId): bool
    {
        return DB::transaction(function () use ($userId, $listingId): bool {
            $existing = Wishlist::query()
                ->where('user_id', $userId)
                ->where('listing_id', $listingId)
                ->first();

            if ($existing) {
                $existing->delete();

                return false;
            }

            Wishlist::query()->create([
                'user_id' => $userId,
                'listing_id' => $listingId,
            ]);

            return true;
        });
    }
}
