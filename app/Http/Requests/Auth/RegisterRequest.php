<?php

declare(strict_types=1);

namespace App\Http\Requests\Auth;

use App\Domain\User\DTOs\RegisterUserData;
use App\Domain\User\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'confirmed', Password::defaults()],
            'role' => ['required', 'string', Rule::in([UserRole::TENANT->value, UserRole::OWNER->value])],
            'phone' => ['nullable', 'string', 'max:20'],
        ];
    }

    public function toDTO(): RegisterUserData
    {
        /** @var array{name: string, email: string, password: string, role?: string|null, phone?: string|null} $validated */
        $validated = $this->validated();

        return RegisterUserData::fromArray($validated);
    }
}
