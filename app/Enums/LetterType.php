<?php

namespace App\Enums;

enum LetterType: string
{
    case DISPENSASI = 'DISPENSASI';
    case SURAT_TUGAS = 'SURAT_TUGAS';

    public function label(): string
    {
        return match ($this) {
            self::DISPENSASI => 'Surat Dispensasi',
            self::SURAT_TUGAS => 'Surat Tugas',
        };
    }
}
