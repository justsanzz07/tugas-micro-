<?php

namespace App\Http\Controllers\Warek;

use App\Enums\ApprovalAction;
use App\Http\Controllers\Controller;
use App\Models\Finance\DisbursementTicket;
use App\Models\Submission\Submission;
use App\Services\Submission\SubmissionService;
use App\Services\Workflow\ApprovalStateMachine;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class FinalApprovalController extends Controller
{
    public function __construct(
        protected ApprovalStateMachine $stateMachine,
        protected SubmissionService $submissionService
    ) {}

    public function dashboard()
    {
        // 1. Antrean Review Final Warek
        $pendingFinalSubmissions = Submission::with(['competitionDetail', 'members'])
            ->whereIn('status', ['kemahasiswaan_review', 'warek_review'])
            ->latest()
            ->get();

        // 2. Antrean Validasi LPJ
        $pendingLpjSubmissions = Submission::with(['competitionDetail', 'lpjReport', 'certificates'])
            ->whereIn('status', ['waiting_lpj', 'lpj_review'])
            ->latest()
            ->get();

        // 3. Metrik Serapan Anggaran
        $totalApprovedFunding = (float) Submission::sum('total_budget_approved');
        $totalDisbursed = (float) DisbursementTicket::sum('amount');
        $totalCompleted = Submission::where('status', 'completed')->count();
        $totalPendingFinal = $pendingFinalSubmissions->count();

        // 4. Seluruh delegasi aktif
        $allSubmissions = Submission::with(['competitionDetail', 'certificates'])
            ->latest()
            ->paginate(15);

        return view('warek.dashboard', compact(
            'pendingFinalSubmissions',
            'pendingLpjSubmissions',
            'totalApprovedFunding',
            'totalDisbursed',
            'totalCompleted',
            'totalPendingFinal',
            'allSubmissions'
        ));
    }

    public function show(Submission $submission)
    {
        Gate::authorize('view', $submission);

        $submission->load([
            'competitionDetail',
            'members',
            'officialLetters',
            'lpjReport.attachments',
            'certificates',
        ]);

        $flow = $submission->approvalFlow();
        $budget = $submission->budgetRequest();
        $decision = $budget?->fundingDecision;
        $ticket = $decision?->disbursementTicket;
        $incentive = $submission->incentiveAward();

        return view('warek.show', compact(
            'submission',
            'flow',
            'budget',
            'decision',
            'ticket',
            'incentive'
        ));
    }

    public function processApproval(Request $request, Submission $submission)
    {
        Gate::authorize('approve', $submission);

        $validated = $request->validate([
            'action' => ['required', 'in:APPROVE,REVISE,REJECT'],
            'approved_amount' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
        ]);

        $action = ApprovalAction::from($validated['action']);

        if ($action === ApprovalAction::APPROVE && (! isset($validated['approved_amount']) || $validated['approved_amount'] < 0)) {
            return back()->with('error', 'Nominal dana yang disetujui wajib diisi saat menyetujui pengajuan.');
        }

        try {
            $newStatus = $this->stateMachine->processDecision(
                $submission,
                $request->user(),
                $action,
                $validated['notes'],
                ['approved_amount' => $validated['approved_amount'] ?? 0]
            );

            $msg = match ($action) {
                ApprovalAction::APPROVE => "Pengajuan [{$submission->code}] telah disetujui final! Surat Tugas Universitas ber-QR Code telah diterbitkan dan tiket pencairan SIMPAY berhasil dibuat.",
                ApprovalAction::REVISE => "Pengajuan [{$submission->code}] dikembalikan ke Mahasiswa untuk perbaikan.",
                ApprovalAction::REJECT => "Pengajuan [{$submission->code}] ditolak.",
                default => 'Keputusan berhasil disimpan.',
            };

            return redirect()->route('warek.dashboard')->with('success', $msg);
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function showLpj(Submission $submission)
    {
        Gate::authorize('validateLpj', $submission);

        $submission->load([
            'competitionDetail',
            'lpjReport.attachments',
            'certificates',
        ]);

        $budget = $submission->budgetRequest();
        $decision = $budget?->fundingDecision;

        return view('warek.lpj-review', compact('submission', 'budget', 'decision'));
    }

    public function processLpj(Request $request, Submission $submission)
    {
        Gate::authorize('validateLpj', $submission);

        $validated = $request->validate([
            'is_valid' => ['required', 'boolean'],
            'skpi_points' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'incentive_amount' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
        ]);

        try {
            $this->submissionService->validateLpj(
                $submission,
                $request->user(),
                (bool) $validated['is_valid'],
                $validated['skpi_points'] ?? null,
                $validated['incentive_amount'] ?? null,
                $validated['notes'] ?? null
            );

            $msg = $validated['is_valid']
                ? "LPJ dan Prestasi [{$submission->code}] telah divalidasi sah! Poin SKPI & insentif telah disahkan, serta status pengajuan kini SELESAI (COMPLETED)."
                : "LPJ [{$submission->code}] dikembalikan ke Mahasiswa untuk perbaikan dokumen.";

            return redirect()->route('warek.dashboard')->with('success', $msg);
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function financeOverview()
    {
        $tickets = DisbursementTicket::latest()
            ->paginate(15);

        $totalApproved = (float) Submission::sum('total_budget_approved');
        $totalDisbursed = (float) DisbursementTicket::where('status', 'disbursed')->sum('amount');
        $totalPending = (float) DisbursementTicket::where('status', 'pending')->sum('amount');

        return view('warek.finance', compact('tickets', 'totalApproved', 'totalDisbursed', 'totalPending'));
    }
}
