<?php

declare(strict_types=1);

namespace App\Domain\Listing\Models;

use App\Domain\Booking\Models\Booking;
use App\Domain\Listing\Enums\GenderType;
use App\Domain\Listing\Enums\ListingStatus;
use App\Domain\Review\Models\Review;
use App\Domain\Wishlist\Models\Wishlist;
use App\Models\User;
use App\Support\Money;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Listing extends Model
{
    use HasUuids;
    use SoftDeletes;

    protected $fillable = [
        'owner_id',
        'name',
        'slug',
        'gender_type',
        'description',
        'address',
        'area',
        'latitude',
        'longitude',
        'rules',
        'status',
        'rating_avg',
        'review_count',
    ];

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'status' => ListingStatus::DRAFT->value,
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'gender_type' => GenderType::class,
        'status' => ListingStatus::class,
        'rating_avg' => 'float',
        'review_count' => 'integer',
        'latitude' => 'float',
        'longitude' => 'float',
    ];

    /**
     * @var list<string>
     */
    protected $appends = [
        'starting_price',
        'formatted_starting_price',
    ];

    public function getStartingPriceAttribute(): int
    {
        $minPrice = $this->relationLoaded('rooms')
            ? $this->rooms->min('price_monthly')
            : $this->rooms()->min('price_monthly');

        return is_numeric($minPrice) ? (int) $minPrice : 0;
    }

    public function getFormattedStartingPriceAttribute(): string
    {
        return Money::format($this->starting_price);
    }

    /**
     * @param  Builder<Listing>  $query
     */
    public function scopePublished(Builder $query): void
    {
        $query->where('status', ListingStatus::PUBLISHED);
    }

    /**
     * @param  Builder<Listing>  $query
     */
    public function scopeByGender(Builder $query, GenderType $gender): void
    {
        $query->where('gender_type', $gender);
    }

    /**
     * @param  Builder<Listing>  $query
     * @param  string|list<string>  $area
     */
    public function scopeByArea(Builder $query, string|array $area): void
    {
        if (is_array($area)) {
            $query->whereIn('area', $area);
        } else {
            $query->where('area', $area);
        }
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * @return HasMany<Room, $this>
     */
    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class);
    }

    /**
     * @return HasMany<ListingImage, $this>
     */
    public function images(): HasMany
    {
        return $this->hasMany(ListingImage::class)->orderBy('sort_order');
    }

    /**
     * @return HasOne<ListingImage, $this>
     */
    public function primaryImage(): HasOne
    {
        return $this->hasOne(ListingImage::class)->where('is_primary', true);
    }

    /**
     * @return BelongsToMany<Facility, $this>
     */
    public function facilities(): BelongsToMany
    {
        return $this->belongsToMany(Facility::class, 'facility_listing');
    }

    /**
     * @return HasMany<Booking, $this>
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * @return HasMany<Review, $this>
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class)->latest();
    }

    /**
     * @return HasMany<Wishlist, $this>
     */
    public function wishlists(): HasMany
    {
        return $this->hasMany(Wishlist::class);
    }
}
