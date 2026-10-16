<?php

declare(strict_types=1);

namespace App\Domain\User\Actions;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UpdateAvatarAction
{
    public function execute(User $user, UploadedFile $file): User
    {
        $currentAvatar = $user->getAttributes()['avatar_url'] ?? null;

        // 1. Delete old avatar if it exists in local storage
        if (is_string($currentAvatar) && Str::startsWith($currentAvatar, '/storage/avatars/')) {
            $oldPath = Str::replaceFirst('/storage/', '', $currentAvatar);
            if (Storage::disk('public')->exists($oldPath)) {
                Storage::disk('public')->delete($oldPath);
            }
        }

        // 2. Securely store new avatar
        $path = $file->store('avatars', 'public');
        $avatarUrl = '/storage/'.$path;

        // 3. Update user record
        $user->update([
            'avatar_url' => $avatarUrl,
        ]);

        return $user;
    }
}
