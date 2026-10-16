<?php

declare(strict_types=1);

namespace App\Domain\Review\DTOs;

final readonly class ReviewData
{
    public function __construct(
        public string|int $userId,
        public string $listingId,
        public int $rating,
        public string $comment,
        public ?string $bookingId = null,
    ) {}

    /**
     * @param  array{user_id: string|int, listing_id: string, rating: int|string, comment: string, booking_id?: string|null}  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            userId: $data['user_id'],
            listingId: (string) $data['listing_id'],
            rating: (int) $data['rating'],
            comment: trim($data['comment']),
            bookingId: ! empty($data['booking_id']) ? (string) $data['booking_id'] : null,
        );
    }
}
