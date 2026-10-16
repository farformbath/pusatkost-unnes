<?php

declare(strict_types=1);

namespace App\Domain\User\Enums;

enum OwnerVerificationStatus: string
{
    case PENDING = 'pending';
    case VERIFIED = 'verified';
    case REJECTED = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Menunggu Verifikasi Admin',
            self::VERIFIED => 'Terverifikasi Resmi',
            self::REJECTED => 'Verifikasi Ditolak',
        };
    }
}
