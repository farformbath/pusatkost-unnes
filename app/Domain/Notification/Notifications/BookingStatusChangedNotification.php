<?php

declare(strict_types=1);

namespace App\Domain\Notification\Notifications;

use App\Domain\Booking\Enums\BookingStatus;
use App\Domain\Booking\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class BookingStatusChangedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public readonly Booking $booking,
        public readonly BookingStatus $status,
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
        $statusLabel = match ($this->status) {
            BookingStatus::APPROVED => 'Disetujui Pemilik Kos ✅',
            BookingStatus::REJECTED => 'Ditolak Pemilik Kos ❌',
            BookingStatus::COMPLETED => 'Pembayaran Terkonfirmasi & Sewa Aktif 🏠',
            BookingStatus::CANCELLED => 'Dibatalkan',
            default => 'Status Diperbarui',
        };

        $listingName = $this->booking->listing !== null ? $this->booking->listing->name : 'Kos UNNES';

        return [
            'booking_id' => $this->booking->id,
            'booking_code' => $this->booking->booking_code,
            'listing_name' => $listingName,
            'status' => $this->status->value,
            'title' => "Booking {$statusLabel}",
            'message' => "Pengajuan sewa Anda untuk {$listingName} ({$this->booking->booking_code}) saat ini berstatus {$this->status->label()}.",
            'action_url' => "/booking/{$this->booking->id}",
        ];
    }
}
