<?php

declare(strict_types=1);

namespace App\Domain\Payment\Actions;

use App\Domain\Payment\DTOs\ContactVerificationData;
use App\Domain\Payment\Enums\PaymentReviewStatus;
use App\Domain\Payment\Models\PaymentAudit;
use App\Domain\Payment\Models\PaymentReview;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class RecordPartyContactAction
{
    public function execute(PaymentReview $review, ContactVerificationData $data, User $admin): PaymentReview
    {
        return DB::transaction(function () use ($review, $data, $admin): PaymentReview {
            /** @var PaymentReview $lockedReview */
            $lockedReview = PaymentReview::where('id', $review->id)->lockForUpdate()->firstOrFail();

            $oldStatus = $lockedReview->status->value;

            $isFullyConfirmed = $data->areBothPartiesConfirmed();
            $newStatus = $isFullyConfirmed
                ? PaymentReviewStatus::VERIFIED
                : PaymentReviewStatus::CONTACTING;

            $lockedReview->update([
                'tenant_contacted' => $data->tenantContacted,
                'owner_contacted' => $data->ownerContacted,
                'tenant_confirmed' => $data->tenantConfirmed,
                'owner_confirmed' => $data->ownerConfirmed,
                'admin_id' => $admin->id,
                'admin_notes' => $data->adminNotes ?? $lockedReview->admin_notes,
                'status' => $newStatus,
                'reviewed_at' => now(),
            ]);

            PaymentAudit::create([
                'payment_id' => $lockedReview->payment_id,
                'actor_id' => $admin->id,
                'action' => 'review.contact_recorded',
                'old_status' => $oldStatus,
                'new_status' => $newStatus->value,
                'metadata' => [
                    'tenant_contacted' => $data->tenantContacted,
                    'owner_contacted' => $data->ownerContacted,
                    'tenant_confirmed' => $data->tenantConfirmed,
                    'owner_confirmed' => $data->ownerConfirmed,
                    'admin_notes' => $data->adminNotes,
                ],
            ]);

            return $lockedReview;
        });
    }
}
