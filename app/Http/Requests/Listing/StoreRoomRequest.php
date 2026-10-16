<?php

declare(strict_types=1);

namespace App\Http\Requests\Listing;

use App\Domain\Listing\DTOs\RoomData;
use Illuminate\Foundation\Http\FormRequest;

class StoreRoomRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'size' => ['nullable', 'string', 'max:50'],
            'price_monthly' => ['required', 'integer', 'min:0'],
            'price_deposit' => ['nullable', 'integer', 'min:0'],
            'total_rooms' => ['required', 'integer', 'min:1'],
            'available_rooms' => ['required', 'integer', 'min:0', 'lte:total_rooms'],
            'facility_ids' => ['nullable', 'array'],
            'facility_ids.*' => ['string', 'exists:facilities,id'],
        ];
    }

    public function toDTO(string $listingId): RoomData
    {
        $rawFacilityIds = $this->input('facility_ids');
        /** @var list<string> $facilityIds */
        $facilityIds = is_array($rawFacilityIds)
            ? array_values(array_filter($rawFacilityIds, 'is_string'))
            : [];

        $priceMonthly = $this->input('price_monthly');
        $priceDeposit = $this->input('price_deposit');
        $totalRooms = $this->input('total_rooms');
        $availableRooms = $this->input('available_rooms');

        return new RoomData(
            listingId: $listingId,
            name: $this->string('name')->toString(),
            size: $this->filled('size') ? $this->string('size')->toString() : null,
            priceMonthly: is_numeric($priceMonthly) ? (int) $priceMonthly : 0,
            priceDeposit: is_numeric($priceDeposit) ? (int) $priceDeposit : 0,
            totalRooms: is_numeric($totalRooms) ? (int) $totalRooms : 1,
            availableRooms: is_numeric($availableRooms) ? (int) $availableRooms : 1,
            facilityIds: $facilityIds,
        );
    }
}
