<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminOwnerController;
use App\Http\Controllers\Admin\AdminPaymentController;
use App\Http\Controllers\Admin\AdminReviewController;
use App\Http\Controllers\Ai\NesaChatController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Booking\BookingController;
use App\Http\Controllers\Booking\InvoiceController;
use App\Http\Controllers\Booking\PaymentController;
use App\Http\Controllers\Booking\ReceiptController;
use App\Http\Controllers\HealthCheckController;
use App\Http\Controllers\Notification\NotificationController;
use App\Http\Controllers\Owner\BookingManagementController as OwnerBookingController;
use App\Http\Controllers\Owner\ListingController as OwnerListingController;
use App\Http\Controllers\Owner\ListingImageController as OwnerListingImageController;
use App\Http\Controllers\Owner\OwnerPayoutController;
use App\Http\Controllers\Owner\RoomController as OwnerRoomController;
use App\Http\Controllers\Payment\WebhookController;
use App\Http\Controllers\Profile\ProfileController;
use App\Http\Controllers\Public\ListingController as PublicListingController;
use App\Http\Controllers\Review\ReviewController;
use App\Http\Controllers\Tenant\TenantBookingController;
use App\Http\Controllers\Wishlist\WishlistController;
use Illuminate\Support\Facades\Route;

// Observability & System Health Check
Route::get('/health', HealthCheckController::class)->name('health');

// Payment Webhook (Provider to Server, Excluded from CSRF)
Route::post('/webhooks/payment/{provider}', WebhookController::class)->name('webhooks.payment');

// Public Listings Catalog & Detail
Route::get('/', [PublicListingController::class, 'index'])->name('home');
Route::get('/kos', [PublicListingController::class, 'index'])->name('listings.index');
Route::get('/kos/{slug}', [PublicListingController::class, 'show'])->name('listings.show');

// Authentication Routes (Guest Only)
Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);
    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store']);
});

// Authenticated Routes
Route::middleware('auth')->group(function (): void {
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    // Email Verification Flow (Laravel Native)
    Route::get('/email/verify', [EmailVerificationController::class, 'notice'])->name('verification.notice');
    Route::get('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');
    Route::post('/email/verification-notification', [EmailVerificationController::class, 'send'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    // Profile Management & Avatar Upload
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    // In-App Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.readAll');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');

    // Admin Endpoints
    Route::prefix('admin')->name('admin.')->group(function (): void {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('/owners', [AdminOwnerController::class, 'index'])->name('owners.index');
        Route::post('/owners/{user}/verify', [AdminOwnerController::class, 'verify'])->name('owners.verify');
        Route::post('/owners/{user}/reject', [AdminOwnerController::class, 'reject'])->name('owners.reject');
        Route::get('/reviews', [AdminReviewController::class, 'index'])->name('reviews.index');
        Route::delete('/reviews/{review}', [AdminReviewController::class, 'destroy'])->name('reviews.destroy');

        // Admin Payment Review & Payout Approvals
        Route::get('/payments', [AdminPaymentController::class, 'index'])->name('payments.index');
        Route::get('/payments/{payment}', [AdminPaymentController::class, 'show'])->name('payments.show');
        Route::post('/payment-reviews/{review}/contact', [AdminPaymentController::class, 'verifyContact'])->name('payment-reviews.contact');
        Route::post('/payment-reviews/{review}/approve', [AdminPaymentController::class, 'approve'])->name('payment-reviews.approve');
        Route::post('/payment-reviews/{review}/reject', [AdminPaymentController::class, 'reject'])->name('payment-reviews.reject');
    });
});

// Reviews & Ratings
Route::post('/kos/{slug}/reviews', [ReviewController::class, 'store'])->name('reviews.store');
Route::post('/reviews/{id}/reply', [ReviewController::class, 'reply'])->name('reviews.reply');

// Wishlist
Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
Route::post('/wishlist/toggle', [WishlistController::class, 'toggle'])->name('wishlist.toggle');

// Booking Flow
Route::get('/booking/{room}/checkout', [BookingController::class, 'checkout'])->name('booking.checkout');
Route::post('/booking', [BookingController::class, 'store'])->name('booking.store');
Route::get('/booking/{id}', [BookingController::class, 'show'])->name('booking.show');
Route::get('/booking/{id}/invoice', [InvoiceController::class, 'show'])->middleware('auth')->name('booking.invoice');
Route::get('/payment/{payment}/receipt', [ReceiptController::class, 'show'])->middleware('auth')->name('payment.receipt');
Route::post('/booking/{id}/cancel', [BookingController::class, 'cancel'])->name('booking.cancel');
Route::post('/booking/{id}/payment', [PaymentController::class, 'simulate'])->name('booking.payment');

// Tenant Booking History
Route::get('/tenant/bookings', [TenantBookingController::class, 'index'])->middleware('auth')->name('tenant.bookings.index');

// Owner Management
Route::prefix('owner')->name('owner.')->middleware('auth')->group(function (): void {
    // Listings
    Route::get('/kos', [OwnerListingController::class, 'index'])->name('listings.index');
    Route::get('/kos/create', [OwnerListingController::class, 'create'])->name('listings.create');
    Route::post('/kos', [OwnerListingController::class, 'store'])->name('listings.store');
    Route::get('/kos/{id}/edit', [OwnerListingController::class, 'edit'])->name('listings.edit');
    Route::put('/kos/{listing}', [OwnerListingController::class, 'update'])->name('listings.update');
    Route::post('/kos/{listing}/publish', [OwnerListingController::class, 'publish'])->name('listings.publish');

    // Rooms
    Route::get('/kos/{listing}/kamar', [OwnerRoomController::class, 'index'])->name('rooms.index');
    Route::post('/kos/{listing}/kamar', [OwnerRoomController::class, 'store'])->name('rooms.store');
    Route::patch('/kamar/{room}/toggle', [OwnerRoomController::class, 'toggleAvailability'])->name('rooms.toggle');

    // Images
    Route::post('/kos/{listing}/images', [OwnerListingImageController::class, 'store'])->name('images.store');

    // Bookings Management
    Route::get('/bookings', [OwnerBookingController::class, 'index'])->name('bookings.index');
    Route::post('/bookings/{id}/approve', [OwnerBookingController::class, 'approve'])->name('bookings.approve');
    Route::post('/bookings/{id}/reject', [OwnerBookingController::class, 'reject'])->name('bookings.reject');

    // Owner Payouts
    Route::get('/payouts', [OwnerPayoutController::class, 'index'])->name('payouts.index');
    Route::get('/payouts/{payout}', [OwnerPayoutController::class, 'show'])->name('payouts.show');
    Route::get('/payouts/{payout}/receipt', [OwnerPayoutController::class, 'receipt'])->name('payouts.receipt');
});

// NESA AI Chatbot Endpoints
Route::prefix('api/nesa')->middleware(['web', 'throttle:60,1'])->group(function (): void {
    Route::post('/chat', [NesaChatController::class, 'chat'])->name('nesa.chat');
    Route::get('/history', [NesaChatController::class, 'history'])->name('nesa.history');
});
