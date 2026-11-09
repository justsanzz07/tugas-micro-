<?php

namespace App\Enums;

enum DisbursementStatus: string
{
    case PENDING = 'PENDING';
    case PROCESSING = 'PROCESSING';
    case DISBURSED = 'DISBURSED';
    case FAILED = 'FAILED';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Menunggu',
            self::PROCESSING => 'Diproses',
            self::DISBURSED => 'Dicairkan',
            self::FAILED => 'Gagal',
        };
    }
}
