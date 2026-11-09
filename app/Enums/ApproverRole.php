<?php

namespace App\Enums;

enum ApproverRole: string
{
    case KAPRODI = 'KAPRODI';
    case DOSEN = 'DOSEN';
    case DEKAN = 'DEKAN';
    case KEUANGAN = 'KEUANGAN';
    case WAREK = 'WAREK';
    case WAREK_II = 'WAREK_II';

    public function label(): string
    {
        return match ($this) {
            self::KAPRODI => 'Ketua Program Studi (Kaprodi)',
            self::DOSEN => 'Dosen Pembimbing',
            self::DEKAN => 'Dekan Fakultas',
            self::KEUANGAN => 'Bagian Keuangan',
            self::WAREK, self::WAREK_II => 'Wakil Rektor II',
        };
    }

    public function stepOrder(): int
    {
        return match ($this) {
            self::KAPRODI, self::DOSEN => 1,
            self::DEKAN => 2,
            self::WAREK => 3,
            self::KEUANGAN => 3,
            self::WAREK_II => 4,
        };
    }
}
