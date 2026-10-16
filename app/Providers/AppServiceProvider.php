<?php

declare(strict_types=1);

namespace App\Providers;

use App\Domain\Booking\Models\Booking;
use App\Domain\Booking\Policies\BookingPolicy;
use App\Domain\Booking\Repositories\BookingRepositoryInterface;
use App\Domain\Booking\Repositories\EloquentBookingRepository;
use App\Domain\Listing\Models\Listing;
use App\Domain\Listing\Models\Room;
use App\Domain\Listing\Policies\ListingPolicy;
use App\Domain\Listing\Policies\RoomPolicy;
use App\Domain\Listing\Repositories\EloquentListingRepository;
use App\Domain\Listing\Repositories\ListingRepositoryInterface;
use App\Domain\Payment\Models\Payment;
use App\Domain\Payment\Models\PaymentReview;
use App\Domain\Payment\Models\Payout;
use App\Domain\Payment\Policies\PaymentPolicy;
use App\Domain\Payment\Policies\PaymentReviewPolicy;
use App\Domain\Payment\Policies\PayoutPolicy;
use App\Domain\Payment\Services\PaymentGatewayInterface;
use App\Domain\Payment\Services\SandboxPaymentGateway;
use App\Domain\Review\Models\Review;
use App\Domain\Review\Policies\ReviewPolicy;
use App\Domain\Review\Repositories\EloquentReviewRepository;
use App\Domain\Review\Repositories\ReviewRepositoryInterface;
use App\Domain\Search\Repositories\EloquentSearchRepository;
use App\Domain\Search\Repositories\SearchRepositoryInterface;
use App\Domain\Wishlist\Repositories\EloquentWishlistRepository;
use App\Domain\Wishlist\Repositories\WishlistRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(ListingRepositoryInterface::class, EloquentListingRepository::class);
        $this->app->bind(BookingRepositoryInterface::class, EloquentBookingRepository::class);
        $this->app->bind(SearchRepositoryInterface::class, EloquentSearchRepository::class);
        $this->app->bind(
            ReviewRepositoryInterface::class,
            EloquentReviewRepository::class
        );
        $this->app->bind(
            WishlistRepositoryInterface::class,
            EloquentWishlistRepository::class
        );
        $this->app->bind(
            PaymentGatewayInterface::class,
            SandboxPaymentGateway::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Model::shouldBeStrict(! app()->isProduction());

        // Explicit Policy Bindings
        Gate::policy(Listing::class, ListingPolicy::class);
        Gate::policy(Room::class, RoomPolicy::class);
        Gate::policy(Booking::class, BookingPolicy::class);
        Gate::policy(Review::class, ReviewPolicy::class);
        Gate::policy(Payment::class, PaymentPolicy::class);
        Gate::policy(PaymentReview::class, PaymentReviewPolicy::class);
        Gate::policy(Payout::class, PayoutPolicy::class);
    }
}
