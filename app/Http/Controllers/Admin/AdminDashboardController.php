<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Domain\Booking\Models\Booking;
use App\Domain\Booking\Models\Payment;
use App\Domain\Listing\Enums\ListingStatus;
use App\Domain\Listing\Models\Listing;
use App\Domain\Payment\Enums\PaymentReviewStatus;
use App\Domain\Payment\Models\PaymentReview;
use App\Domain\Review\Models\Review;
use App\Domain\User\Enums\OwnerVerificationStatus;
use App\Domain\User\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AdminDashboardController extends Controller
{
    public function index(Request $request): Response
    {
        /** @var User $currentUser */
        $currentUser = $request->user();

        if (! $currentUser->isAdmin()) {
            abort(403, 'Akses khusus administrator UNNESKost.');
        }

        $totalListings = Listing::query()->count();
        $publishedListings = Listing::query()->where('status', ListingStatus::PUBLISHED)->count();
        $totalOwners = User::role(UserRole::OWNER->value)->count();
        $pendingOwnersCount = User::role(UserRole::OWNER->value)
            ->where('owner_verification_status', OwnerVerificationStatus::PENDING)
            ->count();
        $totalBookings = Booking::query()->count();
        $totalRevenue = (int) Payment::query()
            ->where('transaction_status', 'paid')
            ->sum('gross_amount');
        $totalReviews = Review::query()->count();

        $pendingReviewsCount = PaymentReview::query()
            ->whereIn('status', [PaymentReviewStatus::PENDING, PaymentReviewStatus::CONTACTING])
            ->count();

        $verifiedPayoutsCount = PaymentReview::query()
            ->where('status', PaymentReviewStatus::VERIFIED)
            ->count();

        $pendingOwners = User::role(UserRole::OWNER->value)
            ->where('owner_verification_status', OwnerVerificationStatus::PENDING)
            ->latest()
            ->take(5)
            ->get(['id', 'name', 'email', 'phone', 'created_at', 'owner_verification_status']);

        $recentReviews = Review::query()
            ->with(['user:id,name,email', 'listing:id,name,slug'])
            ->latest()
            ->take(5)
            ->get();

        return Inertia::render('Admin/Dashboard', [
            'stats' => [
                'total_listings' => $totalListings,
                'published_listings' => $publishedListings,
                'total_owners' => $totalOwners,
                'pending_owners_count' => $pendingOwnersCount,
                'total_bookings' => $totalBookings,
                'total_revenue' => $totalRevenue,
                'total_reviews' => $totalReviews,
                'pending_reviews_count' => $pendingReviewsCount,
                'verified_payouts_count' => $verifiedPayoutsCount,
            ],
            'pending_owners' => $pendingOwners,
            'recent_reviews' => $recentReviews,
        ]);
    }
}
