<?php

declare(strict_types=1);

namespace App\Domain\Payment\Services;

use App\Domain\Payment\Exceptions\PaymentVerificationException;
use App\Domain\Payment\Models\Payment;
use App\Domain\Payment\Models\Payout;
use Illuminate\Support\Str;

class SandboxPaymentGateway implements PaymentGatewayInterface
{
    /**
     * @return array<string, mixed>
     */
    public function createTransaction(Payment $payment): array
    {
        $reference = 'SB-VA-'.now()->format('Ymd').'-'.strtoupper(Str::random(6));

        return [
            'provider' => 'sandbox',
            'transaction_id' => 'SB-TX-'.(string) Str::uuid(),
            'payment_reference' => $reference,
            'gross_amount' => $payment->gross_amount,
            'status' => 'pending',
            'va_number' => '8800'.substr((string) preg_replace('/\D/', '', (string) $payment->gross_amount).rand(1000, 9999), 0, 10),
            'qr_code_url' => null,
        ];
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array{verified: bool, status: string, transaction_id: string, gross_amount: int, channel: string, raw: array<string, mixed>}
     */
    public function verifyWebhook(array $payload, ?string $signature = null): array
    {
        // Sandbox mock validation
        if (isset($payload['invalid_signature']) && $payload['invalid_signature'] === true) {
            throw PaymentVerificationException::invalidSignature();
        }

        $txRaw = $payload['transaction_id'] ?? null;
        $transactionId = is_scalar($txRaw) ? (string) $txRaw : 'SB-TX-'.(string) Str::uuid();

        $statusRaw = $payload['transaction_status'] ?? null;
        $status = is_scalar($statusRaw) ? (string) $statusRaw : 'settlement';

        $grossRaw = $payload['gross_amount'] ?? null;
        $grossAmount = is_numeric($grossRaw) ? (int) $grossRaw : 0;

        $channelRaw = $payload['payment_channel'] ?? $payload['payment_type'] ?? null;
        $channel = is_scalar($channelRaw) ? (string) $channelRaw : 'bca_va';

        $normalizedStatus = match ($status) {
            'settlement', 'capture', 'paid', 'success' => 'paid',
            'deny', 'cancel', 'expire' => 'failed',
            'pending' => 'pending',
            default => 'processing',
        };

        return [
            'verified' => true,
            'status' => $normalizedStatus,
            'transaction_id' => $transactionId,
            'gross_amount' => $grossAmount,
            'channel' => $channel,
            'raw' => $payload,
        ];
    }

    /**
     * @return array{success: bool, provider_payout_id: string, status: string, raw: array<string, mixed>, error: ?string}
     */
    public function processPayout(Payout $payout): array
    {
        // Sandbox payout simulation
        $providerPayoutId = 'SB-PAY-'.(string) Str::uuid();

        return [
            'success' => true,
            'provider_payout_id' => $providerPayoutId,
            'status' => 'paid',
            'raw' => [
                'payout_id' => $providerPayoutId,
                'owner_id' => $payout->owner_id,
                'net_amount' => $payout->net_amount,
                'disbursed_at' => now()->toIso8601String(),
                'provider' => 'sandbox',
            ],
            'error' => null,
        ];
    }
}
