<?php

declare(strict_types=1);

namespace App\Http\Requests\Listing;

use App\Domain\Listing\DTOs\ListingData;
use App\Domain\Listing\Enums\GenderType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateListingRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'gender_type' => ['required', Rule::enum(GenderType::class)],
            'description' => ['required', 'string'],
            'address' => ['required', 'string'],
            'area' => ['required', 'string', 'max:100'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'rules' => ['nullable', 'string'],
            'facility_ids' => ['nullable', 'array'],
            'facility_ids.*' => ['string', 'exists:facilities,id'],
        ];
    }

    public function toDTO(int $ownerId): ListingData
    {
        $rawFacilityIds = $this->input('facility_ids');
        /** @var list<string> $facilityIds */
        $facilityIds = is_array($rawFacilityIds)
            ? array_values(array_filter($rawFacilityIds, 'is_string'))
            : [];

        $latitude = $this->input('latitude');
        $longitude = $this->input('longitude');

        return new ListingData(
            ownerId: $ownerId,
            name: $this->string('name')->toString(),
            genderType: GenderType::from($this->string('gender_type')->toString()),
            description: $this->string('description')->toString(),
            address: $this->string('address')->toString(),
            area: $this->string('area')->toString(),
            latitude: is_numeric($latitude) ? (float) $latitude : null,
            longitude: is_numeric($longitude) ? (float) $longitude : null,
            rules: $this->filled('rules') ? $this->string('rules')->toString() : null,
            facilityIds: $facilityIds,
        );
    }
}
