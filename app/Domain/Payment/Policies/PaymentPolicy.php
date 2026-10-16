<?php

declare(strict_types=1);

namespace App\Domain\Payment\Policies;

use App\Domain\Payment\Models\Payment;
use App\Domain\User\Enums\UserRole;
use App\Models\User;

class PaymentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole(UserRole::ADMIN->value) || $user->can('manage-payments');
    }

    public function view(User $user, Payment $payment): bool
    {
        return $user->hasRole(UserRole::ADMIN->value) || $user->can('manage-payments');
    }

    public function viewReceipt(User $user, Payment $payment): bool
    {
        if ($user->hasRole(UserRole::ADMIN->value) || $user->can('manage-payments')) {
            return true;
        }

        $booking = $payment->booking;
        if (! $booking) {
            return false;
        }

        // Tenant who booked
        if ((int) $booking->user_id === (int) $user->id) {
            return true;
        }

        // Owner of the listing
        if ($booking->listing && (int) $booking->listing->owner_id === (int) $user->id) {
            return true;
        }

        return false;
    }
}
