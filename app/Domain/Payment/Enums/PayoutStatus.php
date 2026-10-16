<?php

declare(strict_types=1);

namespace App\Domain\Payment\Enums;

enum PayoutStatus: string
{
    case PENDING = 'pending';
    case PROCESSING = 'processing';
    case PAID = 'paid';
    case FAILED = 'failed';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Menunggu Pencairan',
            self::PROCESSING => 'Sedang Diproses Provider',
            self::PAID => 'Berhasil Dicairkan (Lunas)',
            self::FAILED => 'Gagal Dicairkan',
            self::CANCELLED => 'Dibatalkan',
        };
    }

    public function isPaid(): bool
    {
        return $this === self::PAID;
    }

    public function isPending(): bool
    {
        return $this === self::PENDING;
    }

    public function isProcessing(): bool
    {
        return $this === self::PROCESSING;
    }

    public function isFailed(): bool
    {
        return $this === self::FAILED;
    }
}
