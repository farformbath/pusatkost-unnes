<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class VerifyContactRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Authorized via controller policy check
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'tenant_contacted' => ['required', 'boolean'],
            'owner_contacted' => ['required', 'boolean'],
            'tenant_confirmed' => ['required', 'boolean'],
            'owner_confirmed' => ['required', 'boolean'],
            'admin_notes' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
