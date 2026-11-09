<?php

namespace App\Policies;

use App\Enums\SubmissionStatus;
use App\Models\Submission\Submission;
use App\Models\User;

class SubmissionPolicy
{
    /**
     * Tentukan apakah user dapat melihat detail pengajuan.
     */
    public function view(User $user, Submission $submission): bool
    {
        if ($user->isAdmin() || $user->isWarek() || $user->isWarekII() || $user->isKeuangan()) {
            return true;
        }

        if ($user->isDekan()) {
            return ! $user->fakultas_id || $submission->fakultas_id === $user->fakultas_id;
        }

        if ($user->isKaprodi()) {
            return ! $user->prodi_id || $submission->study_program_id === $user->prodi_id || ! $submission->study_program_id;
        }

        if ($user->isDosen()) {
            return $user->identifier && $submission->dosen_nidn === $user->identifier;
        }

        if ($user->isMahasiswa()) {
            $nim = $user->identifier ?? $user->email;
            if ($submission->mahasiswa_nim === $nim) {
                return true;
            }

            return $submission->members()->where('nim', $nim)->exists();
        }

        return false;
    }

    /**
     * Tentukan apakah user dapat membuat pengajuan baru.
     */
    public function create(User $user): bool
    {
        return $user->isMahasiswa();
    }

    /**
     * Tentukan apakah user dapat mengedit pengajuan (hanya saat DRAFT atau REVISI).
     */
    public function update(User $user, Submission $submission): bool
    {
        if (! $user->isMahasiswa()) {
            return false;
        }

        $nim = $user->identifier ?? $user->email;
        if ($submission->mahasiswa_nim !== $nim) {
            return false;
        }

        return $submission->isEditable();
    }

    /**
     * Tentukan apakah user berwenang melakukan approval/revisi/penolakan.
     */
    public function approve(User $user, Submission $submission): bool
    {
        $status = $submission->current_status;

        if ($user->isKaprodi() || $user->isDosen()) {
            return in_array($status, [SubmissionStatus::REVIEW_KAPRODI, SubmissionStatus::REVIEW_DOSEN, SubmissionStatus::SUBMITTED], true)
                && (! $user->prodi_id || $submission->study_program_id === $user->prodi_id || ! $submission->study_program_id);
        }

        if ($user->isDekan()) {
            return $status === SubmissionStatus::REVIEW_DEKAN
                && (! $user->fakultas_id || $submission->fakultas_id === $user->fakultas_id);
        }

        if ($user->isKeuangan()) {
            return in_array($status, [SubmissionStatus::REVIEW_KEUANGAN, SubmissionStatus::APPROVED_DEKAN], true);
        }

        if ($user->isWarek() || $user->isWarekII()) {
            return in_array($status, [SubmissionStatus::REVIEW_WAREK, SubmissionStatus::REVIEW_WAREK_II, SubmissionStatus::APPROVED_KEUANGAN], true);
        }

        return false;
    }

    /**
     * Tentukan apakah user dapat mengunggah LPJ pasca-lomba.
     */
    public function submitLpj(User $user, Submission $submission): bool
    {
        if (! $user->isMahasiswa()) {
            return false;
        }

        $nim = $user->identifier ?? $user->email;
        if ($submission->mahasiswa_nim !== $nim) {
            return false;
        }

        return in_array($submission->current_status, [
            SubmissionStatus::WAITING_LPJ,
            SubmissionStatus::APPROVED_FINAL,
        ]);
    }

    /**
     * Tentukan apakah user dapat memvalidasi LPJ & menetapkan poin SKPI serta insentif.
     */
    public function validateLpj(User $user, Submission $submission): bool
    {
        return ($user->isDekan() || $user->isWarek() || $user->isWarekII() || $user->isKeuangan()) && in_array($submission->current_status, [SubmissionStatus::REVIEW_LPJ, SubmissionStatus::WAITING_LPJ], true);
    }
}
