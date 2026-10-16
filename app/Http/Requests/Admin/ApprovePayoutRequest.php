<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ApprovePayoutRequest extends FormRequest
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
            'platform_fee' => ['nullable', 'integer', 'min:0'],
            'payout_provider' => ['nullable', 'string', 'max:50'],
        ];
    }
}
