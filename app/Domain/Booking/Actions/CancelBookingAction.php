<?php

declare(strict_types=1);

namespace App\Domain\Booking\Actions;

use App\Domain\Booking\Enums\BookingStatus;
use App\Domain\Booking\Enums\PaymentStatus;
use App\Domain\Booking\Models\Booking;
use App\Domain\Booking\Models\RoomStatusAudit;
use App\Domain\Listing\Enums\RoomStatus;
use App\Domain\Listing\Models\Room;
use DomainException;
use Illuminate\Support\Facades\DB;

class CancelBookingAction
{
    public function execute(Booking $booking, ?string $reason = null): Booking
    {
        if ($booking->status === BookingStatus::CANCELLED) {
            return $booking;
        }

        if (! $booking->status->canBeCancelled()) {
            throw new DomainException("Pemesanan dengan status [{$booking->status->value}] tidak dapat dibatalkan.");
        }

        return DB::transaction(function () use ($booking, $reason): Booking {
            /** @var Room $room */
            $room = Room::query()
                ->where('id', $booking->room_id)
                ->lockForUpdate()
                ->firstOrFail();

            $prevAvailable = $room->available_rooms;
            $newAvailable = min($room->total_rooms, $prevAvailable + 1);
            $newStatus = RoomStatus::AVAILABLE;

            $room->update([
                'available_rooms' => $newAvailable,
                'status' => $newStatus,
            ]);

            $booking->update([
                'status' => BookingStatus::CANCELLED,
                'payment_status' => PaymentStatus::CANCELLED,
            ]);

            RoomStatusAudit::create([
                'room_id' => $room->id,
                'booking_id' => $booking->id,
                'user_id' => $booking->user_id,
                'previous_status' => $room->status->value,
                'new_status' => $newStatus->value,
                'previous_available_count' => $prevAvailable,
                'new_available_count' => $newAvailable,
                'reason' => 'Pemesanan dibatalkan oleh pengguna: '.($reason ?? $booking->booking_code),
            ]);

            return $booking;
        });
    }
}
