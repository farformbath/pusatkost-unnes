<?php

declare(strict_types=1);

namespace App\Domain\Listing\Enums;

enum GenderType: string
{
    case PUTRA = 'putra';
    case PUTRI = 'putri';
    case CAMPUR = 'campur';

    public function label(): string
    {
        return match ($this) {
            self::PUTRA => 'Putra',
            self::PUTRI => 'Putri',
            self::CAMPUR => 'Campur',
        };
    }
}
