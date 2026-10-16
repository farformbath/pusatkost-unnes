<?php

declare(strict_types=1);

namespace App\Http\Controllers\Tenant;

use App\Domain\Booking\Enums\BookingStatus;
use App\Domain\Booking\Enums\PaymentStatus;
use App\Domain\Booking\Models\Booking;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TenantBookingController extends Controller
{
    public function index(Request $request): Response
    {
        /** @var User $user */
        $user = $request->user();

        $status = $request->query('status');

        $query = Booking::query()
            ->with(['listing.primaryImage', 'listing.owner:id,name,phone', 'room'])
            ->where('user_id', $user->id)
            ->latest();

        if ($status === 'pending') {
            $query->where('payment_status', PaymentStatus::PENDING)
                ->where('status', '!=', BookingStatus::CANCELLED);
        } elseif ($status === 'active') {
            $query->where('payment_status', PaymentStatus::PAID)
                ->whereIn('status', [BookingStatus::APPROVED, BookingStatus::PENDING]);
        } elseif ($status === 'completed') {
            $query->where('status', BookingStatus::COMPLETED);
        } elseif ($status === 'cancelled') {
            $query->where(function ($q): void {
                $q->where('status', BookingStatus::CANCELLED)
                    ->orWhere('status', BookingStatus::REJECTED)
                    ->orWhere('payment_status', PaymentStatus::CANCELLED);
            });
        }

        $bookings = $query->paginate(8)->withQueryString();

        $counts = [
            'all' => Booking::query()->where('user_id', $user->id)->count(),
            'pending' => Booking::query()->where('user_id', $user->id)
                ->where('payment_status', PaymentStatus::PENDING)
                ->where('status', '!=', BookingStatus::CANCELLED)
                ->count(),
            'active' => Booking::query()->where('user_id', $user->id)
                ->where('payment_status', PaymentStatus::PAID)
                ->whereIn('status', [BookingStatus::APPROVED, BookingStatus::PENDING])
                ->count(),
            'completed' => Booking::query()->where('user_id', $user->id)
                ->where('status', BookingStatus::COMPLETED)
                ->count(),
            'cancelled' => Booking::query()->where('user_id', $user->id)
                ->where(function ($q): void {
                    $q->where('status', BookingStatus::CANCELLED)
                        ->orWhere('status', BookingStatus::REJECTED)
                        ->orWhere('payment_status', PaymentStatus::CANCELLED);
                })
                ->count(),
        ];

        return Inertia::render('Tenant/Bookings/Index', [
            'bookings' => $bookings,
            'counts' => $counts,
            'current_status' => $status ?? 'all',
        ]);
    }
}
