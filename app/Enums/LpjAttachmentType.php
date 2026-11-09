<?php

namespace App\Enums;

enum LpjAttachmentType: string
{
    case NOTA = 'NOTA';
    case BUKTI_KEGIATAN = 'BUKTI_KEGIATAN';
    case LAINNYA = 'LAINNYA';

    public function label(): string
    {
        return match ($this) {
            self::NOTA => 'Nota/Kwitansi',
            self::BUKTI_KEGIATAN => 'Bukti Kegiatan',
            self::LAINNYA => 'Lainnya',
        };
    }
}
