<?php

declare(strict_types=1);

namespace App\Domain\Booking\Models;

use App\Support\Money;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
     * @var array<string, string>
     */
    protected $casts = [
        'gross_amount' => 'integer',
        'payload' => 'array',
        'paid_at' => 'datetime',
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
}
