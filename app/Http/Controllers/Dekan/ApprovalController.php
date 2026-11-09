<?php

namespace App\Http\Controllers\Dekan;

use App\Enums\ApprovalAction;
use App\Http\Controllers\Controller;
use App\Models\Submission\Submission;
use App\Services\Submission\SubmissionService;
use App\Services\Workflow\ApprovalStateMachine;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;

class ApprovalController extends Controller
{
    public function __construct(
        protected ApprovalStateMachine $stateMachine,
        protected SubmissionService $submissionService
    ) {}

    public function dashboard(Request $request)
    {
        $dekan = $request->user();
        if (Schema::hasTable('academic_profiles')) {
            $dekan->loadMissing('profile');
        } else {
            $dekan->setRelation('profile', null);
        }
        $fakultasId = $dekan->fakultas_id ?? $dekan->profile?->faculty_id ?? 'FTI';

        // Base query untuk fakultas Dekan
        $baseQuery = Submission::with(['competitionDetail', 'members']);

        // 1. Antrean Persetujuan Proposal & Penetapan Dana (Lanjutan dari Kaprodi)
        $pendingSubmissions = (clone $baseQuery)
            ->whereIn('status', ['dekan_review', 'approved_kaprodi', 'approved_dosen', 'review_dekan'])
            ->latest('id')
            ->get();

        // 2. Antrean Validasi LPJ & SKPI Pasca-Lomba
        $pendingLpjSubmissions = (clone $baseQuery)
            ->whereIn('status', ['review_lpj', 'lpj_review', 'waiting_lpj'])
            ->whereHas('lpjReport')
            ->latest('id')
            ->get();

        // 3. Empat Statistik Utama Dashboard Dekan
        $totalPendingProposal = $pendingSubmissions->count();
        $totalLpj = $pendingLpjSubmissions->count();
        $totalActive = (clone $baseQuery)->whereNotIn('status', ['completed', 'rejected', 'draft'])->count();
        $totalCompleted = (clone $baseQuery)->where('status', 'completed')->count();

        // 4. Counts untuk Tab Status Partisipasi & Capaian Prestasi
        $statusProses = [
            'submitted', 'kaprodi_review', 'dosen_review', 'review_kaprodi', 'review_dosen',
            'dekan_review', 'approved_kaprodi', 'approved_dosen', 'review_dekan',
            'revision_required', 'revision_dosen', 'revision_dekan',
        ];
        $statusDisetujui = ['approved', 'approved_final', 'waiting_lpj', 'review_lpj', 'lpj_review'];

        $countAll = (clone $baseQuery)->count();
        $countProses = (clone $baseQuery)->whereIn('status', $statusProses)->count();
        $countDisetujui = (clone $baseQuery)->whereIn('status', $statusDisetujui)->count();
        $countSelesai = (clone $baseQuery)->where('status', 'completed')->count();

        // 5. Query Partisipasi & Capaian Prestasi Mahasiswa Fakultas (Dengan Filter, Search, & Pagination 10)
        $allQuery = (clone $baseQuery);

        // Filter Tab Cepat
        $activeTab = $request->query('tab', 'all');
        if ($activeTab === 'proses') {
            $allQuery->whereIn('status', $statusProses);
        } elseif ($activeTab === 'disetujui') {
            $allQuery->whereIn('status', $statusDisetujui);
        } elseif ($activeTab === 'selesai') {
            $allQuery->where('status', 'completed');
        }

        // Filter Dropdown Status Tertentu
        if ($filterStatus = $request->query('status')) {
            if ($filterStatus === 'review_kaprodi') {
                $allQuery->whereIn('status', ['submitted', 'kaprodi_review', 'dosen_review', 'review_kaprodi', 'review_dosen']);
            } elseif ($filterStatus === 'review_dekan') {
                $allQuery->whereIn('status', ['dekan_review', 'approved_kaprodi', 'approved_dosen', 'review_dekan']);
            } elseif ($filterStatus === 'disetujui') {
                $allQuery->whereIn('status', $statusDisetujui);
            } elseif ($filterStatus === 'selesai') {
                $allQuery->where('status', 'completed');
            }
        }

        // Filter Pencarian
        if ($search = $request->query('search')) {
            $parsedId = null;
            if (preg_match('/(?:SUB-)?0*(\d+)/i', $search, $matches)) {
                $parsedId = (int) $matches[1];
            }

            $allQuery->where(function ($q) use ($search, $parsedId) {
                if ($parsedId) {
                    $q->where('id', $parsedId);
                }
                $q->orWhere('submitter_name', 'like', "%{$search}%")
                    ->orWhere('submitter_nim', 'like', "%{$search}%")
                    ->orWhere('title', 'like', "%{$search}%")
                    ->orWhereHas('competitionDetail', function ($cq) use ($search) {
                        $cq->where('name', 'like', "%{$search}%")
                            ->orWhere('organizer', 'like', "%{$search}%");
                    });
            });
        }

        // Server-Side Pagination Default 10 per halaman
        $allFacultySubmissions = $allQuery->latest('id')->paginate(10)->withQueryString();

        return view('dekan.dashboard', compact(
            'dekan',
            'pendingSubmissions',
            'pendingLpjSubmissions',
            'allFacultySubmissions',
            'totalPendingProposal',
            'totalLpj',
            'totalActive',
            'totalCompleted',
            'fakultasId',
            'countAll',
            'countProses',
            'countDisetujui',
            'countSelesai',
            'activeTab'
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

        return view('dekan.show', compact('submission', 'flow', 'budget'));
    }

    public function process(Request $request, Submission $submission)
    {
        if ($request->user()->isAdmin()) {
            return back()->with('error', 'Administrator hanya memiliki hak akses peninjauan (read-only).');
        }

        Gate::authorize('approve', $submission);

        $validated = $request->validate([
            'action' => ['required', 'in:APPROVE,REVISE,REJECT'],
            'approved_amount' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
        ]);

        $action = ApprovalAction::from($validated['action']);
        $extraData = [];

        if ($action === ApprovalAction::APPROVE) {
            $extraData['approved_amount'] = $validated['approved_amount'] ?? $submission->total_budget_requested ?? 0;
        }

        try {
            $this->stateMachine->processDecision(
                $submission,
                $request->user(),
                $action,
                $validated['notes'] ?? null,
                $extraData
            );

            $msg = match ($action) {
                ApprovalAction::APPROVE => "Pengajuan [{$submission->code}] telah disetujui final oleh Dekan Fakultas! Surat Dispensasi Kuliah dan Surat Tugas Resmi ber-QR Code telah diterbitkan.",
                ApprovalAction::REVISE => "Pengajuan [{$submission->code}] dikembalikan ke Mahasiswa untuk perbaikan proposal/RAB.",
                ApprovalAction::REJECT => "Pengajuan [{$submission->code}] telah ditolak oleh Dekan Fakultas.",
                default => 'Keputusan Dekan berhasil disimpan.',
            };

            return redirect()->route('dekan.dashboard')->with('success', $msg);
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function showLpj(Submission $submission)
    {
        Gate::authorize('view', $submission);

        $submission->load([
            'competitionDetail',
            'lpjReport.attachments',
            'certificates',
        ]);

        $budget = $submission->budgetRequest();
        $decision = $budget?->fundingDecision;

        return view('dekan.lpj-review', compact('submission', 'budget', 'decision'));
    }

    public function processLpj(Request $request, Submission $submission)
    {
        if ($request->user()->isAdmin()) {
            return back()->with('error', 'Administrator hanya memiliki hak akses peninjauan (read-only).');
        }

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
                ? "Laporan LPJ & Prestasi [{$submission->code}] telah disahkan sah oleh Dekan Fakultas! Poin SKPI & insentif telah tercatat, dan status pengajuan kini SELESAI (COMPLETED)."
                : "LPJ [{$submission->code}] dikembalikan ke Mahasiswa untuk perbaikan dokumen nota/kuitansi.";

            return redirect()->route('dekan.dashboard')->with('success', $msg);
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function reports(Request $request)
    {
        $dekan = $request->user();
        if (Schema::hasTable('academic_profiles')) {
            $dekan->loadMissing('profile');
        } else {
            $dekan->setRelation('profile', null);
        }
        $fakultasId = $dekan->fakultas_id ?? $dekan->profile?->faculty_id ?? 'FTI';

        $baseQuery = Submission::with(['competitionDetail', 'certificates', 'officialLetters', 'members']);

        // ── 1. STATISTIK RINGKASAN REKAPITULASI PRESTASI ──
        $totalSubmissions = (clone $baseQuery)->count();

        $totalPrestasi = (clone $baseQuery)->whereHas('certificates', function ($q) {
            $q->whereNotNull('ranking')->where('ranking', '!=', '');
        })->count();

        // Akumulasi Poin SKPI yang telah divalidasi
        $allSubsForStats = (clone $baseQuery)->with(['certificates', 'officialLetters'])->get();
        $totalSkpiPoints = (float) $allSubsForStats->flatMap->certificates
            ->sum(fn ($c) => (float) ($c->skpi_points_awarded ?? $c->skpi_points ?? 0));

        // Total Surat Resmi / Dispensasi Kuliah yang Diterbitkan
        $totalDispensasi = $allSubsForStats->flatMap->officialLetters->count();

        // ── 2. QUERY REPORT DENGAN PENCARIAN & FILTER ──
        $query = (clone $baseQuery);

        // Filter Pencarian
        if ($search = $request->query('search')) {
            $parsedId = null;
            if (preg_match('/(?:SUB-)?0*(\d+)/i', $search, $matches)) {
                $parsedId = (int) $matches[1];
            }

            $query->where(function ($q) use ($search, $parsedId) {
                if ($parsedId) {
                    $q->where('id', $parsedId);
                }
                $q->orWhere('submitter_name', 'like', "%{$search}%")
                    ->orWhere('submitter_nim', 'like', "%{$search}%")
                    ->orWhere('title', 'like', "%{$search}%")
                    ->orWhereHas('competitionDetail', function ($cq) use ($search) {
                        $cq->where('name', 'like', "%{$search}%")
                            ->orWhere('organizer', 'like', "%{$search}%");
                    });
            });
        }

        // Filter Tingkat Perlombaan
        if ($level = $request->query('level')) {
            if (in_array(strtoupper($level), ['REGIONAL', 'NASIONAL', 'INTERNASIONAL'])) {
                $query->whereHas('competitionDetail', fn ($q) => $q->where('level', strtoupper($level)));
            }
        }

        // Filter Hanya yang Berprestasi (Juara)
        if ($request->query('achievement') === 'juara') {
            $query->whereHas('certificates', function ($q) {
                $q->whereNotNull('ranking')->where('ranking', '!=', '');
            });
        }

        // Pagination 15 per halaman
        $submissions = $query->latest('id')->paginate(15)->withQueryString();

        return view('dekan.reports', compact(
            'submissions',
            'fakultasId',
            'dekan',
            'totalSubmissions',
            'totalPrestasi',
            'totalSkpiPoints',
            'totalDispensasi'
        ));
    }
}
