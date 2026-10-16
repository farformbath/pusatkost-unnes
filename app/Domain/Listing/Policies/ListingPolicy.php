<?php

declare(strict_types=1);

namespace App\Domain\Listing\Policies;

use App\Domain\Listing\Enums\ListingStatus;
use App\Domain\Listing\Models\Listing;
use App\Models\User;

class ListingPolicy
{
    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, Listing $listing): bool
    {
        if ($listing->status === ListingStatus::PUBLISHED) {
            return true;
        }

        if (! $user) {
            return false;
        }

        return (int) $user->id === (int) $listing->owner_id || $user->isAdmin();
    }

    public function create(User $user): bool
    {
        return $user->can('manage-listings');
    }

    public function update(User $user, Listing $listing): bool
    {
        return $user->can('manage-listings') && ((int) $user->id === (int) $listing->owner_id || $user->isAdmin());
    }

    public function delete(User $user, Listing $listing): bool
    {
        return $user->can('manage-listings') && ((int) $user->id === (int) $listing->owner_id || $user->isAdmin());
    }

    public function publish(User $user, Listing $listing): bool
    {
        $isOwnerOrAdmin = ((int) $user->id === (int) $listing->owner_id || $user->isAdmin());
        $isVerifiedOrAdmin = ($user->isVerifiedOwner() || $user->isAdmin());

        return $user->can('manage-listings') && $isOwnerOrAdmin && $isVerifiedOrAdmin;
    }
}
