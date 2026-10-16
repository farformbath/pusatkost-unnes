<?php

declare(strict_types=1);

namespace App\Domain\Listing\Models;

use App\Domain\Booking\Models\Booking;
use App\Domain\Booking\Models\RoomStatusAudit;
use App\Domain\Listing\Enums\RoomStatus;
use App\Support\Money;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Room extends Model
{
    use HasUuids;
    use SoftDeletes;

    protected $fillable = [
        'listing_id',
        'name',
        'size',
        'price_monthly',
        'price_deposit',
        'status',
        'total_rooms',
        'available_rooms',
    ];

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'status' => RoomStatus::AVAILABLE->value,
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'status' => RoomStatus::class,
        'price_monthly' => 'integer',
        'price_deposit' => 'integer',
        'total_rooms' => 'integer',
        'available_rooms' => 'integer',
    ];

    /**
     * @var list<string>
     */
    protected $appends = [
        'formatted_price_monthly',
        'formatted_price_deposit',
    ];

    public function getFormattedPriceMonthlyAttribute(): string
    {
        return Money::format((int) $this->price_monthly);
    }

    public function getFormattedPriceDepositAttribute(): string
    {
        return Money::format((int) $this->price_deposit);
    }

    /**
     * @param  Builder<Room>  $query
     */
    public function scopeAvailable(Builder $query): void
    {
        $query->where('status', RoomStatus::AVAILABLE)
            ->where('available_rooms', '>', 0);
    }

    /**
     * @return BelongsTo<Listing, $this>
     */
    public function listing(): BelongsTo
    {
        return $this->belongsTo(Listing::class);
    }

    /**
     * @return HasMany<ListingImage, $this>
     */
    public function images(): HasMany
    {
        return $this->hasMany(ListingImage::class)->orderBy('sort_order');
    }

    /**
     * @return BelongsToMany<Facility, $this>
     */
    public function facilities(): BelongsToMany
    {
        return $this->belongsToMany(Facility::class, 'facility_room');
    }

    /**
     * @return HasMany<Booking, $this>
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * @return HasMany<RoomStatusAudit, $this>
     */
    public function roomStatusAudits(): HasMany
    {
        return $this->hasMany(RoomStatusAudit::class);
    }
}
