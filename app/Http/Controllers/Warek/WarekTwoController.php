<?php

namespace App\Http\Controllers\Warek;

use App\Enums\ApprovalAction;
use App\Http\Controllers\Controller;
use App\Models\Submission\Submission;
use App\Services\Submission\SubmissionService;
use App\Services\Workflow\ApprovalStateMachine;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;

class WarekTwoController extends Controller
{
    public function __construct(
        protected ApprovalStateMachine $stateMachine,
        protected SubmissionService $submissionService
    ) {}

    public function dashboard(Request $request)
    {
        // 1. Antrean Persetujuan Final Tingkat Universitas (Warek II)
        $pendingSubmissions = Submission::with(['competitionDetail', 'members'])
            ->whereIn('status', ['warek_review', 'review_warek', 'review_warek_ii'])
            ->latest('id')
            ->get();

        // 2. Empat Kartu Statistik Utama Warek II
        $totalPendingFinal = $pendingSubmissions->count();
        $totalApprovedFunding = (float) Submission::sum('total_budget_approved');

        $totalDisbursed = (float) DB::connection('sipres_finance')
            ->table('disbursements')
            ->where('status', 'disbursed')
            ->sum('amount');

        $totalAchievements = Submission::where('status', 'completed')->count();

        // 3. Ringkasan Monitoring Anggaran per Fakultas
        $facultyBudgetSummary = [];
        $facultyList = [
            'FTI' => ['name' => 'Fakultas Teknologi Informasi', 'allocated' => 50000000],
            'FAI' => ['name' => 'Fakultas Agama Islam', 'allocated' => 40000000],
            'FEB' => ['name' => 'Fakultas Ekonomi & Bisnis', 'allocated' => 35000000],
            'FKES' => ['name' => 'Fakultas Kesehatan', 'allocated' => 35000000],
        ];

        foreach ($facultyList as $code => $fac) {
            $fQuery = Submission::query();
            $this->applyFacultyFilter($fQuery, $code);
            $facultyBudgetSummary[$code] = [
                'name' => $fac['name'],
                'allocated' => $fac['allocated'],
                'used' => (float) $fQuery->sum('total_budget_approved'),
            ];
        }

        // 4. Delegasi dan Prestasi Mahasiswa Universitas
        $recentAchievements = Submission::with(['competitionDetail', 'certificates', 'members'])
            ->latest('id')
            ->limit(10)
            ->get();

        return view('warek2.dashboard', compact(
            'pendingSubmissions',
            'totalPendingFinal',
            'totalApprovedFunding',
            'totalDisbursed',
            'totalAchievements',
            'facultyBudgetSummary',
            'recentAchievements'
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
        if ($budget) {
            $budget->loadMissing('items');
        }

        return view('warek2.show', compact('submission', 'flow', 'budget'));
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
            if (! isset($validated['approved_amount']) || $validated['approved_amount'] < 0) {
                return back()->withInput()->with('error', 'Nominal dana yang disetujui final wajib ditentukan.');
            }
            $extraData['approved_amount'] = (float) $validated['approved_amount'];
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
                ApprovalAction::APPROVE => "Pengajuan [{$submission->code}] telah disetujui final oleh Wakil Rektor II dengan plafon Rp ".number_format($extraData['approved_amount'] ?? 0, 0, ',', '.').'. Tiket pencairan diteruskan ke Bagian Keuangan.',
                ApprovalAction::REVISE => "Pengajuan [{$submission->code}] dikembalikan untuk perbaikan sesuai catatan Warek II.",
                ApprovalAction::REJECT => "Pengajuan [{$submission->code}] telah ditolak oleh Wakil Rektor II.",
                default => 'Keputusan Wakil Rektor II berhasil disimpan.',
            };

            return redirect()->route('warek2.dashboard')->with('success', $msg);
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function monitoringBudget(Request $request)
    {
        $year = $request->query('year', date('Y'));
        $faculty = $request->query('faculty');

        $query = Submission::with(['competitionDetail'])
            ->whereNotNull('total_budget_approved')
            ->where('total_budget_approved', '>', 0);

        if ($faculty) {
            $this->applyFacultyFilter($query, $faculty);
        }

        $submissions = $query->latest('id')->paginate(15)->withQueryString();

        $facultyAllocations = [
            'FTI' => ['name' => 'Fakultas Teknologi Informasi', 'allocation' => 60000000],
            'FAI' => ['name' => 'Fakultas Agama Islam', 'allocation' => 45000000],
            'FEB' => ['name' => 'Fakultas Ekonomi & Bisnis', 'allocation' => 40000000],
            'FKES' => ['name' => 'Fakultas Kesehatan', 'allocation' => 35000000],
        ];

        $budgetReport = [];
        foreach ($facultyAllocations as $code => $data) {
            $fQuery = Submission::query();
            $this->applyFacultyFilter($fQuery, $code);
            $used = (float) $fQuery->sum('total_budget_approved');

            $disbursed = (float) DB::connection('sipres_finance')
                ->table('disbursements')
                ->where('status', 'disbursed')
                ->sum('amount');

            $budgetReport[$code] = [
                'name' => $data['name'],
                'allocation' => $data['allocation'],
                'approved' => $used,
                'disbursed' => $disbursed,
                'remaining' => max(0, $data['allocation'] - $used),
                'percent' => $data['allocation'] > 0 ? round(($used / $data['allocation']) * 100, 1) : 0,
            ];
        }

        return view('warek2.monitoring-budget', compact('submissions', 'budgetReport', 'year', 'faculty'));
    }

    public function monitoringAchievements(Request $request)
    {
        $faculty = $request->query('faculty');
        $level = $request->query('level');

        $query = Submission::with(['competitionDetail', 'certificates', 'members']);

        if ($faculty) {
            $this->applyFacultyFilter($query, $faculty);
        }

        if ($level) {
            $query->whereHas('competitionDetail', function ($q) use ($level) {
                $q->where('level', $level);
            });
        }

        $submissions = $query->latest('id')->paginate(15)->withQueryString();

        return view('warek2.monitoring-achievements', compact('submissions', 'faculty', 'level'));
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
