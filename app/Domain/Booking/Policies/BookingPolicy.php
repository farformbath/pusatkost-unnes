<?php

declare(strict_types=1);

namespace App\Domain\Booking\Policies;

use App\Domain\Booking\Models\Booking;
use App\Models\User;

class BookingPolicy
{
    public function view(User $user, Booking $booking): bool
    {
        $isTenant = (int) $user->id === (int) $booking->user_id;
        $isOwner = $booking->listing !== null && (int) $user->id === (int) $booking->listing->owner_id;

        return $isTenant || $isOwner || $user->isAdmin();
    }

    public function cancel(User $user, Booking $booking): bool
    {
        return (int) $user->id === (int) $booking->user_id || $user->isAdmin();
    }

    public function approve(User $user, Booking $booking): bool
    {
        $isOwner = $booking->listing !== null && (int) $user->id === (int) $booking->listing->owner_id;

        return ($isOwner && $user->can('approve-bookings')) || $user->isAdmin();
    }

    public function reject(User $user, Booking $booking): bool
    {
        $isOwner = $booking->listing !== null && (int) $user->id === (int) $booking->listing->owner_id;

        return ($isOwner && $user->can('approve-bookings')) || $user->isAdmin();
    }
}
