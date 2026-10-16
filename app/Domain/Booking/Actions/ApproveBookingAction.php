<?php

declare(strict_types=1);

namespace App\Domain\Booking\Actions;

use App\Domain\Booking\Enums\BookingStatus;
use App\Domain\Booking\Models\Booking;
use App\Domain\Notification\Notifications\BookingStatusChangedNotification;
use DomainException;
use Illuminate\Support\Facades\DB;

class ApproveBookingAction
{
    public function execute(Booking $booking): Booking
    {
        if (! $booking->status->canBeApproved()) {
            throw new DomainException("Pemesanan dengan status [{$booking->status->value}] tidak dapat disetujui.");
        }

        return DB::transaction(function () use ($booking): Booking {
            $booking->update([
                'status' => BookingStatus::APPROVED,
            ]);

            DB::afterCommit(function () use ($booking): void {
                $booking->load(['user', 'listing']);
                if ($booking->user) {
                    $booking->user->notify(new BookingStatusChangedNotification($booking, BookingStatus::APPROVED));
                }
            });

            return $booking;
        });
    }
}
