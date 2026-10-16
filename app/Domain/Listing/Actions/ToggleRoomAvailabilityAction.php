<?php

declare(strict_types=1);

namespace App\Domain\Listing\Actions;

use App\Domain\Listing\Enums\RoomStatus;
use App\Domain\Listing\Models\Room;

final class ToggleRoomAvailabilityAction
{
    public function handle(Room $room): Room
    {
        if ($room->status === RoomStatus::AVAILABLE) {
            $room->update([
                'status' => RoomStatus::OCCUPIED,
                'available_rooms' => 0,
            ]);
        } else {
            $room->update([
                'status' => RoomStatus::AVAILABLE,
                'available_rooms' => $room->total_rooms > 0 ? $room->total_rooms : 1,
            ]);
        }

        return $room;
    }
}
