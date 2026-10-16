<?php

declare(strict_types=1);

namespace App\Domain\Notification\Notifications;

use App\Domain\Payment\Models\Payout;
use App\Support\Money;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PayoutCompletedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public readonly Payout $payout,
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
            'payout_id' => $this->payout->id,
            'booking_id' => $this->payout->booking_id,
            'amount' => $this->payout->net_amount,
            'title' => 'Dana Sewa Berhasil Dicairkan! 💸',
            'message' => 'Pencairan dana sewa sebesar '.Money::format($this->payout->net_amount)." (Ref: {$this->payout->provider_payout_id}) telah berhasil ditransfer.",
            'action_url' => "/owner/payouts/{$this->payout->id}",
        ];
    }
}
