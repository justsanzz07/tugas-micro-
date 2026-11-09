<?php

namespace App\Services\Workflow;

use App\Enums\ApprovalAction;
use App\Enums\ApproverRole;
use App\Enums\SubmissionStatus;
use App\Models\Submission\Submission;
use App\Models\User;
use App\Models\Workflow\ApprovalFlow;
use App\Models\Workflow\ApprovalStep;
use App\Services\Finance\DisbursementService;
use App\Services\Letter\LetterGeneratorService;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ApprovalStateMachine
{
    public function __construct(
        protected LetterGeneratorService $letterService,
        protected DisbursementService $disbursementService
    ) {}

    /**
     * Inisialisasi flow approval saat Mahasiswa submit pengajuan pertama kali.
     */
    public function initiateFlow(Submission $submission, User $mahasiswa): ApprovalFlow
    {
        return DB::connection('sipres_workflow')->transaction(function () use ($submission, $mahasiswa) {
            $refId = $submission->uuid ?? (string) Str::uuid();
            if (! $submission->uuid) {
                $submission->update(['uuid' => $refId]);
            }

            $flow = ApprovalFlow::updateOrCreate(
                ['reference_id' => $refId],
                [
                    'uuid' => (string) Str::uuid(),
                    'template_code' => 'STANDARD_COMPETITION',
                    'current_step' => 'kaprodi',
                    'status' => 'in_progress',
                ]
            );

            // Step 1: Kaprodi (Review proposal & penunjukan Dosen Pendamping)
            ApprovalStep::updateOrCreate(
                ['workflow_instance_id' => $flow->id, 'step_order' => 1],
                [
                    'reference_id' => $refId,
                    'step_name' => 'Persetujuan Kaprodi & Penunjukan Dosen',
                    'role_required' => 'kaprodi',
                    'status' => 'pending',
                ]
            );

            // Step 2: Dekan (Persetujuan Final Fakultas, Plafon Dana & Surat Tugas)
            ApprovalStep::updateOrCreate(
                ['workflow_instance_id' => $flow->id, 'step_order' => 2],
                [
                    'reference_id' => $refId,
                    'step_name' => 'Persetujuan Final Dekan Fakultas',
                    'role_required' => 'dekan',
                    'status' => 'pending',
                ]
            );

            // Audit log di sipres_identity
            DB::connection('mysql')->table('audit_logs')->insert([
                'correlation_id' => (string) Str::uuid(),
                'service_name' => 'sipres_workflow',
                'user_id' => $mahasiswa->id ?? null,
                'user_name' => $mahasiswa->name ?? null,
                'user_role' => 'mahasiswa',
                'action' => 'SUBMIT',
                'resource_type' => 'submission',
                'resource_id' => (string) $submission->id,
                'payload_after' => json_encode(['code' => $submission->code]),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Sync status ke sipres_submission
            $submission->update([
                'current_status' => SubmissionStatus::REVIEW_KAPRODI,
                'status' => 'kaprodi_review',
                'submitted_at' => now(),
            ]);

            return $flow;
        });
    }

    /**
     * Proses keputusan approval oleh Approver (Kaprodi, Dekan).
     */
    public function processDecision(
        Submission $submission,
        User $actor,
        ApprovalAction $action,
        ?string $notes = null,
        array $extraData = []
    ): SubmissionStatus {
        $flow = $submission->approvalFlow();
        if (! $flow) {
            throw new Exception('Alur persetujuan (approval flow) tidak ditemukan.');
        }

        $currentStep = $flow->currentStep();
        if (! $currentStep) {
            throw new Exception('Tahap persetujuan aktif tidak ditemukan.');
        }

        // Validasi hak role approver
        $this->validateActorCanApprove($currentStep, $actor, $submission);

        return DB::connection('sipres_workflow')->transaction(function () use ($submission, $flow, $currentStep, $actor, $action, $notes, $extraData) {
            // Catat ke audit_logs
            DB::connection('mysql')->table('audit_logs')->insert([
                'correlation_id' => (string) Str::uuid(),
                'service_name' => 'sipres_workflow',
                'user_id' => $actor->id ?? null,
                'user_name' => $actor->name ?? null,
                'user_role' => $actor->role?->value ?? (string) $actor->role,
                'action' => $action->value,
                'resource_type' => 'submission',
                'resource_id' => (string) $submission->id,
                'payload_after' => json_encode(array_merge($extraData, ['notes' => $notes])),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $newStatus = match ($action) {
                ApprovalAction::APPROVE => $this->handleApprove($submission, $flow, $currentStep, $actor, $notes, $extraData),
                ApprovalAction::REVISE => $this->handleRevise($submission, $flow, $currentStep, $notes),
                ApprovalAction::REJECT => $this->handleReject($submission, $flow, $currentStep, $notes),
                default => throw new Exception("Aksi approval [{$action->value}] tidak valid."),
            };

            return $newStatus;
        });
    }

    /**
     * Resubmit oleh Mahasiswa setelah melakukan perbaikan atas revisi.
     */
    public function resubmit(Submission $submission, User $mahasiswa, ?string $notes = null): SubmissionStatus
    {
        $flow = $submission->approvalFlow();
        if (! $flow) {
            throw new Exception('Alur persetujuan (approval flow) tidak ditemukan.');
        }

        $currentStatus = $submission->current_status;
        $targetRole = match ($currentStatus) {
            SubmissionStatus::REVISION_KAPRODI, SubmissionStatus::REVISION_DOSEN => 'kaprodi',
            SubmissionStatus::REVISION_DEKAN => 'dekan',
            default => 'kaprodi',
        };

        $targetStatus = ($targetRole === 'kaprodi')
            ? SubmissionStatus::REVIEW_KAPRODI
            : SubmissionStatus::REVIEW_DEKAN;

        $targetDbStatus = ($targetRole === 'kaprodi')
            ? 'kaprodi_review'
            : 'dekan_review';

        return DB::connection('sipres_workflow')->transaction(function () use ($submission, $flow, $targetRole, $targetStatus, $targetDbStatus, $mahasiswa, $notes) {
            $step = $flow->steps()
                ->where('role_required', $targetRole)
                ->orWhere('role_required', 'like', "%{$targetRole}%")
                ->first();

            if ($step) {
                $step->update([
                    'status' => 'pending',
                    'acted_at' => null,
                ]);
            }

            // Audit log
            DB::connection('mysql')->table('audit_logs')->insert([
                'correlation_id' => (string) Str::uuid(),
                'service_name' => 'sipres_workflow',
                'user_id' => $mahasiswa->id ?? null,
                'user_name' => $mahasiswa->name ?? null,
                'user_role' => 'mahasiswa',
                'action' => 'RESUBMIT',
                'resource_type' => 'submission',
                'resource_id' => (string) $submission->id,
                'payload_after' => json_encode(['notes' => $notes]),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $flow->update([
                'current_step' => $targetRole,
                'status' => 'in_progress',
            ]);

            $submission->update([
                'current_status' => $targetStatus,
                'status' => $targetDbStatus,
            ]);

            return $targetStatus;
        });
    }

    protected function handleApprove(
        Submission $submission,
        ApprovalFlow $flow,
        ApprovalStep $currentStep,
        User $actor,
        ?string $notes,
        array $extraData
    ): SubmissionStatus {
        $currentStep->update([
            'status' => 'approved',
            'acted_by_user_id' => $actor->id,
            'acted_by_user_name' => $actor->name,
            'acted_at' => now(),
            'decision_notes' => $notes,
        ]);

        $approverRole = $currentStep->approver_role;

        if ($approverRole === ApproverRole::KAPRODI) {
            // KAPRODI APPROVE -> Tetapkan Dosen Pendamping & Maju ke DEKAN
            $flow->steps()->whereIn('role_required', ['dosen_pembimbing', 'dosen', 'kaprodi'])
                ->where('status', 'pending')
                ->update([
                    'status' => 'approved',
                    'acted_by_user_id' => $actor->id,
                    'acted_by_user_name' => $actor->name,
                    'acted_at' => now(),
                    'decision_notes' => 'Disetujui bersama penunjukan dosen pendamping oleh Kaprodi.',
                ]);

            $flow->update([
                'current_step' => 'dekan',
                'status' => 'in_progress',
            ]);

            $updatePayload = [
                'current_status' => SubmissionStatus::REVIEW_DEKAN,
                'status' => 'dekan_review',
            ];

            if (! empty($extraData['supervisor_nidn']) && ! empty($extraData['supervisor_name'])) {
                $updatePayload['supervisor_nidn'] = $extraData['supervisor_nidn'];
                $updatePayload['supervisor_name'] = $extraData['supervisor_name'];
            }

            $submission->update($updatePayload);

            return SubmissionStatus::REVIEW_DEKAN;
        }

        if ($approverRole === ApproverRole::DEKAN) {
            // DEKAN APPROVE FINAL
            $flow->steps()->where('status', 'pending')->update([
                'status' => 'approved',
                'acted_by_user_id' => $actor->id,
                'acted_by_user_name' => $actor->name,
                'acted_at' => now(),
            ]);

            $flow->update([
                'current_step' => 'completed',
                'status' => 'approved',
            ]);

            $approvedAmount = (float) ($extraData['approved_amount'] ?? $submission->total_budget_requested ?? 0);

            // 1. Catat penetapan dana disetujui & tiket pencairan
            $this->disbursementService->approveFunding($submission, $actor, $approvedAmount, $notes);

            // 2. Terbitkan Surat Dispensasi Kuliah Fakultas
            $this->letterService->generateDispensasiLetter($submission, $actor);

            // 3. Terbitkan Surat Tugas Resmi Kegiatan
            $this->letterService->generateSuratTugasLetter($submission, $actor);

            $newStatus = SubmissionStatus::APPROVED_FINAL;
            $submission->update([
                'current_status' => $newStatus,
                'status' => 'approved',
                'total_budget_approved' => $approvedAmount,
            ]);

            return $newStatus;
        }

        throw new Exception('Tahap persetujuan di luar jangkauan.');
    }

    protected function handleRevise(
        Submission $submission,
        ApprovalFlow $flow,
        ApprovalStep $currentStep,
        ?string $notes
    ): SubmissionStatus {
        $currentStep->update([
            'status' => 'revision_requested',
            'acted_by_user_id' => auth()->id(),
            'acted_by_user_name' => auth()->user()?->name,
            'acted_at' => now(),
            'decision_notes' => $notes,
        ]);

        $flow->update([
            'status' => 'revision_requested',
        ]);

        $newStatus = ($currentStep->approver_role === ApproverRole::KAPRODI)
            ? SubmissionStatus::REVISION_KAPRODI
            : SubmissionStatus::REVISION_DEKAN;

        $submission->update([
            'current_status' => $newStatus,
            'status' => 'revision_required',
        ]);

        return $newStatus;
    }

    protected function handleReject(
        Submission $submission,
        ApprovalFlow $flow,
        ApprovalStep $currentStep,
        ?string $notes
    ): SubmissionStatus {
        $currentStep->update([
            'status' => 'rejected',
            'acted_by_user_id' => auth()->id(),
            'acted_by_user_name' => auth()->user()?->name,
            'acted_at' => now(),
            'decision_notes' => $notes,
        ]);

        $flow->update([
            'status' => 'rejected',
        ]);

        $newStatus = SubmissionStatus::REJECTED;
        $submission->update([
            'current_status' => $newStatus,
            'status' => 'rejected',
        ]);

        return $newStatus;
    }

    protected function validateActorCanApprove(ApprovalStep $step, User $actor, Submission $submission): void
    {
        if ($step->approver_role === ApproverRole::KAPRODI) {
            if (! $actor->isKaprodi() && ! $actor->isDosen()) {
                throw new Exception('Hanya Ketua Program Studi (Kaprodi) yang dapat memproses tahap ini.');
            }
            if ($actor->prodi_id && $submission->study_program_id && $actor->prodi_id !== $submission->study_program_id) {
                throw new Exception('Proposal ini bukan berasal dari program studi yang Anda pimpin.');
            }
        } elseif ($step->approver_role === ApproverRole::DEKAN) {
            if (! $actor->isDekan()) {
                throw new Exception('Hanya Dekan Fakultas yang dapat memproses tahap ini.');
            }
            if ($actor->fakultas_id && $submission->fakultas_id && $actor->fakultas_id !== $submission->fakultas_id) {
                throw new Exception('Proposal ini bukan berasal dari fakultas yang Anda pimpin.');
            }
        }
    }
}
