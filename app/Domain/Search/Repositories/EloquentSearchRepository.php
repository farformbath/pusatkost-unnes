<?php

declare(strict_types=1);

namespace App\Domain\Search\Repositories;

use App\Domain\Listing\Enums\RoomStatus;
use App\Domain\Listing\Models\Listing;
use App\Domain\Listing\Models\Room;
use App\Domain\Search\DTOs\SearchCriteria;
use Illuminate\Contracts\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class EloquentSearchRepository implements SearchRepositoryInterface
{
    /**
     * @return LengthAwarePaginator<int, Listing>
     */
    public function search(SearchCriteria $criteria): LengthAwarePaginator
    {
        $query = Listing::query()
            ->published()
            ->with([
                'primaryImage',
                'images',
                'facilities',
                'rooms' => function (EloquentBuilder $q): void {
                    $q->where('status', RoomStatus::AVAILABLE)
                        ->where('available_rooms', '>', 0)
                        ->with('facilities');
                },
            ]);

        // 1. Keyword search (Name, Address, Description, Area)
        if ($criteria->keyword !== null && $criteria->keyword !== '') {
            $keyword = '%'.$criteria->keyword.'%';
            $likeOperator = DB::getDriverName() === 'pgsql' ? 'ILIKE' : 'like';

            $query->where(function (EloquentBuilder $q) use ($keyword, $likeOperator): void {
                $q->where('name', $likeOperator, $keyword)
                    ->orWhere('address', $likeOperator, $keyword)
                    ->orWhere('description', $likeOperator, $keyword)
                    ->orWhere('area', $likeOperator, $keyword);
            });
        }

        // 2. Gender filter
        if ($criteria->gender !== null) {
            $query->byGender($criteria->gender);
        }

        // 3. Area filter
        if (! empty($criteria->areas)) {
            $query->byArea($criteria->areas);
        } elseif ($criteria->area !== null && $criteria->area !== '') {
            $query->byArea($criteria->area);
        }

        // 4. Price range filter (based on available rooms)
        if ($criteria->minPrice !== null || $criteria->maxPrice !== null) {
            $query->whereHas('rooms', function (EloquentBuilder $q) use ($criteria): void {
                $q->where('status', RoomStatus::AVAILABLE)
                    ->where('available_rooms', '>', 0);

                if ($criteria->minPrice !== null) {
                    $q->where('price_monthly', '>=', $criteria->minPrice);
                }

                if ($criteria->maxPrice !== null) {
                    $q->where('price_monthly', '<=', $criteria->maxPrice);
                }
            });
        }

        // 5. Facilities filter (Listing must have all specified facilities)
        if (! empty($criteria->facilityIds)) {
            foreach ($criteria->facilityIds as $facilityId) {
                $query->where(function (EloquentBuilder $q) use ($facilityId): void {
                    $q->whereHas('facilities', function (EloquentBuilder $subQ) use ($facilityId): void {
                        $subQ->where('facilities.id', $facilityId);
                    })->orWhereHas('rooms', function (EloquentBuilder $subQ) use ($facilityId): void {
                        $subQ->whereHas('facilities', function (EloquentBuilder $roomFacQ) use ($facilityId): void {
                            $roomFacQ->where('facilities.id', $facilityId);
                        });
                    });
                });
            }
        }

        // 6. Whitelisted Sorting
        match ($criteria->sort) {
            'price_low', 'price_asc' => $query->orderBy(
                Room::select('price_monthly')
                    ->whereColumn('rooms.listing_id', 'listings.id')
                    ->where('status', RoomStatus::AVAILABLE)
                    ->where('available_rooms', '>', 0)
                    ->orderBy('price_monthly', 'asc')
                    ->limit(1),
                'asc'
            ),
            'price_high', 'price_desc' => $query->orderBy(
                Room::select('price_monthly')
                    ->whereColumn('rooms.listing_id', 'listings.id')
                    ->where('status', RoomStatus::AVAILABLE)
                    ->where('available_rooms', '>', 0)
                    ->orderBy('price_monthly', 'desc')
                    ->limit(1),
                'desc'
            ),
            'rating_desc' => $query->orderBy('rating_avg', 'desc')->orderBy('review_count', 'desc'),
            'newest' => $query->latest(),
            default => $query->orderBy('rating_avg', 'desc')->latest(),
        };

        return $query->paginate(
            perPage: $criteria->perPage,
            page: $criteria->page
        )->withQueryString();
    }
}
