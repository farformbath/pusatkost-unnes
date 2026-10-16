<?php

declare(strict_types=1);

namespace App\Domain\Payment\Actions;

use App\Domain\Booking\Models\Booking;
use App\Domain\Payment\DTOs\PaymentData;
use App\Domain\Payment\Enums\PaymentStatus;
use App\Domain\Payment\Models\Payment;
use App\Domain\Payment\Models\PaymentAudit;
use App\Domain\Payment\Services\PaymentGatewayInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CreatePaymentAction
{
    public function __construct(
        private readonly PaymentGatewayInterface $gateway,
    ) {}

    public function execute(Booking $booking, PaymentData $data): Payment
    {
        return DB::transaction(function () use ($booking, $data): Payment {
            // Idempotency: if payment with this idempotency key already exists, return it
            if ($data->idempotencyKey) {
                $existing = Payment::where('idempotency_key', $data->idempotencyKey)->first();
                if ($existing) {
                    return $existing;
                }
            }

            // Reuse latest pending payment if available for this booking, or create new
            /** @var Payment|null $existingPending */
            $existingPending = Payment::where('booking_id', $booking->id)
                ->where('transaction_status', PaymentStatus::PENDING)
                ->latest()
                ->first();

            if ($existingPending) {
                return $existingPending;
            }

            $invoiceNumber = 'INV-'.now()->format('Ymd').'-'.strtoupper(substr((string) Str::uuid(), 0, 6));
            $idempotencyKey = $data->idempotencyKey ?? (string) Str::uuid();

            $payment = Payment::create([
                'booking_id' => $booking->id,
                'invoice_number' => $invoiceNumber,
                'payment_provider' => $data->paymentProvider,
                'payment_channel' => $data->paymentChannel,
                'gross_amount' => $booking->grand_total,
                'transaction_status' => PaymentStatus::PENDING,
                'idempotency_key' => $idempotencyKey,
                'payload' => $data->payload,
            ]);

            // Register transaction with gateway
            $gatewayResponse = $this->gateway->createTransaction($payment);
            $payment->update([
                'payment_reference' => $gatewayResponse['payment_reference'] ?? null,
                'provider_transaction_id' => $gatewayResponse['transaction_id'] ?? null,
                'provider_payload' => $gatewayResponse,
            ]);

            // Audit Trail
            PaymentAudit::create([
                'payment_id' => $payment->id,
                'actor_id' => $booking->user_id,
                'action' => 'payment.created',
                'old_status' => null,
                'new_status' => PaymentStatus::PENDING->value,
                'metadata' => [
                    'invoice_number' => $invoiceNumber,
                    'gross_amount' => $booking->grand_total,
                    'channel' => $data->paymentChannel,
                ],
            ]);

            return $payment;
        });
    }
}
