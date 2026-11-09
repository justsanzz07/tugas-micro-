<?php

namespace App\Services\Submission;

use App\Enums\CertificateValidationStatus;
use App\Enums\LpjAttachmentType;
use App\Enums\LpjValidationStatus;
use App\Enums\SubmissionStatus;
use App\Models\Finance\BudgetItem;
use App\Models\Finance\BudgetRequest;
use App\Models\Submission\AchievementCertificate;
use App\Models\Submission\Competition;
use App\Models\Submission\LpjAttachment;
use App\Models\Submission\LpjReport;
use App\Models\Submission\Submission;
use App\Models\Submission\SubmissionMember;
use App\Models\User;
use App\Services\Finance\DisbursementService;
use App\Services\Workflow\ApprovalStateMachine;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SubmissionService
{
    public function __construct(
        protected ApprovalStateMachine $stateMachine,
        protected DisbursementService $disbursementService
    ) {}

    /**
     * Memeriksa apakah mahasiswa terkena cekal (blokir) karena ada tunggakan LPJ pada lomba sebelumnya.
     */
    public function checkLpjBlock(string $nim): ?Submission
    {
        return Submission::where('submitter_nim', $nim)
            ->whereIn('status', ['waiting_lpj', 'WAITING_LPJ'])
            ->whereHas('competitionDetail', function ($query) {
                $query->where('end_date', '<', now()->subDays(7));
            })
            ->first();
    }

    /**
     * Membuat pengajuan lomba baru oleh Mahasiswa (beserta tim, dokumen, RAB, dan approval flow).
     */
    public function createSubmission(User $mahasiswa, array $data): Submission
    {
        $nim = $mahasiswa->identifier ?? $mahasiswa->username ?? $mahasiswa->email;

        // 1. Cekal LPJ
        $blockedSubmission = $this->checkLpjBlock($nim);
        if ($blockedSubmission) {
            throw new Exception("Pengajuan diblokir! Anda memiliki tanggungan LPJ pada pengajuan [{$blockedSubmission->code}] yang belum diselesaikan.");
        }

        return DB::connection('sipres_submission')->transaction(function () use ($mahasiswa, $nim, $data) {
            // Level mapping for competitions table enum ('local', 'regional', 'national', 'international')
            $levelMap = [
                'REGIONAL' => 'regional',
                'NASIONAL' => 'national',
                'INTERNASIONAL' => 'international',
                'regional' => 'regional',
                'national' => 'national',
                'nasional' => 'national',
                'international' => 'international',
                'internasional' => 'international',
                'local' => 'local',
            ];
            $dbLevel = $levelMap[strtoupper((string) ($data['level'] ?? ''))] ?? 'national';

            // 2. Buat data kompetisi di tabel competitions
            $competition = Competition::create([
                'uuid' => (string) Str::uuid(),
                'name' => $data['competition_name'],
                'organizer' => $data['organizer'],
                'level' => $dbLevel,
                'category' => $data['category'],
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'],
                'location_type' => 'offline',
                'venue' => $data['location'],
                'website_url' => $data['competition_url'] ?? null,
            ]);

            // Hitung total RAB yang diajukan
            $totalBudget = 0;
            if (! empty($data['budget_items']) && is_array($data['budget_items'])) {
                foreach ($data['budget_items'] as $item) {
                    $totalBudget += ((int) ($item['quantity'] ?? 1)) * ((float) ($item['unit_price'] ?? 0));
                }
            }

            // Simpan file proposal jika ada
            $proposalPath = null;
            if (! empty($data['proposal_file']) && $data['proposal_file'] instanceof UploadedFile) {
                $file = $data['proposal_file'];
                $proposalPath = $file->store('proposals', 'local');
            }

            $hasMembers = ! empty($data['members']) && count(array_filter($data['members'])) > 0;
            $prodiId = $data['prodi_id'] ?? $mahasiswa->prodi_id ?? 'TI';
            $prodiName = $mahasiswa->profile?->study_program_name ?? 'Informatika';

            // 3. Buat data pengajuan utama di tabel submissions
            $submission = Submission::create([
                'uuid' => (string) Str::uuid(),
                'competition_id' => $competition->id,
                'submitter_id' => $mahasiswa->id,
                'submitter_name' => $mahasiswa->name,
                'submitter_nim' => $nim,
                'study_program_id' => $prodiId,
                'study_program_name' => $prodiName,
                'title' => $data['competition_name'],
                'delegation_type' => $hasMembers ? 'team' : 'individual',
                'team_name' => $data['team_name'] ?? ($hasMembers ? "Tim {$mahasiswa->name}" : null),
                'supervisor_id' => null,
                'supervisor_name' => null,
                'supervisor_nidn' => $data['dosen_nidn'] ?? null,
                'status' => 'submitted',
                'total_budget_requested' => $totalBudget,
                'total_budget_approved' => 0.00,
                'proposal_path' => $proposalPath,
                'submission_notes' => $data['description'] ?? null,
            ]);

            // 4. Simpan anggota tim (Ketua adalah mahasiswa pengaju)
            SubmissionMember::create([
                'submission_id' => $submission->id,
                'user_id' => $mahasiswa->id,
                'nim' => $nim,
                'name' => $mahasiswa->name,
                'study_program_name' => $prodiName,
                'role_in_team' => 'leader',
            ]);

            if ($hasMembers) {
                foreach ($data['members'] as $memberNim) {
                    $trimmedNim = trim((string) $memberNim);
                    if ($trimmedNim && $trimmedNim !== $nim) {
                        $memberUser = User::where('identifier', $trimmedNim)
                            ->orWhere('username', $trimmedNim)
                            ->first();

                        SubmissionMember::firstOrCreate([
                            'submission_id' => $submission->id,
                            'nim' => $trimmedNim,
                        ], [
                            'user_id' => $memberUser->id ?? 0,
                            'name' => $memberUser->name ?? "Mahasiswa ({$trimmedNim})",
                            'study_program_name' => $memberUser->profile?->study_program_name ?? $prodiName,
                            'role_in_team' => 'member',
                        ]);
                    }
                }
            }

            // 5. Buat RAB di connection sipres_finance
            $this->storeBudgetRequest($submission, $data['budget_items'] ?? []);

            // 6. Inisialisasi State Machine persetujuan berjenjang
            $this->stateMachine->initiateFlow($submission, $mahasiswa);

            return $submission;
        });
    }

    /**
     * Simpan rincian RAB di database sipres_finance.
     */
    protected function storeBudgetRequest(Submission $submission, array $items): BudgetRequest
    {
        return DB::connection('sipres_finance')->transaction(function () use ($submission, $items) {
            $refId = $submission->uuid ?? (string) $submission->id;

            $total = 0;
            foreach ($items as $item) {
                $qty = (int) ($item['quantity'] ?? 1);
                $unitPrice = (float) ($item['unit_price'] ?? 0);
                $total += ($qty * $unitPrice);
            }

            $budget = BudgetRequest::create([
                'uuid' => (string) Str::uuid(),
                'reference_id' => $refId,
                'total_proposed' => $total,
                'total_approved' => 0.00,
                'status' => 'pending_verification',
            ]);

            $validCategories = ['registration', 'transport', 'accommodation', 'consumption', 'supplies', 'other'];

            foreach ($items as $item) {
                $qty = (int) ($item['quantity'] ?? 1);
                $unitPrice = (float) ($item['unit_price'] ?? 0);
                $subtotal = $qty * $unitPrice;
                $cat = strtolower((string) ($item['category'] ?? 'other'));
                if (! in_array($cat, $validCategories, true)) {
                    $cat = 'other';
                }

                BudgetItem::create([
                    'budget_request_id' => $budget->id,
                    'category' => $cat,
                    'item_name' => $item['item_name'],
                    'quantity' => $qty,
                    'unit' => $item['unit'] ?? 'Paket',
                    'unit_price' => $unitPrice,
                    'total_price' => $subtotal,
                    'notes' => $item['notes'] ?? null,
                ]);
            }

            return $budget;
        });
    }

    /**
     * Pelaporan LPJ dan klaim prestasi oleh Mahasiswa pasca-lomba.
     */
    public function submitLpj(Submission $submission, User $mahasiswa, array $data): LpjReport
    {
        if (! in_array($submission->current_status, [SubmissionStatus::WAITING_LPJ, SubmissionStatus::APPROVED_FINAL])) {
            throw new Exception("Pengajuan dengan status [{$submission->current_status->label()}] belum dapat mengunggah LPJ.");
        }

        return DB::connection('sipres_submission')->transaction(function () use ($submission, $data) {
            // 1. Simpan Laporan LPJ
            $lpj = LpjReport::updateOrCreate(
                ['submission_id' => $submission->id],
                [
                    'report_text' => $data['report_text'] ?? null,
                    'submitted_at' => now(),
                    'validation_status' => LpjValidationStatus::PENDING,
                ]
            );

            // 2. Lampirkan nota atau bukti kegiatan
            if (! empty($data['receipt_files']) && is_array($data['receipt_files'])) {
                foreach ($data['receipt_files'] as $file) {
                    if ($file instanceof UploadedFile) {
                        $path = $file->store("lpj/{$submission->id}/receipts", 'local');
                        LpjAttachment::create([
                            'lpj_report_id' => $lpj->id,
                            'attachment_type' => LpjAttachmentType::NOTA,
                            'file_name' => $file->getClientOriginalName(),
                            'file_path' => $path,
                            'file_size' => $file->getSize(),
                            'mime_type' => $file->getClientMimeType() ?? 'image/jpeg',
                        ]);
                    }
                }
            }

            // 3. Simpan sertifikat prestasi jika ada
            if (! empty($data['certificate_file']) && $data['certificate_file'] instanceof UploadedFile) {
                $certFile = $data['certificate_file'];
                $certPath = $certFile->store("certificates/{$submission->id}", 'local');

                AchievementCertificate::updateOrCreate(
                    ['submission_id' => $submission->id],
                    [
                        'achievement_rank' => $data['achievement_rank'] ?? 'Peserta / Finalis',
                        'certificate_file_path' => $certPath,
                        'validation_status' => CertificateValidationStatus::PENDING,
                    ]
                );
            }

            // 4. Transisi status pengajuan ke REVIEW_LPJ
            $submission->update(['current_status' => SubmissionStatus::REVIEW_LPJ]);

            return $lpj;
        });
    }

    /**
     * Validasi LPJ & Poin SKPI oleh Warek.
     */
    public function validateLpj(
        Submission $submission,
        User $validator,
        bool $isValid,
        ?float $skpiPoints = null,
        ?float $incentiveAmount = null,
        ?string $notes = null
    ): void {
        if (! $validator->isDekan() && ! $validator->isWarek()) {
            throw new Exception('Hanya Dekan Fakultas yang berhak memvalidasi LPJ dan mengesahkan capaian SKPI.');
        }

        DB::connection('sipres_submission')->transaction(function () use ($submission, $validator, $isValid, $skpiPoints, $incentiveAmount, $notes) {
            $lpj = $submission->lpjReport;
            if (! $lpj) {
                throw new Exception('Data LPJ tidak ditemukan untuk pengajuan ini.');
            }

            if ($isValid) {
                $lpj->update([
                    'validation_status' => LpjValidationStatus::VALID,
                    'validated_at' => now(),
                    'validated_by_nidn' => $validator->identifier ?? '0701017503',
                    'validation_notes' => $notes,
                ]);

                // Validasi sertifikat & tetapkan poin SKPI
                $certificate = $submission->certificates()->first();
                if ($certificate) {
                    $certificate->update([
                        'validation_status' => CertificateValidationStatus::VALID,
                        'validated_at' => now(),
                        'validated_by_nidn' => $validator->identifier ?? '0701017503',
                        'skpi_points' => $skpiPoints ?? 2.00,
                    ]);

                    // Jika ada penetapan insentif juara
                    if ($incentiveAmount && $incentiveAmount > 0) {
                        $this->disbursementService->awardIncentive(
                            $submission,
                            $validator,
                            $certificate->achievement_rank ?? 'Juara',
                            $incentiveAmount,
                            $notes
                        );
                    }
                }

                // Status akhir: COMPLETED (Cekal LPJ terbuka)
                $submission->update(['current_status' => SubmissionStatus::COMPLETED]);
            } else {
                $lpj->update([
                    'validation_status' => LpjValidationStatus::REVISION,
                    'validation_notes' => $notes,
                ]);

                $submission->update(['current_status' => SubmissionStatus::WAITING_LPJ]);
            }
        });
    }
}
