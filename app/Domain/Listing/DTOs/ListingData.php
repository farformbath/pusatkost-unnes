<?php

declare(strict_types=1);

namespace App\Domain\Listing\DTOs;

use App\Domain\Listing\Enums\GenderType;

final readonly class ListingData
{
    /**
     * @param  list<string>  $facilityIds
     */
    public function __construct(
        public int $ownerId,
        public string $name,
        public GenderType $genderType,
        public string $description,
        public string $address,
        public string $area,
        public ?float $latitude = null,
        public ?float $longitude = null,
        public ?string $rules = null,
        public array $facilityIds = [],
    ) {}
}
