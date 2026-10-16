<?php

declare(strict_types=1);

namespace App\Domain\Payment\DTOs;

readonly class PaymentData
{
    /**
     * @param  array<string, mixed>|null  $payload
     */
    public function __construct(
        public string $bookingId,
        public string $paymentChannel = 'bca_va',
        public string $paymentProvider = 'sandbox',
        public ?string $idempotencyKey = null,
        public ?array $payload = null,
    ) {}
}
