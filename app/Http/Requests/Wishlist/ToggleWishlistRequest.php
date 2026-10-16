<?php

declare(strict_types=1);

namespace App\Http\Requests\Wishlist;

use Illuminate\Foundation\Http\FormRequest;

class ToggleWishlistRequest extends FormRequest
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
            'listing_id' => ['required', 'string', 'uuid', 'exists:listings,id'],
        ];
    }
}
