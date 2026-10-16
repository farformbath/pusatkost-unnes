<?php

declare(strict_types=1);

namespace App\Domain\Booking\Models;

use App\Domain\Booking\Enums\BookingStatus;
use App\Domain\Booking\Enums\PaymentStatus;
use App\Domain\Listing\Models\Listing;
use App\Domain\Listing\Models\Room;
use App\Domain\Payment\Models\PaymentReview;
use App\Domain\Payment\Models\Payout;
use App\Models\User;
use App\Support\Money;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Booking extends Model
{
    use HasUuids;
    use SoftDeletes;

    protected $fillable = [
        'booking_code',
        'user_id',
        'listing_id',
        'room_id',
        'tenant_name',
        'tenant_phone',
        'start_date',
        'duration_months',
        'end_date',
        'total_amount',
        'deposit_amount',
        'grand_total',
        'status',
        'payment_status',
        'notes',
        'expired_at',
    ];

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'status' => BookingStatus::PENDING->value,
        'payment_status' => PaymentStatus::PENDING->value,
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'status' => BookingStatus::class,
        'payment_status' => PaymentStatus::class,
        'start_date' => 'date:Y-m-d',
        'end_date' => 'date:Y-m-d',
        'expired_at' => 'datetime',
        'total_amount' => 'integer',
        'deposit_amount' => 'integer',
        'grand_total' => 'integer',
        'duration_months' => 'integer',
    ];

    /**
     * @var list<string>
     */
    protected $appends = [
        'formatted_total_amount',
        'formatted_deposit_amount',
        'formatted_grand_total',
    ];

    public function getFormattedTotalAmountAttribute(): string
    {
        return Money::format((int) $this->total_amount);
    }

    public function getFormattedDepositAmountAttribute(): string
    {
        return Money::format((int) $this->deposit_amount);
    }

    public function getFormattedGrandTotalAttribute(): string
    {
        return Money::format((int) $this->grand_total);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<Listing, $this>
     */
    public function listing(): BelongsTo
    {
        return $this->belongsTo(Listing::class);
    }

    /**
     * @return BelongsTo<Room, $this>
     */
    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    /**
     * @return HasMany<Payment, $this>
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * @return HasOne<Payment, $this>
     */
    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class)->latestOfMany();
    }

    /**
     * @return HasOne<Payment, $this>
     */
    public function latestPayment(): HasOne
    {
        return $this->hasOne(Payment::class)->latestOfMany();
    }

    /**
     * @return HasMany<RoomStatusAudit, $this>
     */
    public function roomStatusAudits(): HasMany
    {
        return $this->hasMany(RoomStatusAudit::class);
    }

    /**
     * @return HasMany<PaymentReview, $this>
     */
    public function paymentReviews(): HasMany
    {
        return $this->hasMany(PaymentReview::class);
    }

    /**
     * @return HasOne<PaymentReview, $this>
     */
    public function paymentReview(): HasOne
    {
        return $this->hasOne(PaymentReview::class)->latestOfMany();
    }

    /**
     * @return HasMany<Payout, $this>
     */
    public function payouts(): HasMany
    {
        return $this->hasMany(Payout::class);
    }

    /**
     * @return HasOne<Payout, $this>
     */
    public function payout(): HasOne
    {
        return $this->hasOne(Payout::class)->latestOfMany();
    }
}
