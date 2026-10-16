<?php

declare(strict_types=1);

namespace App\Domain\Payment\Actions;

use App\Domain\Payment\DTOs\PayoutData;
use App\Domain\Payment\Enums\PaymentReviewStatus;
use App\Domain\Payment\Enums\PaymentStatus;
use App\Domain\Payment\Enums\PayoutStatus;
use App\Domain\Payment\Exceptions\PayoutNotAllowedException;
use App\Domain\Payment\Models\Payment;
use App\Domain\Payment\Models\PaymentAudit;
use App\Domain\Payment\Models\PaymentReview;
use App\Domain\Payment\Models\Payout;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ApprovePayoutAction
{
    public function __construct(
        private readonly ProcessPayoutAction $processPayoutAction,
    ) {}

    public function execute(PaymentReview $review, PayoutData $data, User $admin): Payout
    {
        return DB::transaction(function () use ($review, $data, $admin): Payout {
            /** @var PaymentReview $lockedReview */
            $lockedReview = PaymentReview::where('id', $review->id)->lockForUpdate()->firstOrFail();

            /** @var Payment $lockedPayment */
            $lockedPayment = Payment::where('id', $lockedReview->payment_id)->lockForUpdate()->firstOrFail();

            // 1. Validate payment is PAID
            if ($lockedPayment->transaction_status !== PaymentStatus::PAID) {
                throw PayoutNotAllowedException::paymentNotPaid();
            }

            // 2. Validate prerequisites (both tenant and owner contacted & confirmed)
            if (! $lockedReview->isEligibleForApproval()) {
                throw PayoutNotAllowedException::unverifiedPrerequisites();
            }

            // 3. Validate idempotency (no already PAID payout)
            /** @var Payout|null $existingPayout */
            $existingPayout = Payout::where('payment_id', $lockedPayment->id)->first();
            if ($existingPayout && $existingPayout->status === PayoutStatus::PAID) {
                throw PayoutNotAllowedException::alreadyPaid();
            }

            $booking = $lockedPayment->booking;
            if (! $booking || ! $booking->listing) {
                throw new \RuntimeException('Booking or listing associated with payment was not found.');
            }

            $ownerId = (int) $booking->listing->owner_id;
            $grossAmount = (int) $lockedPayment->gross_amount;
            $platformFee = (int) ($data->platformFee ?? 0);
            $netAmount = max(0, $grossAmount - $platformFee);

            $idempotencyKey = $data->idempotencyKey ?? (string) Str::uuid();

            // Create or reuse Payout record
            $payout = $existingPayout ?? Payout::create([
                'payment_id' => $lockedPayment->id,
                'booking_id' => $booking->id,
                'owner_id' => $ownerId,
                'gross_amount' => $grossAmount,
                'platform_fee' => $platformFee,
                'net_amount' => $netAmount,
                'payout_provider' => $data->payoutProvider ?? 'sandbox',
                'status' => PayoutStatus::PENDING,
                'idempotency_key' => $idempotencyKey,
            ]);

            // Mark Review as APPROVED
            $oldReviewStatus = $lockedReview->status->value;
            $lockedReview->update([
                'status' => PaymentReviewStatus::APPROVED,
                'admin_id' => $admin->id,
                'approved_at' => now(),
            ]);

            PaymentAudit::create([
                'payment_id' => $lockedPayment->id,
                'actor_id' => $admin->id,
                'action' => 'payout.approved',
                'old_status' => $oldReviewStatus,
                'new_status' => PaymentReviewStatus::APPROVED->value,
                'metadata' => [
                    'admin_id' => $admin->id,
                    'gross_amount' => $grossAmount,
                    'platform_fee' => $platformFee,
                    'net_amount' => $netAmount,
                    'payout_id' => $payout->id,
                ],
            ]);

            // Execute Payout immediately via ProcessPayoutAction
            return $this->processPayoutAction->execute($payout, $admin);
        });
    }
}
