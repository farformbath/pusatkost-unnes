<?php

declare(strict_types=1);

namespace App\Http\Requests\Review;

use App\Domain\Review\DTOs\ReviewData;
use Illuminate\Foundation\Http\FormRequest;

class StoreReviewRequest extends FormRequest
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
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['required', 'string', 'min:5', 'max:1000'],
            'booking_id' => ['nullable', 'string', 'uuid'],
        ];
    }

    public function toDTO(string|int $userId, string $listingId): ReviewData
    {
        /** @var array{rating: int, comment: string, booking_id?: ?string} $validated */
        $validated = $this->validated();

        return new ReviewData(
            userId: $userId,
            listingId: $listingId,
            rating: (int) $validated['rating'],
            comment: $validated['comment'],
            bookingId: $validated['booking_id'] ?? null,
        );
    }
}
