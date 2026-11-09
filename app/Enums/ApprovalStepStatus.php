<?php

namespace App\Enums;

enum ApprovalStepStatus: string
{
    case PENDING = 'PENDING';
    case APPROVED = 'APPROVED';
    case REVISED = 'REVISED';
    case REJECTED = 'REJECTED';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Menunggu',
            self::APPROVED => 'Disetujui',
            self::REVISED => 'Revisi',
            self::REJECTED => 'Ditolak',
        };
    }
}
