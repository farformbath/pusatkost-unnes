<?php

declare(strict_types=1);

namespace App\Domain\Booking\DTOs;

final readonly class PaymentData
{
    /**
     * @param  array<string, mixed>|null  $payload
     */
    public function __construct(
        public string $bookingId,
        public string $paymentChannel,
        public int $grossAmount,
        public string $transactionStatus = 'pending',
        public ?string $paymentReference = null,
        public ?array $payload = null,
    ) {}
}
