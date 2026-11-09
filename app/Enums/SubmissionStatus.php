<?php

namespace App\Enums;

enum SubmissionStatus: string
{
    case DRAFT = 'DRAFT';
    case SUBMITTED = 'SUBMITTED';
    case REVIEW_KAPRODI = 'REVIEW_KAPRODI';
    case REVISION_KAPRODI = 'REVISION_KAPRODI';
    case APPROVED_KAPRODI = 'APPROVED_KAPRODI';
    case REVIEW_DOSEN = 'REVIEW_DOSEN';
    case REVISION_DOSEN = 'REVISION_DOSEN';
    case APPROVED_DOSEN = 'APPROVED_DOSEN';
    case REVIEW_DEKAN = 'REVIEW_DEKAN';
    case REVISION_DEKAN = 'REVISION_DEKAN';
    case APPROVED_DEKAN = 'APPROVED_DEKAN';
    case REVIEW_WAREK = 'REVIEW_WAREK';
    case REVISION_WAREK = 'REVISION_WAREK';
    case APPROVED_FINAL = 'APPROVED_FINAL';
    case WAITING_LPJ = 'WAITING_LPJ';
    case REVIEW_LPJ = 'REVIEW_LPJ';
    case COMPLETED = 'COMPLETED';
    case REJECTED = 'REJECTED';

    public function label(): string
    {
        return match ($this) {
            self::DRAFT => 'Draft',
            self::SUBMITTED => 'Diajukan',
            self::REVIEW_KAPRODI, self::REVIEW_DOSEN => 'Review Kaprodi',
            self::REVISION_KAPRODI, self::REVISION_DOSEN => 'Revisi dari Kaprodi',
            self::APPROVED_KAPRODI, self::APPROVED_DOSEN => 'Disetujui Kaprodi',
            self::REVIEW_DEKAN => 'Review Dekan',
            self::REVISION_DEKAN => 'Revisi dari Dekan',
            self::APPROVED_DEKAN => 'Disetujui Dekan',
            self::REVIEW_WAREK => 'Review Wakil Rektor',
            self::REVISION_WAREK => 'Revisi dari Wakil Rektor',
            self::APPROVED_FINAL => 'Disetujui Final',
            self::WAITING_LPJ => 'Menunggu LPJ',
            self::REVIEW_LPJ => 'Review LPJ',
            self::COMPLETED => 'Selesai',
            self::REJECTED => 'Ditolak',
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::DRAFT => 'gray',
            self::SUBMITTED, self::REVIEW_KAPRODI, self::REVIEW_DOSEN, self::REVIEW_DEKAN, self::REVIEW_WAREK, self::REVIEW_LPJ => 'blue',
            self::REVISION_KAPRODI, self::REVISION_DOSEN, self::REVISION_DEKAN, self::REVISION_WAREK => 'yellow',
            self::APPROVED_KAPRODI, self::APPROVED_DOSEN, self::APPROVED_DEKAN => 'indigo',
            self::APPROVED_FINAL => 'green',
            self::WAITING_LPJ => 'orange',
            self::COMPLETED => 'emerald',
            self::REJECTED => 'red',
        };
    }

    public function isEditable(): bool
    {
        return in_array($this, [
            self::DRAFT,
            self::REVISION_KAPRODI,
            self::REVISION_DOSEN,
            self::REVISION_DEKAN,
            self::REVISION_WAREK,
        ]);
    }

    public function isTerminal(): bool
    {
        return in_array($this, [self::COMPLETED, self::REJECTED]);
    }
}
