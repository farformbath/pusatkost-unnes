<?php

declare(strict_types=1);

namespace App\Domain\Notification\Notifications;

use App\Domain\Booking\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AdminReviewStartedNotification extends Notification
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
            'amount' => $this->booking->grand_total,
            'title' => 'Verifikasi Admin Dimulai 🔍',
            'message' => "Pembayaran booking #{$this->booking->booking_code} telah masuk ke tahap verifikasi admin UNNESKost.",
            'action_url' => "/admin/payments/{$this->booking->payment?->id}",
        ];
    }
}
