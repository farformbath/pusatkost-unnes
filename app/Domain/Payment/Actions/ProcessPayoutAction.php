<?php

declare(strict_types=1);

namespace App\Domain\Payment\Actions;

use App\Domain\Booking\Enums\BookingStatus;
use App\Domain\Notification\Notifications\BookingApprovedNotification;
use App\Domain\Notification\Notifications\PayoutCompletedNotification;
use App\Domain\Notification\Notifications\PayoutFailedNotification;
use App\Domain\Payment\Enums\PayoutStatus;
use App\Domain\Payment\Models\PaymentAudit;
use App\Domain\Payment\Models\Payout;
use App\Domain\Payment\Services\PaymentGatewayInterface;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ProcessPayoutAction
{
    public function __construct(
        private readonly PaymentGatewayInterface $gateway,
    ) {}

    public function execute(Payout $payout, ?User $actor = null): Payout
    {
        return DB::transaction(function () use ($payout, $actor): Payout {
            /** @var Payout $lockedPayout */
            $lockedPayout = Payout::where('id', $payout->id)->lockForUpdate()->firstOrFail();

            if ($lockedPayout->status === PayoutStatus::PAID) {
                return $lockedPayout;
            }

            $lockedPayout->update([
                'status' => PayoutStatus::PROCESSING,
                'requested_at' => now(),
            ]);

            // Call Payment Gateway to disburse funds
            $result = $this->gateway->processPayout($lockedPayout);

            $oldStatus = $lockedPayout->status->value;

            if ($result['success']) {
                $lockedPayout->update([
                    'status' => PayoutStatus::PAID,
                    'provider_payout_id' => $result['provider_payout_id'],
                    'provider_payload' => $result['raw'],
                    'completed_at' => now(),
                    'failure_reason' => null,
                ]);

                // Synchronize Booking status to COMPLETED / APPROVED
                $booking = $lockedPayout->booking;
                if ($booking) {
                    $booking->update([
                        'status' => BookingStatus::COMPLETED,
                    ]);
                }

                PaymentAudit::create([
                    'payment_id' => $lockedPayout->payment_id,
                    'actor_id' => $actor?->id,
                    'action' => 'payout.completed',
                    'old_status' => $oldStatus,
                    'new_status' => PayoutStatus::PAID->value,
                    'metadata' => [
                        'provider_payout_id' => $result['provider_payout_id'],
                        'net_amount' => $lockedPayout->net_amount,
                        'owner_id' => $lockedPayout->owner_id,
                    ],
                ]);

                // Send Notifications after commit
                DB::afterCommit(function () use ($lockedPayout): void {
                    $lockedPayout->load(['owner', 'booking.user', 'booking.listing']);

                    if ($lockedPayout->owner) {
                        $lockedPayout->owner->notify(new PayoutCompletedNotification($lockedPayout));
                    }

                    $booking = $lockedPayout->booking;
                    if ($booking && $booking->user) {
                        $booking->user->notify(new BookingApprovedNotification($booking));
                    }
                });
            } else {
                $lockedPayout->update([
                    'status' => PayoutStatus::FAILED,
                    'failure_reason' => $result['error'] ?? 'Payout disbursement failed.',
                    'provider_payload' => $result['raw'],
                ]);

                PaymentAudit::create([
                    'payment_id' => $lockedPayout->payment_id,
                    'actor_id' => $actor?->id,
                    'action' => 'payout.failed',
                    'old_status' => $oldStatus,
                    'new_status' => PayoutStatus::FAILED->value,
                    'metadata' => [
                        'error' => $result['error'] ?? 'Unknown error',
                        'net_amount' => $lockedPayout->net_amount,
                    ],
                ]);

                DB::afterCommit(function () use ($lockedPayout): void {
                    $lockedPayout->load(['owner']);
                    if ($lockedPayout->owner) {
                        $lockedPayout->owner->notify(new PayoutFailedNotification($lockedPayout));
                    }
                });
            }

            return $lockedPayout;
        });
    }
}
