<?php

declare(strict_types=1);

namespace App\Domain\Review\Policies;

use App\Domain\Review\Models\Review;
use App\Models\User;

class ReviewPolicy
{
    public function reply(User $user, Review $review): bool
    {
        $isOwner = $review->listing !== null && (int) $user->id === (int) $review->listing->owner_id;

        return $isOwner || $user->isAdmin();
    }

    public function delete(User $user, Review $review): bool
    {
        return (int) $user->id === (int) $review->user_id || $user->isAdmin();
    }
}
