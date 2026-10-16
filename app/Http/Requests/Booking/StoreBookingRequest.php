<?php

declare(strict_types=1);

namespace App\Http\Requests\Booking;

use App\Domain\Booking\DTOs\BookingData;
use Illuminate\Foundation\Http\FormRequest;

class StoreBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'listing_id' => ['required', 'string', 'exists:listings,id'],
            'room_id' => ['required', 'string', 'exists:rooms,id'],
            'tenant_name' => ['required', 'string', 'max:100'],
            'tenant_phone' => ['required', 'string', 'max:30'],
            'start_date' => ['required', 'date', 'after_or_equal:today'],
            'duration_months' => ['required', 'integer', 'min:1', 'max:12'],
            'notes' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function toDTO(int $userId): BookingData
    {
        $listingId = $this->input('listing_id');
        $roomId = $this->input('room_id');
        $tenantName = $this->input('tenant_name');
        $tenantPhone = $this->input('tenant_phone');
        $startDate = $this->input('start_date');
        $durationMonths = $this->integer('duration_months', 1);
        $notes = $this->input('notes');

        return new BookingData(
            userId: $userId,
            listingId: is_string($listingId) ? $listingId : '',
            roomId: is_string($roomId) ? $roomId : '',
            tenantName: is_string($tenantName) ? $tenantName : '',
            tenantPhone: is_string($tenantPhone) ? $tenantPhone : '',
            startDate: is_string($startDate) ? $startDate : '',
            durationMonths: $durationMonths,
            notes: is_string($notes) ? $notes : null,
        );
    }
}
