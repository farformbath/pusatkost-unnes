<?php

declare(strict_types=1);

namespace App\Domain\Booking\Repositories;

use App\Domain\Booking\Models\Booking;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface BookingRepositoryInterface
{
    public function findById(string $id): ?Booking;

    public function findByCode(string $code): ?Booking;

    /**
     * @return LengthAwarePaginator<int, Booking>
     */
    public function getForUser(int $userId, int $perPage = 10): LengthAwarePaginator;

    /**
     * @return LengthAwarePaginator<int, Booking>
     */
    public function getForOwner(int $ownerId, ?string $status = null, int $perPage = 15): LengthAwarePaginator;
}
