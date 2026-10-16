<?php

declare(strict_types=1);

namespace App\Domain\Booking\DTOs;

final readonly class BookingData
{
    public function __construct(
        public int $userId,
        public string $listingId,
        public string $roomId,
        public string $tenantName,
        public string $tenantPhone,
        public string $startDate,
        public int $durationMonths,
        public ?string $notes = null,
    ) {}

    /**
     * @param  array{listing_id: string, room_id: string, tenant_name: string, tenant_phone: string, start_date: string, duration_months?: int, notes?: ?string}  $data
     */
    public static function fromArray(array $data, int $userId): self
    {
        return new self(
            userId: $userId,
            listingId: $data['listing_id'],
            roomId: $data['room_id'],
            tenantName: $data['tenant_name'],
            tenantPhone: $data['tenant_phone'],
            startDate: $data['start_date'],
            durationMonths: $data['duration_months'] ?? 1,
            notes: $data['notes'] ?? null,
        );
    }
}
