<?php

declare(strict_types=1);

namespace App\Domain\User\Actions;

use App\Domain\User\Enums\OwnerVerificationStatus;
use App\Models\User;
use InvalidArgumentException;

class VerifyOwnerAction
{
    public function verify(User $user): User
    {
        if (! $user->isOwner()) {
            throw new InvalidArgumentException('Pengguna bukan merupakan pemilik kos.');
        }

        $user->owner_verification_status = OwnerVerificationStatus::VERIFIED;
        $user->save();

        return $user;
    }

    public function reject(User $user): User
    {
        if (! $user->isOwner()) {
            throw new InvalidArgumentException('Pengguna bukan merupakan pemilik kos.');
        }

        $user->owner_verification_status = OwnerVerificationStatus::REJECTED;
        $user->save();

        return $user;
    }
}
