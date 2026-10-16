<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Domain\User\Actions\VerifyOwnerAction;
use App\Domain\User\Enums\OwnerVerificationStatus;
use App\Domain\User\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AdminOwnerController extends Controller
{
    public function index(Request $request): Response
    {
        /** @var User $currentUser */
        $currentUser = $request->user();

        if (! $currentUser->isAdmin()) {
            abort(403, 'Akses khusus administrator UNNESKost.');
        }

        $status = $request->query('status');

        $query = User::role(UserRole::OWNER->value)
            ->withCount('listings')
            ->latest();

        if ($status && in_array($status, ['pending', 'verified', 'rejected'], true)) {
            $query->where('owner_verification_status', $status);
        }

        $owners = $query->paginate(10)->withQueryString();

        $counts = [
            'all' => User::role(UserRole::OWNER->value)->count(),
            'pending' => User::role(UserRole::OWNER->value)->where('owner_verification_status', OwnerVerificationStatus::PENDING)->count(),
            'verified' => User::role(UserRole::OWNER->value)->where('owner_verification_status', OwnerVerificationStatus::VERIFIED)->count(),
            'rejected' => User::role(UserRole::OWNER->value)->where('owner_verification_status', OwnerVerificationStatus::REJECTED)->count(),
        ];

        return Inertia::render('Admin/Owners/Index', [
            'owners' => $owners,
            'counts' => $counts,
            'current_status' => $status ?? 'all',
        ]);
    }

    public function verify(User $user, VerifyOwnerAction $action, Request $request): RedirectResponse
    {
        /** @var User $currentUser */
        $currentUser = $request->user();

        if (! $currentUser->isAdmin()) {
            abort(403, 'Akses khusus administrator UNNESKost.');
        }

        $action->verify($user);

        return back()->with('success', "Status pemilik {$user->name} berhasil diverifikasi.");
    }

    public function reject(User $user, VerifyOwnerAction $action, Request $request): RedirectResponse
    {
        /** @var User $currentUser */
        $currentUser = $request->user();

        if (! $currentUser->isAdmin()) {
            abort(403, 'Akses khusus administrator UNNESKost.');
        }

        $action->reject($user);

        return back()->with('success', "Status verifikasi pemilik {$user->name} ditolak.");
    }
}
