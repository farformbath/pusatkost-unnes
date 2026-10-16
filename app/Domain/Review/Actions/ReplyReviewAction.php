<?php

declare(strict_types=1);

namespace App\Domain\Review\Actions;

use App\Domain\Review\Models\Review;

class ReplyReviewAction
{
    public function execute(string $reviewId, string $reply): Review
    {
        $review = Review::query()->findOrFail($reviewId);

        $review->update([
            'owner_reply' => trim($reply),
            'owner_replied_at' => now(),
        ]);

        return $review;
    }
}
