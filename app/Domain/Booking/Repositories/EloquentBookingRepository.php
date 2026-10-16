<?php

declare(strict_types=1);

namespace App\Domain\Booking\Repositories;

use App\Domain\Booking\Models\Booking;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EloquentBookingRepository implements BookingRepositoryInterface
{
    public function findById(string $id): ?Booking
    {
        return Booking::with(['listing.images', 'room.facilities', 'payments', 'user'])->find($id);
    }

    public function findByCode(string $code): ?Booking
    {
        return Booking::with(['listing.images', 'room.facilities', 'payments', 'user'])->where('booking_code', $code)->first();
    }

    /**
     * @return LengthAwarePaginator<int, Booking>
     */
    public function getForUser(int $userId, int $perPage = 10): LengthAwarePaginator
    {
        return Booking::with(['listing.images', 'room', 'latestPayment'])
            ->where('user_id', $userId)
            ->latest()
            ->paginate($perPage);
    }

    /**
     * @return LengthAwarePaginator<int, Booking>
     */
    public function getForOwner(int $ownerId, ?string $status = null, int $perPage = 15): LengthAwarePaginator
    {
        $query = Booking::with(['listing.images', 'room', 'user', 'latestPayment'])
            ->whereHas('listing', function ($q) use ($ownerId) {
                $q->where('owner_id', $ownerId);
            });

        if ($status !== null && $status !== '') {
            $query->where('status', $status);
        }

        return $query->latest()->paginate($perPage);
    }
}
