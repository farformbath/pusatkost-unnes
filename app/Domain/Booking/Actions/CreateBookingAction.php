<?php

declare(strict_types=1);

namespace App\Domain\Booking\Actions;

use App\Domain\Booking\DTOs\BookingData;
use App\Domain\Booking\Enums\BookingStatus;
use App\Domain\Booking\Enums\PaymentStatus;
use App\Domain\Booking\Exceptions\RoomUnavailableException;
use App\Domain\Booking\Models\Booking;
use App\Domain\Booking\Models\Payment;
use App\Domain\Booking\Models\RoomStatusAudit;
use App\Domain\Listing\Enums\RoomStatus;
use App\Domain\Listing\Models\Room;
use App\Domain\Notification\Notifications\BookingCreatedNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use InvalidArgumentException;

class CreateBookingAction
{
    public function execute(BookingData $data): Booking
    {
        if ($data->durationMonths < 1 || $data->durationMonths > 12) {
            throw new InvalidArgumentException('Durasi sewa harus antara 1 sampai 12 bulan.');
        }

        return DB::transaction(function () use ($data): Booking {
            /** @var Room $room */
            $room = Room::query()
                ->where('id', $data->roomId)
                ->lockForUpdate()
                ->firstOrFail();

            // 1. Availability check inside the lock
            if ($room->status !== RoomStatus::AVAILABLE || $room->available_rooms <= 0) {
                throw new RoomUnavailableException('Kamar yang Anda pilih baru saja terisi penuh atau tidak tersedia.');
            }

            $startDate = Carbon::parse($data->startDate);
            $endDate = $startDate->copy()->addMonths($data->durationMonths);

            // 2. Strict Integer Zero Float calculation
            $totalAmount = (int) $room->price_monthly * $data->durationMonths;
            $depositAmount = (int) $room->price_deposit;
            $grandTotal = $totalAmount + $depositAmount;

            // 3. Atomically decrement room availability
            $prevAvailable = $room->available_rooms;
            $newAvailable = $prevAvailable - 1;
            $newStatus = $newAvailable === 0 ? RoomStatus::OCCUPIED : RoomStatus::AVAILABLE;

            $room->update([
                'available_rooms' => $newAvailable,
                'status' => $newStatus,
            ]);

            // 4. Generate unique codes
            $bookingCode = 'BK-'.now()->format('Ymd').'-'.strtoupper(Str::random(6));
            $invoiceNumber = 'INV-'.now()->format('Ymd').'-'.strtoupper(Str::random(6));

            // 5. Create Booking
            $booking = Booking::create([
                'booking_code' => $bookingCode,
                'user_id' => $data->userId,
                'listing_id' => $data->listingId,
                'room_id' => $room->id,
                'tenant_name' => $data->tenantName,
                'tenant_phone' => $data->tenantPhone,
                'start_date' => $startDate->toDateString(),
                'duration_months' => $data->durationMonths,
                'end_date' => $endDate->toDateString(),
                'total_amount' => $totalAmount,
                'deposit_amount' => $depositAmount,
                'grand_total' => $grandTotal,
                'status' => BookingStatus::PENDING,
                'payment_status' => PaymentStatus::PENDING,
                'notes' => $data->notes,
                'expired_at' => now()->addHours(24),
            ]);

            // 6. Create initial simulated payment invoice
            Payment::create([
                'booking_id' => $booking->id,
                'invoice_number' => $invoiceNumber,
                'payment_channel' => 'bca_va',
                'payment_reference' => '8800'.rand(10000000, 99999999),
                'gross_amount' => $grandTotal,
                'transaction_status' => 'pending',
                'payload' => [
                    'bank' => 'BCA',
                    'va_number' => '8800'.rand(10000000, 99999999),
                    'simulation' => true,
                ],
            ]);

            // 7. Create Room Status Audit record
            RoomStatusAudit::create([
                'room_id' => $room->id,
                'booking_id' => $booking->id,
                'user_id' => $data->userId,
                'previous_status' => $room->status->value,
                'new_status' => $newStatus->value,
                'previous_available_count' => $prevAvailable,
                'new_available_count' => $newAvailable,
                'reason' => 'Pemesanan baru dibuat: '.$bookingCode,
            ]);

            // 8. Transaction-safe notification dispatch (afterCommit)
            DB::afterCommit(function () use ($booking): void {
                $booking->load(['listing.owner', 'room', 'user']);
                if ($booking->listing !== null && $booking->listing->owner !== null) {
                    $booking->listing->owner->notify(new BookingCreatedNotification($booking));
                }
            });

            return $booking;
        });
    }
}
