<?php

declare(strict_types=1);

namespace App\Domain\Review\Actions;

use App\Domain\Review\DTOs\ReviewData;
use App\Domain\Review\Models\Review;
use Illuminate\Support\Facades\DB;

class CreateReviewAction
{
    public function __construct(
        private readonly RecalculateListingRatingAction $recalculateRating,
    ) {}

    public function execute(ReviewData $data): Review
    {
        return DB::transaction(function () use ($data): Review {
            $review = Review::query()->updateOrCreate(
                [
                    'user_id' => $data->userId,
                    'listing_id' => $data->listingId,
                ],
                [
                    'booking_id' => $data->bookingId,
                    'rating' => $data->rating,
                    'comment' => $data->comment,
                ]
            );

            $this->recalculateRating->execute($data->listingId);

            return $review;
        });
    }
}
