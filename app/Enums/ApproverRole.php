<?php

namespace App\Enums;

enum ApproverRole: string
{
    case KAPRODI = 'KAPRODI';
    case DOSEN = 'DOSEN';
    case DEKAN = 'DEKAN';
    case WAREK = 'WAREK';

    public function label(): string
    {
        return match ($this) {
            self::KAPRODI => 'Ketua Program Studi (Kaprodi)',
            self::DOSEN => 'Dosen Pembimbing',
            self::DEKAN => 'Dekan Fakultas',
            self::WAREK => 'Wakil Rektor',
        };
    }

    public function stepOrder(): int
    {
        return match ($this) {
            self::KAPRODI, self::DOSEN => 1,
            self::DEKAN => 2,
            self::WAREK => 3,
        };
    }
}
