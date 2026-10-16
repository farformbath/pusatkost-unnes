<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Domain\User\Enums\OwnerVerificationStatus;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        /** @var User|null $user */
        $user = $request->user();
        $userAttrs = $user ? $user->getAttributes() : [];

        $ownerStatus = $userAttrs['owner_verification_status'] ?? null;
        $isVerifiedOwner = $user ? ($user->isOwner() && $ownerStatus === OwnerVerificationStatus::VERIFIED->value) : false;
        $emailVerified = ! empty($userAttrs['email_verified_at']);

        return [
            ...parent::share($request),
            'auth' => [
                'user' => $user ? [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $userAttrs['phone'] ?? null,
                    'avatar_url' => $userAttrs['avatar_url'] ?? null,
                    'owner_verification_status' => $ownerStatus,
                    'is_verified_owner' => $isVerifiedOwner,
                    'email_verified' => $emailVerified,
                    'roles' => $user->relationLoaded('roles') ? $user->roles->pluck('name')->all() : $user->getRoleNames()->all(),
                    'is_owner' => $user->isOwner(),
                    'is_tenant' => $user->isTenant(),
                    'is_admin' => $user->isAdmin(),
                ] : null,
            ],
            'notifications' => fn () => [
                'unread_count' => $user ? $user->unreadNotifications()->count() : 0,
                'items' => $user ? $user->notifications()->take(5)->get() : [],
            ],
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
            ],
        ];
    }
}
