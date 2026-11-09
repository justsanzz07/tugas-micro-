<?php

namespace App\Enums;

enum CertificateValidationStatus: string
{
    case PENDING = 'PENDING';
    case VALID = 'VALID';
    case REJECTED = 'REJECTED';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Menunggu Validasi',
            self::VALID => 'Valid',
            self::REJECTED => 'Ditolak',
        };
    }
}
