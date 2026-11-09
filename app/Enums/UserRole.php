<?php

namespace App\Enums;

enum UserRole: string
{
    case MAHASISWA = 'mahasiswa';
    case DOSEN = 'dosen';
    case DOSEN_PEMBIMBING = 'dosen_pembimbing';
    case DEKAN = 'dekan';
    case WAREK = 'warek';
    case PIMPINAN = 'pimpinan';
    case ORMAWA = 'ormawa';
    case KAPRODI = 'kaprodi';
    case KEMAHASISWAAN = 'kemahasiswaan';
    case KEUANGAN = 'keuangan';
    case WAREK_II = 'warek_ii';
    case ADMIN = 'admin';

    public function label(): string
    {
        return match ($this) {
            self::MAHASISWA => 'Mahasiswa',
            self::DOSEN, self::DOSEN_PEMBIMBING => 'Dosen Pembimbing',
            self::DEKAN => 'Dekan',
            self::WAREK, self::PIMPINAN, self::WAREK_II => 'Wakil Rektor II',
            self::ORMAWA => 'Ormawa',
            self::KAPRODI => 'Kaprodi',
            self::KEMAHASISWAAN => 'Kemahasiswaan',
            self::KEUANGAN => 'Keuangan',
            self::ADMIN => 'Administrator',
        };
    }
}
