<?php

declare(strict_types=1);

namespace App\Http\Controllers\Notification;

use App\Domain\Notification\Actions\MarkNotificationAsReadAction;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        /** @var User|null $user */
        $user = $request->user();

        if (! $user) {
            return response()->json(['notifications' => []]);
        }

        $notifications = $user->notifications()->take(10)->get();

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $user->unreadNotifications()->count(),
        ]);
    }

    public function markAsRead(
        string $id,
        Request $request,
        MarkNotificationAsReadAction $markAction
    ): JsonResponse|RedirectResponse {
        /** @var User $user */
        $user = $request->user();

        $markAction->execute($user, $id);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'unread_count' => $user->unreadNotifications()->count(),
            ]);
        }

        return back();
    }

    public function markAllAsRead(
        Request $request,
        MarkNotificationAsReadAction $markAction
    ): JsonResponse|RedirectResponse {
        /** @var User $user */
        $user = $request->user();

        $markAction->execute($user);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'unread_count' => 0,
            ]);
        }

        return back();
    }
}
