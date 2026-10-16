<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Domain\User\Actions\VerifyOwnerAction;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class OwnerVerificationController extends Controller
{
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

        return back()->with('success', "Status pemilik {$user->name} ditolak.");
    }
}
