<?php

declare(strict_types=1);

namespace App\Domain\Payment\Models;

use App\Domain\Booking\Models\Booking;
use App\Domain\Payment\Enums\PaymentReviewStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentReview extends Model
{
    use HasUuids;

    protected $fillable = [
        'payment_id',
        'booking_id',
        'status',
        'tenant_contacted',
        'owner_contacted',
        'tenant_confirmed',
        'owner_confirmed',
        'admin_id',
        'admin_notes',
        'reviewed_at',
        'approved_at',
        'rejected_at',
        'rejection_reason',
    ];

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'status' => PaymentReviewStatus::PENDING->value,
        'tenant_contacted' => false,
        'owner_contacted' => false,
        'tenant_confirmed' => false,
        'owner_confirmed' => false,
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'status' => PaymentReviewStatus::class,
        'tenant_contacted' => 'boolean',
        'owner_contacted' => 'boolean',
        'tenant_confirmed' => 'boolean',
        'owner_confirmed' => 'boolean',
        'reviewed_at' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

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
    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function isEligibleForApproval(): bool
    {
        return $this->tenant_contacted
            && $this->owner_contacted
            && $this->tenant_confirmed
            && $this->owner_confirmed
            && ($this->status === PaymentReviewStatus::VERIFIED || $this->status === PaymentReviewStatus::CONTACTING);
    }
}
