<?php

namespace App\Enums;

enum LpjValidationStatus: string
{
    case PENDING = 'PENDING';
    case VALID = 'VALID';
    case REVISION = 'REVISION';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Menunggu Validasi',
            self::VALID => 'Valid',
            self::REVISION => 'Perlu Revisi',
        };
    }
}
