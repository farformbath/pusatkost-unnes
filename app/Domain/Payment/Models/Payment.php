<?php

declare(strict_types=1);

namespace App\Domain\Payment\Models;

use App\Domain\Booking\Models\Booking;
use App\Domain\Payment\Enums\PaymentStatus;
use App\Support\Money;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Payment extends Model
{
    use HasUuids;

    protected $fillable = [
        'booking_id',
        'invoice_number',
        'payment_provider',
        'payment_channel',
        'payment_reference',
        'provider_transaction_id',
        'gross_amount',
        'transaction_status',
        'payload',
        'provider_payload',
        'idempotency_key',
        'paid_at',
        'verified_at',
    ];

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'payment_provider' => 'sandbox',
        'transaction_status' => PaymentStatus::PENDING->value,
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'gross_amount' => 'integer',
        'transaction_status' => PaymentStatus::class,
        'payload' => 'array',
        'provider_payload' => 'array',
        'paid_at' => 'datetime',
        'verified_at' => 'datetime',
    ];

    /**
     * @var list<string>
     */
    protected $appends = [
        'formatted_gross_amount',
    ];

    public function getFormattedGrossAmountAttribute(): string
    {
        return Money::format((int) $this->gross_amount);
    }

    /**
     * @return BelongsTo<Booking, $this>
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * @return HasOne<PaymentReview, $this>
     */
    public function review(): HasOne
    {
        return $this->hasOne(PaymentReview::class);
    }

    /**
     * @return HasOne<Payout, $this>
     */
    public function payout(): HasOne
    {
        return $this->hasOne(Payout::class);
    }

    /**
     * @return HasMany<PaymentAudit, $this>
     */
    public function audits(): HasMany
    {
        return $this->hasMany(PaymentAudit::class)->orderByDesc('created_at');
    }
}
