<?php

declare(strict_types=1);

namespace App\Domain\Payment\Policies;

use App\Domain\Payment\Models\Payout;
use App\Domain\User\Enums\UserRole;
use App\Models\User;

class PayoutPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole(UserRole::ADMIN->value)
            || $user->hasRole(UserRole::OWNER->value)
            || $user->can('manage-payouts')
            || $user->can('view-payouts');
    }

    public function view(User $user, Payout $payout): bool
    {
        if ($user->hasRole(UserRole::ADMIN->value) || $user->can('manage-payouts')) {
            return true;
        }

        return (int) $payout->owner_id === (int) $user->id;
    }

    public function viewReceipt(User $user, Payout $payout): bool
    {
        return $this->view($user, $payout);
    }
}
