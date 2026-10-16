<?php

declare(strict_types=1);

namespace App\Domain\Listing\Actions;

use App\Domain\Listing\Enums\ListingStatus;
use App\Domain\Listing\Models\Listing;
use InvalidArgumentException;

final class PublishListingAction
{
    public function handle(Listing $listing): Listing
    {
        if ($listing->rooms()->count() === 0) {
            throw new InvalidArgumentException('Kos wajib memiliki minimal 1 tipe kamar sebelum dapat dipublikasikan.');
        }

        if (! $listing->primaryImage()->exists()) {
            throw new InvalidArgumentException('Kos wajib memiliki minimal 1 foto utama sebelum dapat dipublikasikan.');
        }

        $listing->update([
            'status' => ListingStatus::PUBLISHED,
        ]);

        return $listing;
    }
}
