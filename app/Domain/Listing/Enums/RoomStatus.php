<?php

declare(strict_types=1);

namespace App\Domain\Listing\Enums;

enum RoomStatus: string
{
    case AVAILABLE = 'available';
    case OCCUPIED = 'occupied';
    case MAINTENANCE = 'maintenance';

    public function isAvailable(): bool
    {
        return $this === self::AVAILABLE;
    }

    public function label(): string
    {
        return match ($this) {
            self::AVAILABLE => 'Tersedia',
            self::OCCUPIED => 'Penuh',
            self::MAINTENANCE => 'Perbaikan',
        };
    }
}
