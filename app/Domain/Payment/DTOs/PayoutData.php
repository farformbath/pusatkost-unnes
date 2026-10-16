<?php

declare(strict_types=1);

namespace App\Domain\Payment\DTOs;

readonly class PayoutData
{
    public function __construct(
        public string $paymentId,
        public ?int $platformFee = 0,
        public ?string $payoutProvider = 'sandbox',
        public ?string $idempotencyKey = null,
    ) {}
}
