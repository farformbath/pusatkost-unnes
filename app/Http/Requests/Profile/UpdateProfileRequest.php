<?php

declare(strict_types=1);

namespace App\Http\Requests\Profile;

use App\Domain\User\DTOs\UserProfileData;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
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
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($this->user()?->id),
            ],
            'phone' => ['nullable', 'string', 'max:20'],
            'avatar_url' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function toDTO(): UserProfileData
    {
        /** @var array{name: string, email: string, phone?: string|null, avatar_url?: string|null} $validated */
        $validated = $this->validated();

        return UserProfileData::fromArray($validated);
    }
}
