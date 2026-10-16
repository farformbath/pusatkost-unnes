<?php

declare(strict_types=1);

namespace App\Domain\Listing\Policies;

use App\Domain\Listing\Models\Listing;
use App\Domain\Listing\Models\Room;
use App\Models\User;

class RoomPolicy
{
    public function create(User $user, Listing $listing): bool
    {
        return $user->can('manage-rooms') && ((int) $user->id === (int) $listing->owner_id || $user->isAdmin());
    }

    public function update(User $user, Room $room): bool
    {
        $isOwner = $room->listing !== null && (int) $user->id === (int) $room->listing->owner_id;

        return $user->can('manage-rooms') && ($isOwner || $user->isAdmin());
    }

    public function toggle(User $user, Room $room): bool
    {
        $isOwner = $room->listing !== null && (int) $user->id === (int) $room->listing->owner_id;

        return $user->can('manage-rooms') && ($isOwner || $user->isAdmin());
    }
}
