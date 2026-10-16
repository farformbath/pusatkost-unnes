<?php

declare(strict_types=1);

namespace App\Domain\User\Enums;

enum UserRole: string
{
    case ADMIN = 'admin';
    case OWNER = 'owner';
    case TENANT = 'tenant';

    public function label(): string
    {
        return match ($this) {
            self::ADMIN => 'Administrator UNNESKost',
            self::OWNER => 'Pemilik Kos (Mitra)',
            self::TENANT => 'Pencari Kos / Mahasiswa',
        };
    }
}
