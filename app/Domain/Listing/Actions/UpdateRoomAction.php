<?php

declare(strict_types=1);

namespace App\Domain\Listing\Actions;

use App\Domain\Listing\DTOs\RoomData;
use App\Domain\Listing\Enums\RoomStatus;
use App\Domain\Listing\Models\Room;
use InvalidArgumentException;

final class UpdateRoomAction
{
    public function handle(Room $room, RoomData $data): Room
    {
        if ($data->priceMonthly < 0) {
            throw new InvalidArgumentException('Harga sewa bulanan tidak boleh bernilai negatif.');
        }

        if ($data->availableRooms > $data->totalRooms) {
            throw new InvalidArgumentException('Jumlah kamar tersedia tidak boleh melebihi total kamar.');
        }

        $status = $data->availableRooms > 0 ? RoomStatus::AVAILABLE : RoomStatus::OCCUPIED;

        $room->update([
            'name' => $data->name,
            'size' => $data->size,
            'price_monthly' => $data->priceMonthly,
            'price_deposit' => $data->priceDeposit,
            'status' => $status,
            'total_rooms' => $data->totalRooms,
            'available_rooms' => $data->availableRooms,
        ]);

        $room->facilities()->sync($data->facilityIds);

        return $room->load('facilities');
    }
}
