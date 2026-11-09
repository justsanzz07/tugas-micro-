<?php

namespace App\Http\Controllers\Kaprodi;

use App\Enums\ApprovalAction;
use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Submission\Submission;
use App\Services\Workflow\ApprovalStateMachine;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;

class ApprovalController extends Controller
{
    public function __construct(protected ApprovalStateMachine $stateMachine) {}

    public function dashboard(Request $request)
    {
        $kaprodi = $request->user();
        if (Schema::hasTable('academic_profiles')) {
            $kaprodi->loadMissing('profile');
        } else {
            $kaprodi->setRelation('profile', null);
        }
        $isAdmin = $request->user()->isAdmin();
        $prodiId = $kaprodi->profile?->study_program_id ?? $kaprodi->prodi_id ?? ($isAdmin ? null : 'TI');

        $baseQuery = Submission::with(['competitionDetail', 'members'])
            ->when(! $isAdmin && $prodiId, fn ($q) => $q->where(function ($sq) use ($prodiId) {
                $sq->where('study_program_id', $prodiId)->orWhereNull('study_program_id');
            }));

        // ── 1. STATISTIK ASLI DATABASE ──
        $pendingStatuses = ['submitted', 'kaprodi_review', 'dosen_review', 'review_kaprodi', 'review_dosen'];

        $totalSubmissions = (clone $baseQuery)->count();
        $totalPending = (clone $baseQuery)->whereIn('status', $pendingStatuses)->count();
        $totalSupervisorAssigned = (clone $baseQuery)->whereNotNull('supervisor_name')->where('supervisor_name', '!=', '')->count();
        $totalCompletedReview = (clone $baseQuery)->whereNotIn('status', array_merge($pendingStatuses, ['draft']))->count();

        // ── 2. QUICK FILTER COUNTS UNTUK ANTREAN VERIFIKASI ──
        $countAll = $totalPending;
        $countNasional = (clone $baseQuery)->whereIn('status', $pendingStatuses)
            ->whereHas('competitionDetail', fn ($q) => $q->where('level', 'NASIONAL'))
            ->count();
        $countInternasional = (clone $baseQuery)->whereIn('status', $pendingStatuses)
            ->whereHas('competitionDetail', fn ($q) => $q->where('level', 'INTERNASIONAL'))
            ->count();
        $countMenungguDosen = (clone $baseQuery)->whereIn('status', $pendingStatuses)
            ->where(fn ($q) => $q->whereNull('supervisor_name')->orWhere('supervisor_name', ''))
            ->count();
        $countSudahDitunjuk = (clone $baseQuery)->whereIn('status', $pendingStatuses)
            ->whereNotNull('supervisor_name')->where('supervisor_name', '!=', '')
            ->count();

        // ── 3. QUERY ANTREAN VERIFIKASI DENGAN PENCARIAN & FILTER ──
        $pendingQuery = (clone $baseQuery)->whereIn('status', $pendingStatuses);

        // Filter Pencarian
        if ($search = $request->query('search')) {
            $parsedId = null;
            if (preg_match('/(?:SUB-)?0*(\d+)/i', $search, $matches)) {
                $parsedId = (int) $matches[1];
            }

            $pendingQuery->where(function ($q) use ($search, $parsedId) {
                if ($parsedId) {
                    $q->where('id', $parsedId);
                }
                $q->orWhere('submitter_name', 'like', "%{$search}%")
                    ->orWhere('submitter_nim', 'like', "%{$search}%")
                    ->orWhere('supervisor_name', 'like', "%{$search}%")
                    ->orWhere('supervisor_nidn', 'like', "%{$search}%")
                    ->orWhere('title', 'like', "%{$search}%")
                    ->orWhereHas('competitionDetail', function ($cq) use ($search) {
                        $cq->where('name', 'like', "%{$search}%")
                            ->orWhere('organizer', 'like', "%{$search}%");
                    });
            });
        }

        // Quick Filter Shortcut
        $quick = $request->query('quick');
        if ($quick === 'nasional') {
            $pendingQuery->whereHas('competitionDetail', fn ($q) => $q->where('level', 'NASIONAL'));
        } elseif ($quick === 'internasional') {
            $pendingQuery->whereHas('competitionDetail', fn ($q) => $q->where('level', 'INTERNASIONAL'));
        } elseif ($quick === 'menunggu_dosen') {
            $pendingQuery->where(fn ($q) => $q->whereNull('supervisor_name')->orWhere('supervisor_name', ''));
        } elseif ($quick === 'sudah_ditunjuk') {
            $pendingQuery->whereNotNull('supervisor_name')->where('supervisor_name', '!=', '');
        }

        // Dropdown Filter: Tingkat Lomba
        if ($level = $request->query('level')) {
            if (in_array(strtoupper($level), ['REGIONAL', 'NASIONAL', 'INTERNASIONAL'])) {
                $pendingQuery->whereHas('competitionDetail', fn ($q) => $q->where('level', strtoupper($level)));
            }
        }

        // Dropdown Filter: Status Dosen
        if ($supervisorStatus = $request->query('supervisor_status')) {
            if ($supervisorStatus === 'belum') {
                $pendingQuery->where(fn ($q) => $q->whereNull('supervisor_name')->orWhere('supervisor_name', ''));
            } elseif ($supervisorStatus === 'sudah') {
                $pendingQuery->whereNotNull('supervisor_name')->where('supervisor_name', '!=', '');
            }
        }

        // Pagination Antrean Verifikasi (Default 10 per halaman)
        $pendingSubmissions = $pendingQuery->latest('id')->paginate(10, ['*'], 'page')->withQueryString();

        // ── 4. QUERY RIWAYAT PENGESAHAN KAPRODI ──
        $historyQuery = (clone $baseQuery)
            ->whereNotIn('status', array_merge($pendingStatuses, ['draft']));

        if ($searchHistory = $request->query('search_history')) {
            $parsedId = null;
            if (preg_match('/(?:SUB-)?0*(\d+)/i', $searchHistory, $matches)) {
                $parsedId = (int) $matches[1];
            }

            $historyQuery->where(function ($q) use ($searchHistory, $parsedId) {
                if ($parsedId) {
                    $q->where('id', $parsedId);
                }
                $q->orWhere('submitter_name', 'like', "%{$searchHistory}%")
                    ->orWhere('supervisor_name', 'like', "%{$searchHistory}%")
                    ->orWhere('title', 'like', "%{$searchHistory}%")
                    ->orWhereHas('competitionDetail', function ($cq) use ($searchHistory) {
                        $cq->where('name', 'like', "%{$searchHistory}%");
                    });
            });
        }

        // Pagination Riwayat Pengesahan (Default 10 per halaman)
        $historySubmissions = $historyQuery->latest('id')->paginate(10, ['*'], 'history_page')->withQueryString();

        return view('kaprodi.dashboard', compact(
            'kaprodi',
            'pendingSubmissions',
            'historySubmissions',
            'totalSubmissions',
            'totalPending',
            'totalSupervisorAssigned',
            'totalCompletedReview',
            'countAll',
            'countNasional',
            'countInternasional',
            'countMenungguDosen',
            'countSudahDitunjuk'
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

        // Daftar Dosen Program Studi yang dapat ditunjuk sebagai Dosen Pendamping
        $dosenList = [
            ['nidn' => '0701057501', 'nama' => 'Dr. H. M. Zainuri, M.Kom'],
            ['nidn' => '0715088901', 'nama' => 'Dr. Muhammad Ilham, M.Kom'],
            ['nidn' => '0710037802', 'nama' => 'Ahmad Faisol, M.T.'],
            ['nidn' => '0725118503', 'nama' => 'Siti Nurjanah, M.Kom'],
            ['nidn' => '0712048204', 'nama' => 'Zainal Arifin, M.Pd.'],
        ];

        return view('kaprodi.show', compact('submission', 'flow', 'budget', 'dosenList'));
    }

    public function process(Request $request, Submission $submission)
    {
        if ($request->user()->isAdmin()) {
            return back()->with('error', 'Administrator hanya memiliki hak akses peninjauan (read-only).');
        }

        Gate::authorize('approve', $submission);

        $rules = [
            'action' => ['required', 'in:APPROVE,REVISE,REJECT'],
            'notes' => ['nullable', 'string'],
        ];

        if ($request->input('action') === 'APPROVE') {
            $rules['supervisor_nidn'] = ['required', 'string'];
            $rules['supervisor_name'] = ['required', 'string'];
        }

        $validated = $request->validate($rules);

        $action = ApprovalAction::from($validated['action']);
        $extraData = [];

        if ($action === ApprovalAction::APPROVE) {
            $extraData = [
                'supervisor_nidn' => $validated['supervisor_nidn'],
                'supervisor_name' => $validated['supervisor_name'],
            ];
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
                ApprovalAction::APPROVE => "Pengajuan [{$submission->code}] berhasil disetujui, Dosen Pendamping [{$validated['supervisor_name']}] telah resmi ditugaskan, dan berkas diteruskan ke Dekan Fakultas.",
                ApprovalAction::REVISE => "Pengajuan [{$submission->code}] telah dikembalikan ke Mahasiswa untuk perbaikan proposal/RAB.",
                ApprovalAction::REJECT => "Pengajuan [{$submission->code}] telah ditolak oleh Kaprodi.",
                default => 'Keputusan Kaprodi berhasil disimpan.',
            };

            return redirect()->route('kaprodi.dashboard')->with('success', $msg);
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function updateSupervisor(Request $request, Submission $submission)
    {
        if ($request->user()->isAdmin()) {
            return back()->with('error', 'Administrator hanya memiliki hak akses peninjauan (read-only).');
        }

        $validated = $request->validate([
            'supervisor_nidn' => ['required', 'string', 'max:50'],
            'supervisor_name' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $oldName = $submission->supervisor_name ?? 'Belum Ditugaskan';
        $oldNidn = $submission->supervisor_nidn ?? '-';

        $submission->update([
            'supervisor_name' => $validated['supervisor_name'],
            'supervisor_nidn' => $validated['supervisor_nidn'],
        ]);

        try {
            AuditLog::create([
                'service_name' => 'sipres_workflow',
                'user_id' => $request->user()->id,
                'user_name' => $request->user()->name,
                'user_role' => $request->user()->role?->value ?? 'kaprodi',
                'action' => 'UPDATE_SUPERVISOR',
                'resource_type' => 'submission',
                'resource_id' => (string) $submission->id,
                'payload_before' => ['supervisor_name' => $oldName, 'supervisor_nidn' => $oldNidn],
                'payload_after' => [
                    'supervisor_name' => $validated['supervisor_name'],
                    'supervisor_nidn' => $validated['supervisor_nidn'],
                    'notes' => $validated['notes'] ?? null,
                ],
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
        } catch (\Throwable) {
            // Ignore audit log error if database/table not available
        }

        return redirect()->route('kaprodi.show', $submission->id)->with(
            'success',
            "Dosen Pendamping berhasil diperbarui menjadi {$validated['supervisor_name']} (NIDN: {$validated['supervisor_nidn']})."
        );
    }
}
