<?php

namespace App\Services\Letter;

use App\Models\Submission\OfficialLetter;
use App\Models\Submission\Submission;
use App\Models\User;
use Illuminate\Support\Str;

class LetterGeneratorService
{
    /**
     * Terbitkan Surat Dispensasi Kuliah oleh Dekan.
     */
    public function generateDispensasiLetter(Submission $submission, User $dekan): OfficialLetter
    {
        $year = now()->format('Y');
        $monthRomawi = $this->getRomawiMonth((int) now()->format('n'));
        $count = OfficialLetter::where('subject', 'like', '%Dispensasi%')
            ->whereYear('issued_date', now()->year)
            ->count() + 1;

        $fakultasCode = $submission->fakultas_id ?? 'FTI';
        $letterNumber = sprintf('%03d/UNUJA/D/%s/%s/%d', $count, $fakultasCode, $monthRomawi, $year);
        $qrToken = 'DISP-'.Str::upper(Str::random(16));
        $refId = $submission->uuid ?? (string) $submission->id;

        return OfficialLetter::updateOrCreate(
            [
                'reference_id' => $refId,
                'subject' => 'Surat Dispensasi Kuliah',
            ],
            [
                'uuid' => (string) Str::uuid(),
                'letter_number' => $letterNumber,
                'signed_by_name' => $dekan->name,
                'signed_by_nip' => $dekan->identifier ?? '197803102005011002',
                'signed_by_position' => 'Dekan Fakultas',
                'issued_date' => now()->toDateString(),
                'pdf_path' => "letters/dispensasi/{$submission->id}.pdf",
                'verification_token' => $qrToken,
            ]
        );
    }

    /**
     * Terbitkan Surat Tugas Resmi Kegiatan oleh Dekan Fakultas.
     */
    public function generateSuratTugasLetter(Submission $submission, User $dekan): OfficialLetter
    {
        $year = now()->format('Y');
        $monthRomawi = $this->getRomawiMonth((int) now()->format('n'));
        $count = OfficialLetter::where('subject', 'like', '%Tugas%')
            ->whereYear('issued_date', now()->year)
            ->count() + 1;

        $fakultasCode = $submission->fakultas_id ?? 'FTI';
        $letterNumber = sprintf('%03d/UNUJA/%s/ST/%s/%d', $count, $fakultasCode, $monthRomawi, $year);
        $qrToken = 'STUGAS-'.Str::upper(Str::random(16));
        $refId = $submission->uuid ?? (string) $submission->id;

        return OfficialLetter::updateOrCreate(
            [
                'reference_id' => $refId,
                'subject' => 'Surat Tugas Delegasi Lomba',
            ],
            [
                'uuid' => (string) Str::uuid(),
                'letter_number' => $letterNumber,
                'signed_by_name' => $dekan->name,
                'signed_by_nip' => $dekan->identifier ?? '197803102005011002',
                'signed_by_position' => 'Dekan Fakultas',
                'issued_date' => now()->toDateString(),
                'pdf_path' => "letters/surat-tugas/{$submission->id}.pdf",
                'verification_token' => $qrToken,
            ]
        );
    }

    protected function getRomawiMonth(int $month): string
    {
        $map = [
            1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI',
            7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII',
        ];

        return $map[$month] ?? 'I';
    }
}
