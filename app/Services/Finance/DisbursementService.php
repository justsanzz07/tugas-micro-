<?php

namespace App\Services\Finance;

use App\Enums\DisbursementStatus;
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
     * Dekan menetapkan plafon dana disetujui dan memicu pembuatan tiket pencairan ke SIMPAY.
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
