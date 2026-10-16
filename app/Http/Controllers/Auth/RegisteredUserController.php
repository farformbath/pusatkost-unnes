<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Domain\User\Actions\RegisterUserAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    public function create(): Response
    {
        return Inertia::render('Auth/Register');
    }

    public function store(RegisterRequest $request, RegisterUserAction $registerAction): RedirectResponse
    {
        $dto = $request->toDTO();
        $user = $registerAction->execute($dto);

        Auth::login($user);

        if ($user->isOwner()) {
            return redirect('/owner/kos/create')->with('success', 'Selamat datang di Pusat Kost UNNES! Silakan lengkapi data kos Anda.');
        }

        return redirect('/kos')->with('success', 'Akun berhasil didaftarkan! Selamat menjelajah kos impian Anda.');
    }
}
