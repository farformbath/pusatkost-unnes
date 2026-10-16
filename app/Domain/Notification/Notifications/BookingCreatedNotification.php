<?php

declare(strict_types=1);

namespace App\Domain\Notification\Notifications;

use App\Domain\Booking\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class BookingCreatedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public readonly Booking $booking,
    ) {}

    /**
     * @return list<string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $listingName = $this->booking->listing !== null ? $this->booking->listing->name : 'Kos UNNES';
        $roomName = $this->booking->room !== null ? $this->booking->room->name : 'Kamar';
        $tenantName = $this->booking->user !== null ? $this->booking->user->name : $this->booking->tenant_name;

        return [
            'booking_id' => $this->booking->id,
            'booking_code' => $this->booking->booking_code,
            'listing_name' => $listingName,
            'room_name' => $roomName,
            'tenant_name' => $tenantName,
            'title' => 'Pengajuan Sewa Baru Masuk! 🎉',
            'message' => "Mahasiswa {$tenantName} mengajukan sewa untuk kamar {$roomName}.",
            'action_url' => '/owner/bookings',
        ];
    }
}
