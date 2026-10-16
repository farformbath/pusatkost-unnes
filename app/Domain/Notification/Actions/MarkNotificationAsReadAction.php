<?php

declare(strict_types=1);

namespace App\Domain\Notification\Actions;

use App\Models\User;

class MarkNotificationAsReadAction
{
    public function execute(User $user, ?string $notificationId = null): void
    {
        if ($notificationId !== null) {
            $notification = $user->unreadNotifications()->where('id', $notificationId)->firstOrFail();
            $notification->markAsRead();

            return;
        }

        $user->unreadNotifications()->update(['read_at' => now()]);
    }
}
