<?php

namespace App\Enums;

enum ApprovalAction: string
{
    case SUBMIT = 'SUBMIT';
    case APPROVE = 'APPROVE';
    case REVISE = 'REVISE';
    case REJECT = 'REJECT';
    case RESUBMIT = 'RESUBMIT';

    public function label(): string
    {
        return match ($this) {
            self::SUBMIT => 'Diajukan',
            self::APPROVE => 'Disetujui',
            self::REVISE => 'Diminta Revisi',
            self::REJECT => 'Ditolak',
            self::RESUBMIT => 'Diajukan Ulang',
        };
    }
}
