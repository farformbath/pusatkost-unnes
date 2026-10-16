<?php

declare(strict_types=1);

namespace App\Domain\Listing\Repositories;

use App\Domain\Listing\Enums\GenderType;
use App\Domain\Listing\Models\Listing;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ListingRepositoryInterface
{
    /**
     * @return LengthAwarePaginator<int, Listing>
     */
    public function getPublishedListings(?GenderType $gender = null, ?string $area = null, int $perPage = 12): LengthAwarePaginator;

    public function findBySlug(string $slug): ?Listing;

    /**
     * @return LengthAwarePaginator<int, Listing>
     */
    public function getOwnerListings(int $ownerId, int $perPage = 10): LengthAwarePaginator;

    public function findForOwner(string $id, int $ownerId): ?Listing;
}
