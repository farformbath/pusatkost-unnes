<?php

declare(strict_types=1);

namespace App\Domain\Payment\Enums;

enum PaymentReviewStatus: string
{
    case PENDING = 'pending';
    case CONTACTING = 'contacting';
    case VERIFIED = 'verified';
    case REJECTED = 'rejected';
    case APPROVED = 'approved';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Menunggu Verifikasi Admin',
            self::CONTACTING => 'Sedang Menghubungi Pihak',
            self::VERIFIED => 'Terverifikasi (Siap Payout)',
            self::REJECTED => 'Ditolak / Dibatalkan',
            self::APPROVED => 'Disetujui & Payout Dirilis',
        };
    }

    public function isVerified(): bool
    {
        return $this === self::VERIFIED;
    }

    public function isApproved(): bool
    {
        return $this === self::APPROVED;
    }

    public function isRejected(): bool
    {
        return $this === self::REJECTED;
    }
}
