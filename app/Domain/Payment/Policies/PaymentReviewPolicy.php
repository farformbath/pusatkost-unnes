<?php

declare(strict_types=1);

namespace App\Domain\Payment\Policies;

use App\Domain\Payment\Models\PaymentReview;
use App\Domain\User\Enums\UserRole;
use App\Models\User;

class PaymentReviewPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole(UserRole::ADMIN->value) || $user->can('manage-payments');
    }

    public function view(User $user, PaymentReview $review): bool
    {
        return $user->hasRole(UserRole::ADMIN->value) || $user->can('manage-payments');
    }

    public function updateContact(User $user, PaymentReview $review): bool
    {
        return $user->hasRole(UserRole::ADMIN->value) || $user->can('manage-payments');
    }

    public function approve(User $user, PaymentReview $review): bool
    {
        if (! ($user->hasRole(UserRole::ADMIN->value) || $user->can('approve-payouts'))) {
            return false;
        }

        return $review->isEligibleForApproval();
    }

    public function reject(User $user, PaymentReview $review): bool
    {
        return $user->hasRole(UserRole::ADMIN->value) || $user->can('manage-payments');
    }
}
