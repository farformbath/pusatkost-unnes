<?php

declare(strict_types=1);

namespace App\Domain\User\Actions;

use App\Domain\User\DTOs\RegisterUserData;
use App\Domain\User\Enums\OwnerVerificationStatus;
use App\Domain\User\Enums\UserRole;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class RegisterUserAction
{
    public function execute(RegisterUserData $data): User
    {
        return DB::transaction(function () use ($data): User {
            $user = User::query()->create([
                'name' => $data->name,
                'email' => $data->email,
                'password' => Hash::make($data->password),
                'phone' => $data->phone,
            ]);

            if ($data->role === UserRole::OWNER) {
                $user->owner_verification_status = OwnerVerificationStatus::PENDING;
                $user->save();
            }

            // Assign Spatie Role server-side
            $user->assignRole($data->role->value);

            event(new Registered($user));

            return $user;
        });
    }
}
