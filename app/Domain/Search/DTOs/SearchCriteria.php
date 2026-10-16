<?php

declare(strict_types=1);

namespace App\Domain\Search\DTOs;

use App\Domain\Listing\Enums\GenderType;

final readonly class SearchCriteria
{
    /**
     * @param  list<string>  $facilityIds
     * @param  list<string>  $areas
     */
    public function __construct(
        public ?string $keyword = null,
        public ?GenderType $gender = null,
        public ?string $area = null,
        public array $areas = [],
        public ?int $minPrice = null,
        public ?int $maxPrice = null,
        public array $facilityIds = [],
        public string $sort = 'relevance',
        public int $page = 1,
        public int $perPage = 12,
        public ?float $latitude = null,
        public ?float $longitude = null,
        public ?float $radius = null,
    ) {}

    /**
     * @param  array{keyword?: string|null, gender?: string|null, area?: string|list<string>|null, areas?: list<string>|string|null, min_price?: int|string|null, max_price?: int|string|null, facility_ids?: list<string>|string|null, sort?: string|null, page?: int|string|null, per_page?: int|string|null, latitude?: float|string|null, longitude?: float|string|null, radius?: float|string|null}  $data
     */
    public static function fromArray(array $data): self
    {
        $gender = null;
        if (isset($data['gender']) && $data['gender'] !== '') {
            $gender = GenderType::tryFrom($data['gender']);
        }

        $facilityIds = [];
        if (isset($data['facility_ids']) && $data['facility_ids'] !== '') {
            if (is_array($data['facility_ids'])) {
                /** @var list<string> $facilityIds */
                $facilityIds = array_values(array_filter($data['facility_ids'], 'is_string'));
            } else {
                $facilityIds = [$data['facility_ids']];
            }
        }

        $minPrice = isset($data['min_price']) && is_numeric($data['min_price'])
            ? (int) $data['min_price']
            : null;

        $maxPrice = isset($data['max_price']) && is_numeric($data['max_price'])
            ? (int) $data['max_price']
            : null;

        $page = isset($data['page']) && is_numeric($data['page']) && (int) $data['page'] > 0
            ? (int) $data['page']
            : 1;

        $perPage = 12;
        if (isset($data['per_page']) && in_array((int) $data['per_page'], [12, 24, 48], true)) {
            $perPage = (int) $data['per_page'];
        }

        $keyword = isset($data['keyword']) && $data['keyword'] !== ''
            ? trim($data['keyword'])
            : null;

        /** @var list<string> $areas */
        $areas = [];
        if (isset($data['areas']) && $data['areas'] !== '') {
            if (is_array($data['areas'])) {
                $areas = array_values(array_filter(array_map('trim', $data['areas']), fn (string $a) => $a !== ''));
            } elseif (is_string($data['areas'])) {
                $areas = array_values(array_filter(array_map('trim', explode(',', $data['areas'])), fn (string $a) => $a !== ''));
            }
        }

        $area = null;
        if (isset($data['area']) && $data['area'] !== '') {
            if (is_array($data['area'])) {
                $parsed = array_values(array_filter(array_map('trim', $data['area']), fn (string $a) => $a !== ''));
                if (empty($areas)) {
                    $areas = $parsed;
                }
                $area = implode(',', $areas);
            } elseif (is_string($data['area'])) {
                $area = trim($data['area']);
                if (str_contains($area, ',')) {
                    $parsed = array_values(array_filter(array_map('trim', explode(',', $area)), fn (string $a) => $a !== ''));
                    if (empty($areas)) {
                        $areas = $parsed;
                    }
                } elseif (empty($areas)) {
                    $areas = [$area];
                }
            }
        } elseif (! empty($areas)) {
            $area = implode(',', $areas);
        }

        $sort = isset($data['sort']) && $data['sort'] !== ''
            ? $data['sort']
            : 'relevance';

        $latitude = isset($data['latitude']) && is_numeric($data['latitude'])
            ? (float) $data['latitude']
            : null;

        $longitude = isset($data['longitude']) && is_numeric($data['longitude'])
            ? (float) $data['longitude']
            : null;

        $radius = isset($data['radius']) && is_numeric($data['radius'])
            ? (float) $data['radius']
            : null;

        return new self(
            keyword: $keyword,
            gender: $gender,
            area: $area,
            areas: $areas,
            minPrice: $minPrice,
            maxPrice: $maxPrice,
            facilityIds: $facilityIds,
            sort: $sort,
            page: $page,
            perPage: $perPage,
            latitude: $latitude,
            longitude: $longitude,
            radius: $radius,
        );
    }
}
