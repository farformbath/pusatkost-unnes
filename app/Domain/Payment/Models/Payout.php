<?php

declare(strict_types=1);

namespace App\Domain\Payment\Models;

use App\Domain\Booking\Models\Booking;
use App\Domain\Payment\Enums\PayoutStatus;
use App\Models\User;
use App\Support\Money;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payout extends Model
{
    use HasUuids;

    protected $fillable = [
        'payment_id',
        'booking_id',
        'owner_id',
        'gross_amount',
        'platform_fee',
        'net_amount',
        'payout_provider',
        'provider_payout_id',
        'status',
        'idempotency_key',
        'requested_at',
        'completed_at',
        'failure_reason',
        'provider_payload',
    ];

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'platform_fee' => 0,
        'payout_provider' => 'sandbox',
        'status' => PayoutStatus::PENDING->value,
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'gross_amount' => 'integer',
        'platform_fee' => 'integer',
        'net_amount' => 'integer',
        'status' => PayoutStatus::class,
        'provider_payload' => 'array',
        'requested_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    /**
     * @var list<string>
     */
    protected $appends = [
        'formatted_gross_amount',
        'formatted_platform_fee',
        'formatted_net_amount',
    ];

    public function getFormattedGrossAmountAttribute(): string
    {
        return Money::format((int) $this->gross_amount);
    }

    public function getFormattedPlatformFeeAttribute(): string
    {
        return Money::format((int) $this->platform_fee);
    }

    public function getFormattedNetAmountAttribute(): string
    {
        return Money::format((int) $this->net_amount);
    }

    /**
     * @return BelongsTo<Payment, $this>
     */
    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    /**
     * @return BelongsTo<Booking, $this>
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }
}
