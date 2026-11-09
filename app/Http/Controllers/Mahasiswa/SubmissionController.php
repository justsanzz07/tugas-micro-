<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Enums\CompetitionLevel;
use App\Http\Controllers\Controller;
use App\Models\External\Dosen;
use App\Models\External\Mahasiswa;
use App\Models\Submission\Submission;
use App\Services\Submission\SubmissionService;
use App\Services\Workflow\ApprovalStateMachine;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;

class SubmissionController extends Controller
{
    public function __construct(
        protected SubmissionService $submissionService,
        protected ApprovalStateMachine $stateMachine
    ) {}

    public function dashboard(Request $request)
    {
        $user = $request->user();
        if (Schema::hasTable('academic_profiles')) {
            $user->loadMissing('profile');
        } else {
            $user->setRelation('profile', null);
        }
        $nim = $user->identifier ?? $user->email;
        $isAdmin = $user->isAdmin();

        $blockedSubmission = $isAdmin ? null : $this->submissionService->checkLpjBlock($nim);

        // Kueri dasar: Khusus pengajuan mahasiswa akun ini, atau semua pengajuan jika diakses Administrator
        $baseQuery = Submission::with(['competitionDetail', 'members', 'certificates']);
        if (! $isAdmin) {
            $baseQuery->where(function ($q) use ($nim) {
                $q->where('submitter_nim', $nim)
                    ->orWhereHas('members', fn ($m) => $m->where('nim', $nim));
            });
        }

        // Hitung statistik khusus akun mahasiswa ini
        $totalAll = (clone $baseQuery)->count();
        $totalActive = (clone $baseQuery)->whereIn('status', [
            'draft', 'submitted', 'dosen_review', 'kaprodi_review',
            'dekan_review', 'kemahasiswaan_review', 'warek_review',
            'review_dosen', 'review_dekan', 'review_warek',
            'approved_dosen', 'approved_dekan',
        ])->count();

        $totalRevision = (clone $baseQuery)->whereIn('status', [
            'revision_required', 'revision_dosen', 'revision_dekan', 'revision_warek',
        ])->count();

        $totalWaitingLpj = (clone $baseQuery)->whereIn('status', [
            'approved', 'approved_final', 'waiting_lpj', 'review_lpj',
        ])->count();

        $totalCompleted = (clone $baseQuery)->where('status', 'completed')->count();

        $totalSkpiPoints = (clone $baseQuery)->with('certificates')
            ->get()
            ->flatMap->certificates
            ->where('validation_status.value', 'VALID')
            ->sum('skpi_points');

        // Filter berdasarkan tab
        $activeTab = $request->query('tab', 'all');
        $query = clone $baseQuery;

        if ($activeTab === 'active') {
            $query->whereIn('status', [
                'draft', 'submitted', 'dosen_review', 'kaprodi_review',
                'dekan_review', 'kemahasiswaan_review', 'warek_review',
                'review_dosen', 'review_dekan', 'review_warek',
                'approved_dosen', 'approved_dekan',
            ]);
        } elseif ($activeTab === 'revision') {
            $query->whereIn('status', [
                'revision_required', 'revision_dosen', 'revision_dekan', 'revision_warek',
            ]);
        } elseif ($activeTab === 'waiting_lpj') {
            $query->whereIn('status', [
                'approved', 'approved_final', 'waiting_lpj', 'review_lpj',
            ]);
        } elseif ($activeTab === 'completed') {
            $query->where('status', 'completed');
        }

        // Pencarian opsional
        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('team_name', 'like', "%{$search}%")
                    ->orWhereHas('competitionDetail', function ($cq) use ($search) {
                        $cq->where('competition_name', 'like', "%{$search}%")
                            ->orWhere('organizer', 'like', "%{$search}%");
                    });
            });
        }

        $submissions = $query->latest('id')->paginate(10)->withQueryString();

        return view('mahasiswa.dashboard', compact(
            'user',
            'submissions',
            'blockedSubmission',
            'totalAll',
            'totalActive',
            'totalRevision',
            'totalWaitingLpj',
            'totalCompleted',
            'totalSkpiPoints',
            'activeTab'
        ));
    }

    public function create(Request $request)
    {
        $user = $request->user();
        $nim = $user->identifier ?? $user->email;

        // Cekal LPJ
        if ($this->submissionService->checkLpjBlock($nim)) {
            return redirect()->route('mahasiswa.dashboard')->with('error', 'Akses pembuatan pengajuan lomba baru diblokir karena ada tanggungan LPJ yang belum diselesaikan.');
        }

        // Mock/Daftar Dosen & Rekan Mahasiswa untuk Tom Select dropdown
        $dosenList = [
            ['nidn' => '0715088901', 'nama' => 'Dr. Muhammad Ilham, M.Kom (Informatika)'],
            ['nidn' => '0710037802', 'nama' => 'Ahmad Faisol, M.T. (Teknologi Informasi)'],
            ['nidn' => '0725118503', 'nama' => 'Siti Nurjanah, M.Kom (Sistem Informasi)'],
            ['nidn' => '0712048204', 'nama' => 'Zainal Arifin, M.Pd. (Pendidikan Saintek)'],
        ];

        $levels = CompetitionLevel::cases();

        return view('mahasiswa.submissions.create', compact('dosenList', 'levels'));
    }

    public function store(Request $request)
    {
        if ($request->user()->isAdmin()) {
            return back()->with('error', 'Administrator hanya memiliki hak akses peninjauan (read-only).');
        }

        $user = $request->user();

        $validated = $request->validate([
            'competition_name' => ['required', 'string', 'max:255'],
            'organizer' => ['required', 'string', 'max:255'],
            'level' => ['required', 'string'],
            'category' => ['required', 'string', 'max:150'],
            'competition_url' => ['nullable', 'url', 'max:500'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'location' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'dosen_nidn' => ['nullable', 'string'],
            'members' => ['nullable', 'array'],
            'members.*' => ['nullable', 'string'],
            'proposal_file' => ['required', 'file', 'mimes:pdf', 'max:10240'], // Max 10MB
            'budget_items' => ['required', 'array', 'min:1'],
            'budget_items.*.item_name' => ['required', 'string'],
            'budget_items.*.quantity' => ['required', 'integer', 'min:1'],
            'budget_items.*.unit' => ['required', 'string'],
            'budget_items.*.unit_price' => ['required', 'numeric', 'min:0'],
            'budget_items.*.notes' => ['nullable', 'string'],
        ]);

        try {
            $submission = $this->submissionService->createSubmission($user, $validated);

            return redirect()->route('mahasiswa.submissions.show', $submission->id)
                ->with('success', "Pengajuan delegasi lomba [{$submission->code}] berhasil dikirim ke Ketua Program Studi (Kaprodi) untuk ditinjau dan ditentukan Dosen Pendampingnya.");
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function show(Submission $submission)
    {
        Gate::authorize('view', $submission);

        $submission->load([
            'competitionDetail',
            'members',
            'officialLetters',
            'lpjReport',
            'certificates',
        ]);

        $flow = $submission->approvalFlow();
        $budget = $submission->budgetRequest();
        $decision = $budget?->fundingDecision;
        $ticket = $decision?->disbursementTicket;
        $incentive = $submission->incentiveAward();

        return view('mahasiswa.submissions.show', compact(
            'submission',
            'flow',
            'budget',
            'decision',
            'ticket',
            'incentive'
        ));
    }

    public function edit(Submission $submission)
    {
        if (request()->user()->isAdmin()) {
            return redirect()->route('mahasiswa.submissions.show', $submission)->with('error', 'Administrator hanya memiliki hak akses peninjauan (read-only).');
        }

        Gate::authorize('update', $submission);

        $submission->load(['competitionDetail', 'members']);
        $budget = $submission->budgetRequest();

        $levels = CompetitionLevel::cases();
        $dosenList = [
            ['nidn' => '0715088901', 'nama' => 'Dr. Muhammad Ilham, M.Kom (Informatika)'],
            ['nidn' => '0710037802', 'nama' => 'Ahmad Faisol, M.T. (Teknologi Informasi)'],
            ['nidn' => '0725118503', 'nama' => 'Siti Nurjanah, M.Kom (Sistem Informasi)'],
        ];

        return view('mahasiswa.submissions.edit', compact('submission', 'budget', 'levels', 'dosenList'));
    }

    public function resubmit(Request $request, Submission $submission)
    {
        if ($request->user()->isAdmin()) {
            return back()->with('error', 'Administrator hanya memiliki hak akses peninjauan (read-only).');
        }

        Gate::authorize('update', $submission);

        $request->validate([
            'revision_notes' => ['nullable', 'string'],
        ]);

        try {
            $this->stateMachine->resubmit($submission, $request->user(), $request->input('revision_notes'));

            return redirect()->route('mahasiswa.submissions.show', $submission->id)
                ->with('success', 'Perbaikan berhasil diajukan ulang ke tahap peninjauan.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function lpjForm(Submission $submission)
    {
        Gate::authorize('view', $submission);

        $submission->load(['competitionDetail', 'lpjReport.attachments', 'certificates']);
        $budget = $submission->budgetRequest();
        $decision = $budget?->fundingDecision;

        return view('mahasiswa.submissions.lpj', compact('submission', 'budget', 'decision'));
    }

    public function submitLpj(Request $request, Submission $submission)
    {
        if ($request->user()->isAdmin()) {
            return back()->with('error', 'Administrator hanya memiliki hak akses peninjauan (read-only).');
        }

        Gate::authorize('submitLpj', $submission);

        $validated = $request->validate([
            'report_text' => ['required', 'string'],
            'receipt_files' => ['nullable', 'array'],
            'receipt_files.*' => ['file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'achievement_rank' => ['required', 'string', 'max:50'],
            'certificate_file' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ]);

        try {
            $this->submissionService->submitLpj($submission, $request->user(), $validated);

            return redirect()->route('mahasiswa.submissions.show', $submission->id)
                ->with('success', 'Laporan Pertanggungjawaban (LPJ) & Sertifikat berhasil diunggah. Menunggu verifikasi dari Wakil Rektor.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
