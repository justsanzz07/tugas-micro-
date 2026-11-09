<?php

namespace App\Services\Finance;

use App\Enums\DisbursementStatus;
use App\Enums\SubmissionStatus;
use App\Models\Finance\BudgetRequest;
use App\Models\Finance\DisbursementTicket;
use App\Models\Finance\IncentiveAward;
use App\Models\Submission\Submission;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DisbursementService
{
    /**
     * Dekan menetapkan rekomendasi plafon dana tingkat fakultas.
     */
    public function recordDeanRecommendation(
        Submission $submission,
        User $dekan,
        float $recommendedAmount,
        ?string $notes = null
    ): BudgetRequest {
        return DB::connection('sipres_finance')->transaction(function () use ($submission, $dekan, $recommendedAmount, $notes) {
            $refId = $submission->uuid ?? (string) $submission->id;

            $budgetRequest = BudgetRequest::updateOrCreate(
                ['reference_id' => $refId],
                [
                    'uuid' => (string) Str::uuid(),
                    'total_proposed' => $submission->total_budget_requested ?? $recommendedAmount,
                    'total_approved' => $recommendedAmount,
                    'status' => 'dean_approved',
                    'verified_by_user_id' => $dekan->id,
                    'verified_by_name' => $dekan->name,
                    'verification_notes' => $notes,
                    'verified_at' => now(),
                ]
            );

            return $budgetRequest;
        });
    }

    /**
     * Keuangan mengeksekusi pencairan dana setelah persetujuan Warek II.
     */
    public function disburseFunding(
        Submission $submission,
        User $keuangan,
        float $disbursedAmount,
        string $disbursedAt,
        ?string $transactionRef = null,
        ?string $proofPath = null,
        ?string $notes = null
    ): void {
        DB::connection('sipres_finance')->transaction(function () use ($submission, $keuangan, $disbursedAmount, $disbursedAt, $transactionRef, $proofPath, $notes) {
            $refId = $submission->uuid ?? (string) $submission->id;

            $ticketCode = 'TKT-'.date('Ymd').'-'.str_pad((string) $submission->id, 4, '0', STR_PAD_LEFT);
            DB::connection('sipres_finance')->table('disbursements')->updateOrInsert(
                ['reference_id' => $refId],
                [
                    'uuid' => (string) Str::uuid(),
                    'disbursement_code' => $ticketCode,
                    'stage' => 'stage_1_advance',
                    'amount' => $disbursedAmount,
                    'payment_method' => 'Bank Transfer',
                    'recipient_name' => $submission->submitter_name ?? 'Mahasiswa Pengusul',
                    'transaction_reference' => $transactionRef,
                    'proof_path' => $proofPath,
                    'disbursed_at' => $disbursedAt,
                    'disbursed_by_user_id' => $keuangan->id,
                    'disbursed_by_name' => $keuangan->name,
                    'status' => 'disbursed',
                    'notes' => $notes,
                    'updated_at' => now(),
                ]
            );

            $submission->update([
                'current_status' => SubmissionStatus::WAITING_LPJ,
                'status' => 'waiting_lpj',
            ]);
        });
    }

    /**
     * Warek II / Dekan menetapkan plafon dana disetujui dan memicu pembuatan tiket pencairan.
     */
    public function approveFunding(
        Submission $submission,
        User $dekan,
        float $approvedAmount,
        ?string $notes = null
    ): BudgetRequest {
        return DB::connection('sipres_finance')->transaction(function () use ($submission, $dekan, $approvedAmount, $notes) {
            $refId = $submission->uuid ?? (string) $submission->id;

            $budgetRequest = BudgetRequest::updateOrCreate(
                ['reference_id' => $refId],
                [
                    'uuid' => (string) Str::uuid(),
                    'total_proposed' => $submission->total_budget_requested ?? $approvedAmount,
                    'total_approved' => $approvedAmount,
                    'status' => 'approved',
                    'verified_by_user_id' => $dekan->id,
                    'verified_by_name' => $dekan->name,
                    'verification_notes' => $notes,
                    'verified_at' => now(),
                ]
            );

            // Buat record pencairan dana di sipres_finance.disbursements
            $ticketCode = 'TKT-'.date('Ymd').'-'.str_pad((string) $submission->id, 4, '0', STR_PAD_LEFT);
            DB::connection('sipres_finance')->table('disbursements')->updateOrInsert(
                ['reference_id' => $refId],
                [
                    'uuid' => (string) Str::uuid(),
                    'disbursement_code' => $ticketCode,
                    'stage' => 'stage_1_advance',
                    'amount' => $approvedAmount,
                    'payment_method' => 'Bank Transfer',
                    'recipient_name' => $submission->submitter_name ?? 'Mahasiswa Pengusul',
                    'processed_by_user_id' => $dekan->id,
                    'processed_by_name' => $dekan->name,
                    'status' => 'pending',
                    'notes' => $notes,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );

            return $budgetRequest;
        });
    }

    /**
     * Penetapan insentif juara oleh Warek setelah LPJ dan sertifikat divalidasi.
     */
    public function awardIncentive(
        Submission $submission,
        User $warek,
        string $achievementRank,
        float $incentiveAmount,
        ?string $notes = null
    ): IncentiveAward {
        return DB::connection('sipres_finance')->transaction(function () use ($submission, $warek, $achievementRank, $incentiveAmount, $notes) {
            $award = IncentiveAward::updateOrCreate(
                ['submission_id' => $submission->id],
                [
                    'achievement_rank' => $achievementRank,
                    'incentive_amount' => $incentiveAmount,
                    'decided_by_nidn' => $warek->identifier ?? '0701017503',
                    'decided_at' => now(),
                    'notes' => $notes,
                ]
            );

            return $award;
        });
    }

    /**
     * Simulasi sinkronisasi update status dari SIMPAY.
     */
    public function updateSimpayStatus(string $ticketCode, DisbursementStatus $status, ?string $simpayRef = null): bool
    {
        $ticket = DisbursementTicket::where('ticket_code', $ticketCode)->first();
        if (! $ticket) {
            throw new Exception("Tiket pencairan [{$ticketCode}] tidak ditemukan.");
        }

        $data = ['simpay_status' => $status];
        if ($simpayRef) {
            $data['simpay_reference'] = $simpayRef;
        }
        if ($status === DisbursementStatus::DISBURSED) {
            $data['disbursed_at'] = now();
        }

        return $ticket->update($data);
    }
}
