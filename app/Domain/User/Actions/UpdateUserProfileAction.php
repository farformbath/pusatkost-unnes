<?php

declare(strict_types=1);

namespace App\Domain\User\Actions;

use App\Domain\User\DTOs\UserProfileData;
use App\Models\User;

class UpdateUserProfileAction
{
    public function execute(User $user, UserProfileData $data): User
    {
        $user->update([
            'name' => $data->name,
            'email' => $data->email,
            'phone' => $data->phone,
            'avatar_url' => $data->avatarUrl,
        ]);

        return $user;
    }
}
