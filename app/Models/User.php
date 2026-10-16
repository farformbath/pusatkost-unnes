<?php

declare(strict_types=1);

namespace App\Models;

use App\Domain\Booking\Models\Booking;
use App\Domain\Listing\Models\Listing;
use App\Domain\Review\Models\Review;
use App\Domain\User\Enums\OwnerVerificationStatus;
use App\Domain\User\Enums\UserRole;
use App\Domain\Wishlist\Models\Wishlist;
use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

/**
 * @property OwnerVerificationStatus|null $owner_verification_status
 */
class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory;

    use HasRoles;
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     * Note: 'owner_verification_status' is protected from mass assignment to prevent privilege escalation.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'avatar_url',
        'password',
    ];

    /**
     * Default model attributes.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'phone' => null,
        'avatar_url' => null,
        'owner_verification_status' => null,
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'owner_verification_status' => OwnerVerificationStatus::class,
        ];
    }

    public function isAdmin(): bool
    {
        return $this->hasRole(UserRole::ADMIN->value);
    }

    public function isOwner(): bool
    {
        return $this->hasRole(UserRole::OWNER->value);
    }

    public function isVerifiedOwner(): bool
    {
        $status = $this->getAttributes()['owner_verification_status'] ?? null;

        return $this->isOwner() && ($status === OwnerVerificationStatus::VERIFIED->value || $status === OwnerVerificationStatus::VERIFIED);
    }

    public function isTenant(): bool
    {
        return $this->hasRole(UserRole::TENANT->value);
    }

    /**
     * @return HasMany<Listing, $this>
     */
    public function listings(): HasMany
    {
        return $this->hasMany(Listing::class, 'owner_id');
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
        return $this->hasMany(Review::class);
    }

    /**
     * @return HasMany<Wishlist, $this>
     */
    public function wishlists(): HasMany
    {
        return $this->hasMany(Wishlist::class);
    }
}
