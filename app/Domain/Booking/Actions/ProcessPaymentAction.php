<?php

declare(strict_types=1);

namespace App\Domain\Booking\Actions;

use App\Domain\Booking\Enums\BookingStatus;
use App\Domain\Booking\Enums\PaymentStatus;
use App\Domain\Booking\Models\Booking;
use App\Domain\Booking\Models\Payment;
use App\Domain\Notification\Notifications\BookingStatusChangedNotification;
use App\Domain\Notification\Notifications\PaymentConfirmedNotification;
use App\Domain\Payment\Enums\PaymentReviewStatus;
use App\Domain\Payment\Models\PaymentAudit;
use App\Domain\Payment\Models\PaymentReview;
use Illuminate\Support\Facades\DB;

class ProcessPaymentAction
{
    public function execute(Booking $booking, string $channel = 'bca_va'): Booking
    {
        // Strict Idempotency: if already paid, do not perform updates or send duplicate notifications
        if ($booking->payment_status === PaymentStatus::PAID) {
            return $booking;
        }

        return DB::transaction(function () use ($booking, $channel): Booking {
            $payment = $booking->latestPayment ?? Payment::create([
                'booking_id' => $booking->id,
                'invoice_number' => 'INV-'.now()->format('Ymd').'-'.strtoupper(substr(md5($booking->id), 0, 6)),
                'payment_channel' => $channel,
                'gross_amount' => $booking->grand_total,
                'transaction_status' => 'paid',
                'payment_provider' => 'sandbox',
            ]);

            $payment->update([
                'transaction_status' => 'paid',
                'paid_at' => now(),
                'verified_at' => now(),
            ]);

            // Initialize PaymentReview for admin review workflow
            PaymentReview::firstOrCreate(
                [
                    'payment_id' => $payment->id,
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

            // Audit Trail
            PaymentAudit::create([
                'payment_id' => $payment->id,
                'actor_id' => $booking->user_id,
                'action' => 'payment.simulated',
                'old_status' => 'pending',
                'new_status' => 'paid',
                'metadata' => [
                    'channel' => $channel,
                    'gross_amount' => $booking->grand_total,
                ],
            ]);

            $newBookingStatus = $booking->status === BookingStatus::PENDING ? BookingStatus::APPROVED : $booking->status;

            $booking->update([
                'payment_status' => PaymentStatus::PAID,
                'status' => $newBookingStatus,
            ]);

            // Dispatch notifications strictly after transaction commit
            DB::afterCommit(function () use ($booking): void {
                $booking->load(['user', 'listing.owner', 'room']);

                if ($booking->user) {
                    $booking->user->notify(new PaymentConfirmedNotification($booking));
                    $booking->user->notify(new BookingStatusChangedNotification($booking, BookingStatus::COMPLETED));
                }

                if ($booking->listing && $booking->listing->owner) {
                    $booking->listing->owner->notify(new PaymentConfirmedNotification($booking));
                }
            });

            return $booking;
        });
    }
}
