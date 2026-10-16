<?php

declare(strict_types=1);

namespace App\Domain\Payment\Actions;

use App\Domain\Payment\Enums\PaymentReviewStatus;
use App\Domain\Payment\Models\Payment;
use App\Domain\Payment\Models\PaymentAudit;
use App\Domain\Payment\Models\PaymentReview;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class StartAdminReviewAction
{
    public function execute(Payment $payment, User $admin): PaymentReview
    {
        return DB::transaction(function () use ($payment, $admin): PaymentReview {
            /** @var PaymentReview $review */
            $review = PaymentReview::firstOrCreate(
                [
                    'payment_id' => $payment->id,
                    'booking_id' => $payment->booking_id,
                ],
                [
                    'status' => PaymentReviewStatus::PENDING,
                    'tenant_contacted' => false,
                    'owner_contacted' => false,
                    'tenant_confirmed' => false,
                    'owner_confirmed' => false,
                ]
            );

            if ($review->status === PaymentReviewStatus::PENDING) {
                $review->update([
                    'status' => PaymentReviewStatus::CONTACTING,
                    'admin_id' => $admin->id,
                    'reviewed_at' => now(),
                ]);

                PaymentAudit::create([
                    'payment_id' => $payment->id,
                    'actor_id' => $admin->id,
                    'action' => 'review.started',
                    'old_status' => PaymentReviewStatus::PENDING->value,
                    'new_status' => PaymentReviewStatus::CONTACTING->value,
                    'metadata' => [
                        'admin_id' => $admin->id,
                        'admin_name' => $admin->name,
                    ],
                ]);
            }

            return $review;
        });
    }
}
