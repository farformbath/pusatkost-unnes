<?php

declare(strict_types=1);

namespace App\Domain\Payment\Actions;

use App\Domain\Booking\Enums\BookingStatus;
use App\Domain\Booking\Enums\PaymentStatus as BookingPaymentStatus;
use App\Domain\Notification\Notifications\AdminReviewStartedNotification;
use App\Domain\Notification\Notifications\PaymentVerifiedNotification;
use App\Domain\Payment\Enums\PaymentReviewStatus;
use App\Domain\Payment\Enums\PaymentStatus;
use App\Domain\Payment\Exceptions\PaymentVerificationException;
use App\Domain\Payment\Models\Payment;
use App\Domain\Payment\Models\PaymentAudit;
use App\Domain\Payment\Models\PaymentReview;
use App\Domain\Payment\Services\PaymentGatewayInterface;
use App\Domain\User\Enums\UserRole;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class VerifyPaymentAction
{
    public function __construct(
        private readonly PaymentGatewayInterface $gateway,
    ) {}

    /**
     * @param  array<string, mixed>  $webhookPayload
     */
    public function execute(array $webhookPayload, ?string $signature = null): Payment
    {
        // 1. Verify webhook with gateway
        $verifiedData = $this->gateway->verifyWebhook($webhookPayload, $signature);

        $refRaw = $webhookPayload['payment_reference'] ?? $webhookPayload['order_id'] ?? $webhookPayload['invoice_number'] ?? '';
        $reference = is_scalar($refRaw) ? (string) $refRaw : '';
        $transactionId = $verifiedData['transaction_id'];

        /** @var Payment|null $payment */
        $payment = Payment::where('invoice_number', $reference)
            ->orWhere('payment_reference', $reference)
            ->orWhere('provider_transaction_id', $transactionId)
            ->first();

        if (! $payment) {
            throw PaymentVerificationException::paymentNotFound($reference ?: $transactionId);
        }

        // 2. Strict Idempotency: if already paid, return early safely
        if ($payment->transaction_status === PaymentStatus::PAID) {
            return $payment;
        }

        return DB::transaction(function () use ($payment, $verifiedData, $webhookPayload): Payment {
            /** @var Payment $lockedPayment */
            $lockedPayment = Payment::where('id', $payment->id)->lockForUpdate()->firstOrFail();

            if ($lockedPayment->transaction_status === PaymentStatus::PAID) {
                return $lockedPayment;
            }

            $oldStatus = $lockedPayment->transaction_status->value;
            $newStatus = $verifiedData['status'] === 'paid' ? PaymentStatus::PAID : PaymentStatus::FAILED;

            $lockedPayment->update([
                'transaction_status' => $newStatus,
                'provider_transaction_id' => $verifiedData['transaction_id'],
                'provider_payload' => $webhookPayload,
                'paid_at' => $newStatus === PaymentStatus::PAID ? now() : null,
                'verified_at' => now(),
            ]);

            // Synchronize Booking payment_status & status
            $booking = $lockedPayment->booking;
            if ($booking) {
                $newBookingStatus = $booking->status === BookingStatus::PENDING && $newStatus === PaymentStatus::PAID
                    ? BookingStatus::APPROVED
                    : $booking->status;

                $booking->update([
                    'payment_status' => $newStatus === PaymentStatus::PAID ? BookingPaymentStatus::PAID : BookingPaymentStatus::FAILED,
                    'status' => $newBookingStatus,
                ]);
            }

            // Create PaymentReview record if verified as PAID
            if ($newStatus === PaymentStatus::PAID && $booking) {
                PaymentReview::firstOrCreate(
                    [
                        'payment_id' => $lockedPayment->id,
                        'booking_id' => $booking->id,
                    ],
                    [
                        'status' => PaymentReviewStatus::PENDING,
                        'tenant_contacted' => false,
                        'owner_contacted' => false,
                        'tenant_confirmed' => false,
                        'owner_confirmed' => false,
                    ]
                );
            }

            // Audit Trail
            PaymentAudit::create([
                'payment_id' => $lockedPayment->id,
                'actor_id' => null, // webhook/system
                'action' => 'payment.verified',
                'old_status' => $oldStatus,
                'new_status' => $newStatus->value,
                'metadata' => [
                    'provider_transaction_id' => $verifiedData['transaction_id'],
                    'channel' => $verifiedData['channel'],
                    'gross_amount' => $verifiedData['gross_amount'],
                ],
            ]);

            // Dispatch Notifications strictly after commit
            DB::afterCommit(function () use ($lockedPayment, $newStatus): void {
                if ($newStatus !== PaymentStatus::PAID) {
                    return;
                }

                $lockedPayment->load(['booking.user', 'booking.listing.owner']);
                $booking = $lockedPayment->booking;

                if ($booking && $booking->user) {
                    $booking->user->notify(new PaymentVerifiedNotification($booking));
                }

                // Notify all admins that review has started
                $admins = User::role(UserRole::ADMIN->value)->get();
                if ($booking) {
                    foreach ($admins as $admin) {
                        $admin->notify(new AdminReviewStartedNotification($booking));
                    }
                }
            });

            return $lockedPayment;
        });
    }
}
