<?php

namespace App\Enums;

enum DocumentType: string
{
    case PROPOSAL = 'PROPOSAL';
    case SUPPORTING = 'SUPPORTING';

    public function label(): string
    {
        return match ($this) {
            self::PROPOSAL => 'Proposal',
            self::SUPPORTING => 'Dokumen Pendukung',
        };
    }
}
