<?php

declare(strict_types=1);

namespace App\Domain\Booking\Exceptions;

use RuntimeException;

final class RoomUnavailableException extends RuntimeException
{
    public function __construct(string $message = 'Kamar yang Anda pilih saat ini sudah tidak tersedia atau baru saja terisi penuh.')
    {
        parent::__construct($message);
    }
}
