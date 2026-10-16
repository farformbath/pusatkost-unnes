<?php

declare(strict_types=1);

namespace App\Domain\Listing\DTOs;

final readonly class RoomData
{
    /**
     * @param  list<string>  $facilityIds
     */
    public function __construct(
        public string $listingId,
        public string $name,
        public ?string $size,
        public int $priceMonthly,
        public int $priceDeposit = 0,
        public int $totalRooms = 1,
        public int $availableRooms = 1,
        public array $facilityIds = [],
    ) {}
}
