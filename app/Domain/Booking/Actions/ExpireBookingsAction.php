<?php

declare(strict_types=1);

namespace App\Domain\Booking\Actions;

use App\Domain\Booking\Enums\BookingStatus;
use App\Domain\Booking\Enums\PaymentStatus;
use App\Domain\Booking\Models\Booking;
use App\Domain\Booking\Models\RoomStatusAudit;
use App\Domain\Listing\Enums\RoomStatus;
use App\Domain\Listing\Models\Room;
use Illuminate\Support\Facades\DB;

class ExpireBookingsAction
{
    /**
     * @return int Number of expired bookings processed
     */
    public function execute(): int
    {
        $expiredBookings = Booking::query()
            ->where('status', BookingStatus::PENDING)
            ->where('expired_at', '<', now())
            ->get();

        $count = 0;

        foreach ($expiredBookings as $booking) {
            DB::transaction(function () use ($booking): void {
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
                    'status' => BookingStatus::EXPIRED,
                    'payment_status' => PaymentStatus::EXPIRED,
                ]);

                RoomStatusAudit::create([
                    'room_id' => $room->id,
                    'booking_id' => $booking->id,
                    'user_id' => $booking->user_id,
                    'previous_status' => $room->status->value,
                    'new_status' => $newStatus->value,
                    'previous_available_count' => $prevAvailable,
                    'new_available_count' => $newAvailable,
                    'reason' => 'Pemesanan kadaluarsa otomatis: '.$booking->booking_code,
                ]);
            });

            $count++;
        }

        return $count;
    }
}
