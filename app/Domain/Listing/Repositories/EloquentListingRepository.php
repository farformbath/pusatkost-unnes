<?php

declare(strict_types=1);

namespace App\Domain\Listing\Repositories;

use App\Domain\Listing\Enums\GenderType;
use App\Domain\Listing\Enums\RoomStatus;
use App\Domain\Listing\Models\Listing;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Relations\Relation;

final class EloquentListingRepository implements ListingRepositoryInterface
{
    /**
     * @return LengthAwarePaginator<int, Listing>
     */
    public function getPublishedListings(?GenderType $gender = null, ?string $area = null, int $perPage = 12): LengthAwarePaginator
    {
        $query = Listing::published()
            ->with([
                'primaryImage',
                'rooms' => function (Relation $q): void {
                    $q->where('status', RoomStatus::AVAILABLE)
                        ->where('available_rooms', '>', 0);
                },
                'facilities',
            ])
            ->latest();

        if ($gender !== null) {
            $query->byGender($gender);
        }

        if ($area !== null && trim($area) !== '') {
            $query->byArea($area);
        }

        return $query->paginate($perPage);
    }

    public function findBySlug(string $slug): ?Listing
    {
        return Listing::published()
            ->with([
                'images',
                'rooms.facilities',
                'rooms.images',
                'facilities',
                'owner',
                'reviews.user',
            ])
            ->where('slug', $slug)
            ->first();
    }

    /**
     * @return LengthAwarePaginator<int, Listing>
     */
    public function getOwnerListings(int $ownerId, int $perPage = 10): LengthAwarePaginator
    {
        return Listing::where('owner_id', $ownerId)
            ->with([
                'primaryImage',
                'rooms',
                'facilities',
            ])
            ->latest()
            ->paginate($perPage);
    }

    public function findForOwner(string $id, int $ownerId): ?Listing
    {
        return Listing::where('owner_id', $ownerId)
            ->with([
                'images',
                'rooms.facilities',
                'facilities',
            ])
            ->where('id', $id)
            ->first();
    }
}
