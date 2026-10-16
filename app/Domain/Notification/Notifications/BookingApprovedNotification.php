<?php

declare(strict_types=1);

namespace App\Domain\Notification\Notifications;

use App\Domain\Booking\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class BookingApprovedNotification extends Notification
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
        return [
            'booking_id' => $this->booking->id,
            'booking_code' => $this->booking->booking_code,
            'title' => 'Sewa Kos Resmi Disetujui & Aktif! 🏠',
            'message' => "Selamat! Booking #{$this->booking->booking_code} di {$this->booking->listing?->name} telah resmi disetujui.",
            'action_url' => "/booking/{$this->booking->id}",
        ];
    }
}
