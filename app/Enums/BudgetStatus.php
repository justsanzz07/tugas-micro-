<?php

namespace App\Enums;

enum BudgetStatus: string
{
    case DRAFT = 'DRAFT';
    case SUBMITTED = 'SUBMITTED';
    case APPROVED = 'APPROVED';
    case REVISED = 'REVISED';
    case REJECTED = 'REJECTED';

    public function label(): string
    {
        return match ($this) {
            self::DRAFT => 'Draft',
            self::SUBMITTED => 'Diajukan',
            self::APPROVED => 'Disetujui',
            self::REVISED => 'Revisi',
            self::REJECTED => 'Ditolak',
        };
    }
}
