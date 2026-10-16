<?php

declare(strict_types=1);

namespace App\Domain\Payment\Services;

use App\Domain\Payment\Models\Payment;
use App\Domain\Payment\Models\Payout;

interface PaymentGatewayInterface
{
    /**
     * Create or register transaction on gateway.
     *
     * @return array<string, mixed>
     */
    public function createTransaction(Payment $payment): array;

    /**
     * Verify incoming webhook signature & payload.
     *
     * @param  array<string, mixed>  $payload
     * @return array{verified: bool, status: string, transaction_id: string, gross_amount: int, channel: string, raw: array<string, mixed>}
     */
    public function verifyWebhook(array $payload, ?string $signature = null): array;

    /**
     * Disburse funds / execute payout to owner.
     *
     * @return array{success: bool, provider_payout_id: string, status: string, raw: array<string, mixed>, error: ?string}
     */
    public function processPayout(Payout $payout): array;
}
