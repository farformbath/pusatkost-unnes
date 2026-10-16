<?php

declare(strict_types=1);

namespace App\Domain\Booking\Models;

use App\Domain\Listing\Models\Room;
use App\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoomStatusAudit extends Model
{
    use HasUuids;

    protected $fillable = [
        'room_id',
        'booking_id',
        'user_id',
        'previous_status',
        'new_status',
        'previous_available_count',
        'new_available_count',
        'reason',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'previous_available_count' => 'integer',
        'new_available_count' => 'integer',
    ];

    /**
     * @return BelongsTo<Room, $this>
     */
    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    /**
     * @return BelongsTo<Booking, $this>
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
