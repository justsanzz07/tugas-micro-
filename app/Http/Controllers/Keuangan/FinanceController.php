<?php

namespace App\Http\Controllers\Keuangan;

use App\Enums\ApprovalAction;
use App\Http\Controllers\Controller;
use App\Models\Submission\Submission;
use App\Services\Finance\DisbursementService;
use App\Services\Workflow\ApprovalStateMachine;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class FinanceController extends Controller
{
    public function __construct(
        protected ApprovalStateMachine $stateMachine,
        protected DisbursementService $disbursementService
    ) {}

    public function dashboard(Request $request)
    {
        // 1. Antrean Verifikasi Anggaran (Proposal disetujui Dekan, menunggu Keuangan)
        $verificationQueue = Submission::with(['competitionDetail', 'members'])
            ->whereIn('status', ['keuangan_review', 'review_keuangan'])
            ->latest('id')
            ->get();

        // 2. Antrean Persetujuan Warek II (Lolos Keuangan, menunggu Warek II)
        $warekQueue = Submission::with(['competitionDetail', 'members'])
            ->whereIn('status', ['warek_review', 'review_warek', 'review_warek_ii'])
            ->latest('id')
            ->get();

        // 3. Antrean Pencairan Dana (Disetujui Final oleh Warek II, menunggu pencairan bank)
        $disbursementQueue = Submission::with(['competitionDetail', 'members'])
            ->whereIn('status', ['approved', 'approved_final'])
            ->latest('id')
            ->get();

        // 4. Antrean Verifikasi LPJ Keuangan (Mahasiswa unggah LPJ beserta bukti transaksi)
        $lpjQueue = Submission::with(['competitionDetail', 'members', 'lpjReport'])
            ->whereIn('status', ['waiting_lpj', 'lpj_review', 'review_lpj'])
            ->whereHas('lpjReport')
            ->latest('id')
            ->get();

        // 5. Metrik 4 Kartu Statistik Keuangan
        $totalPendingVerification = $verificationQueue->count();
        $totalPendingWarek = $warekQueue->count();
        $totalPendingDisbursement = $disbursementQueue->count();

        // Hitung total dana yang telah dicairkan dari tabel sipres_finance.disbursements
        $totalDisbursed = (float) DB::connection('sipres_finance')
            ->table('disbursements')
            ->where('status', 'disbursed')
            ->sum('amount');

        // Total anggaran disetujui secara keseluruhan
        $totalApprovedBudget = (float) Submission::whereIn('status', ['approved', 'approved_final', 'waiting_lpj', 'lpj_review', 'completed'])
            ->sum('total_budget_approved');

        return view('keuangan.dashboard', compact(
            'verificationQueue',
            'warekQueue',
            'disbursementQueue',
            'lpjQueue',
            'totalPendingVerification',
            'totalPendingWarek',
            'totalPendingDisbursement',
            'totalDisbursed',
            'totalApprovedBudget'
        ));
    }

    public function show(Submission $submission)
    {
        Gate::authorize('view', $submission);

        $submission->load([
            'competitionDetail',
            'members',
            'officialLetters',
        ]);

        $flow = $submission->approvalFlow();
        $budget = $submission->budgetRequest();
        if ($budget) {
            $budget->loadMissing('items');
        }

        return view('keuangan.verify', compact('submission', 'flow', 'budget'));
    }

    public function verifyBudget(Request $request, Submission $submission)
    {
        if ($request->user()->isAdmin()) {
            return back()->with('error', 'Administrator hanya memiliki hak akses peninjauan (read-only).');
        }

        Gate::authorize('approve', $submission);

        $validated = $request->validate([
            'action' => ['required', 'in:APPROVE,REVISE,REJECT'],
            'notes' => ['nullable', 'string'],
        ]);

        $action = ApprovalAction::from($validated['action']);

        try {
            $this->stateMachine->processDecision(
                $submission,
                $request->user(),
                $action,
                $validated['notes'] ?? null,
                ['verified_by' => 'keuangan']
            );

            $msg = match ($action) {
                ApprovalAction::APPROVE => "Verifikasi Anggaran [{$submission->code}] berhasil disetujui. Pengajuan diteruskan ke Wakil Rektor II untuk penetapan dana final.",
                ApprovalAction::REVISE => "Pengajuan [{$submission->code}] dikembalikan ke Mahasiswa untuk perbaikan rincian dokumen anggaran.",
                ApprovalAction::REJECT => "Pengajuan [{$submission->code}] telah ditolak oleh Bagian Keuangan.",
                default => 'Hasil verifikasi anggaran berhasil disimpan.',
            };

            return redirect()->route('keuangan.dashboard')->with('success', $msg);
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function disburse(Request $request, Submission $submission)
    {
        if ($request->user()->isAdmin()) {
            return back()->with('error', 'Administrator hanya memiliki hak akses peninjauan (read-only).');
        }

        $validated = $request->validate([
            'disbursed_amount' => ['required', 'numeric', 'min:1'],
            'disbursed_at' => ['required', 'date'],
            'transaction_reference' => ['nullable', 'string', 'max:100'],
            'proof_file' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $proofPath = null;
        if ($request->hasFile('proof_file')) {
            $proofPath = $request->file('proof_file')->store("disbursements/{$submission->id}", 'public');
        }

        try {
            $this->disbursementService->disburseFunding(
                $submission,
                $request->user(),
                (float) $validated['disbursed_amount'],
                $validated['disbursed_at'],
                $validated['transaction_reference'] ?? null,
                $proofPath,
                $validated['notes'] ?? null
            );

            // Audit log
            DB::connection('mysql')->table('audit_logs')->insert([
                'correlation_id' => (string) Str::uuid(),
                'service_name' => 'sipres_finance',
                'user_id' => $request->user()->id,
                'user_name' => $request->user()->name,
                'user_role' => 'keuangan',
                'action' => 'DISBURSE',
                'resource_type' => 'disbursement',
                'resource_id' => (string) $submission->id,
                'payload_after' => json_encode([
                    'amount' => $validated['disbursed_amount'],
                    'transaction_ref' => $validated['transaction_reference'] ?? null,
                    'proof_path' => $proofPath,
                ]),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return redirect()->route('keuangan.dashboard')
                ->with('success', 'Pencairan dana sebesar Rp '.number_format($validated['disbursed_amount'], 0, ',', '.')." untuk [{$submission->code}] berhasil diproses!");
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function lpjShow(Submission $submission)
    {
        Gate::authorize('view', $submission);

        $submission->load([
            'competitionDetail',
            'members',
            'lpjReport.attachments',
            'certificates',
        ]);

        $budget = $submission->budgetRequest();
        $disbursement = DB::connection('sipres_finance')->table('disbursements')
            ->where('reference_id', $submission->uuid)
            ->orWhere('reference_id', (string) $submission->id)
            ->first();

        return view('keuangan.lpj-verify', compact('submission', 'budget', 'disbursement'));
    }

    public function verifyLpj(Request $request, Submission $submission)
    {
        if ($request->user()->isAdmin()) {
            return back()->with('error', 'Administrator hanya memiliki hak akses peninjauan (read-only).');
        }

        $validated = $request->validate([
            'status' => ['required', 'in:verified,revision,rejected'],
            'actual_spent' => ['nullable', 'numeric', 'min:0'],
            'refund_amount' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        try {
            $lpj = $submission->lpjReport;
            if ($lpj) {
                $statusMap = [
                    'verified' => 'approved',
                    'revision' => 'revision_required',
                    'rejected' => 'rejected',
                ];

                $lpj->update([
                    'status' => $statusMap[$validated['status']],
                    'verified_at' => now(),
                    'verification_notes' => $validated['notes'] ?? null,
                ]);
            }

            // Audit log
            DB::connection('mysql')->table('audit_logs')->insert([
                'correlation_id' => (string) Str::uuid(),
                'service_name' => 'sipres_finance',
                'user_id' => $request->user()->id,
                'user_name' => $request->user()->name,
                'user_role' => 'keuangan',
                'action' => 'VERIFY_LPJ',
                'resource_type' => 'lpj_report',
                'resource_id' => (string) ($lpj->id ?? $submission->id),
                'payload_after' => json_encode($validated),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return redirect()->route('keuangan.dashboard')
                ->with('success', "Pemeriksaan LPJ Keuangan [{$submission->code}] berhasil disimpan.");
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function reports(Request $request)
    {
        $year = $request->query('year', date('Y'));
        $faculty = $request->query('faculty');
        $status = $request->query('status');

        $query = Submission::with(['competitionDetail'])
            ->whereNotNull('total_budget_approved')
            ->where('total_budget_approved', '>', 0);

        if ($faculty) {
            $this->applyFacultyFilter($query, $faculty);
        }

        if ($status === 'disbursed') {
            $query->whereIn('status', ['waiting_lpj', 'lpj_review', 'completed']);
        } elseif ($status === 'pending') {
            $query->whereIn('status', ['approved', 'approved_final']);
        }

        $submissions = $query->latest('id')->paginate(15)->withQueryString();

        // Rekapitulasi per fakultas
        $faculties = ['FTI' => 'Fakultas Teknologi Informasi', 'FAI' => 'Fakultas Agama Islam', 'FEB' => 'Fakultas Ekonomi & Bisnis', 'FKES' => 'Fakultas Kesehatan'];
        $facultyStats = [];

        foreach ($faculties as $code => $name) {
            $fQuery = Submission::query();
            $this->applyFacultyFilter($fQuery, $code);
            $approvedSum = (float) $fQuery->sum('total_budget_approved');

            $disbursedSum = (float) DB::connection('sipres_finance')
                ->table('disbursements')
                ->where('status', 'disbursed')
                ->sum('amount');

            $facultyStats[$code] = [
                'name' => $name,
                'approved' => $approvedSum,
                'disbursed' => $disbursedSum,
                'remaining' => max(0, $approvedSum - $disbursedSum),
                'percentage' => $approvedSum > 0 ? round(($disbursedSum / $approvedSum) * 100, 1) : 0,
            ];
        }

        return view('keuangan.reports', compact('submissions', 'facultyStats', 'year', 'faculty', 'status'));
    }

    protected function applyFacultyFilter($query, ?string $faculty): void
    {
        if (! $faculty) {
            return;
        }

        $prodiMap = [
            'FTI' => ['TI', 'TE', 'SI', 'Teknologi Informasi', 'Teknik Elektro', 'Sistem Informasi'],
            'FAI' => ['PAI', 'PBA', 'MPI', 'HES', 'Pendidikan Agama Islam'],
            'FEB' => ['MNJ', 'AKT', 'Manajemen', 'Akuntansi'],
            'FKES' => ['KPR', 'KBD', 'Keperawatan', 'Kebidanan'],
        ];

        $prodis = $prodiMap[$faculty] ?? [$faculty];

        $query->where(function ($q) use ($faculty, $prodis) {
            $hasFakultasCol = false;
            try {
                $hasFakultasCol = Schema::connection($q->getModel()->getConnectionName())
                    ->hasColumn($q->getModel()->getTable(), 'fakultas_id');
            } catch (\Throwable) {
                // Ignore
            }

            if ($hasFakultasCol) {
                $q->where('fakultas_id', $faculty)
                    ->orWhereIn('study_program_id', $prodis);
            } else {
                $q->whereIn('study_program_id', $prodis)
                    ->orWhereIn('study_program_name', $prodis);
            }
        });
    }
}
