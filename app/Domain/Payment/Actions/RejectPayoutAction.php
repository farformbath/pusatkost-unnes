<?php

declare(strict_types=1);

namespace App\Domain\Payment\Actions;

use App\Domain\Payment\Enums\PaymentReviewStatus;
use App\Domain\Payment\Models\PaymentAudit;
use App\Domain\Payment\Models\PaymentReview;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class RejectPayoutAction
{
    public function execute(PaymentReview $review, string $reason, User $admin): PaymentReview
    {
        return DB::transaction(function () use ($review, $reason, $admin): PaymentReview {
            /** @var PaymentReview $lockedReview */
            $lockedReview = PaymentReview::where('id', $review->id)->lockForUpdate()->firstOrFail();

            $oldStatus = $lockedReview->status->value;

            $lockedReview->update([
                'status' => PaymentReviewStatus::REJECTED,
                'admin_id' => $admin->id,
                'rejection_reason' => $reason,
                'rejected_at' => now(),
            ]);

            PaymentAudit::create([
                'payment_id' => $lockedReview->payment_id,
                'actor_id' => $admin->id,
                'action' => 'payout.rejected',
                'old_status' => $oldStatus,
                'new_status' => PaymentReviewStatus::REJECTED->value,
                'metadata' => [
                    'reason' => $reason,
                    'admin_id' => $admin->id,
                ],
            ]);

            return $lockedReview;
        });
    }
}
