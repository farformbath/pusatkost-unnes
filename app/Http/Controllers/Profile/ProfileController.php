<?php

declare(strict_types=1);

namespace App\Http\Controllers\Profile;

use App\Domain\User\Actions\UpdateAvatarAction;
use App\Domain\User\Actions\UpdateUserProfileAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Profile\UpdateAvatarRequest;
use App\Http\Requests\Profile\UpdatePasswordRequest;
use App\Http\Requests\Profile\UpdateProfileRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    public function edit(Request $request): Response
    {
        /** @var User|null $user */
        $user = $request->user();

        if (! $user) {
            $user = User::query()->first() ?? User::query()->create([
                'name' => 'Pengguna Demo',
                'email' => 'demo@example.com',
                'password' => Hash::make('password'),
            ]);
        }

        return Inertia::render('Profile/Edit', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'avatar_url' => $user->avatar_url,
                'role' => $user->roles->pluck('name')->first() ?? 'tenant',
                'owner_verification_status' => $user->owner_verification_status?->value,
            ],
            'status' => session('status'),
        ]);
    }

    public function update(
        UpdateProfileRequest $request,
        UpdateUserProfileAction $updateAction
    ): RedirectResponse {
        /** @var User $user */
        $user = $request->user();

        $dto = $request->toDTO();
        $updateAction->execute($user, $dto);

        return back()->with('success', 'Profil berhasil diperbarui!');
    }

    public function updateAvatar(
        UpdateAvatarRequest $request,
        UpdateAvatarAction $avatarAction
    ): RedirectResponse {
        /** @var User $user */
        $user = $request->user();

        /** @var UploadedFile $file */
        $file = $request->file('avatar');
        $avatarAction->execute($user, $file);

        return back()->with('success', 'Foto profil berhasil diperbarui!');
    }

    public function updatePassword(UpdatePasswordRequest $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        /** @var string $password */
        $password = $request->validated('password');

        $user->update([
            'password' => Hash::make($password),
        ]);

        return back()->with('success', 'Kata sandi berhasil diganti!');
    }
}
