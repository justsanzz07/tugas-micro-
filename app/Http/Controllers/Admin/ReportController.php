<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Submission\AchievementCertificate;
use App\Models\Submission\Submission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $year = $request->query('year', date('Y'));
        $faculty = $request->query('faculty');

        $query = Submission::with(['competitionDetail', 'certificates'])->latest('id');

        if ($faculty) {
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

        $submissions = $query->paginate(15)->withQueryString();

        // 1. Rekapitulasi Pengajuan
        $totalSubmissions = $query->count();
        $totalApproved = (clone $query)->whereIn('status', ['approved', 'approved_final', 'waiting_lpj', 'lpj_review', 'completed'])->count();
        $totalCompleted = (clone $query)->where('status', 'completed')->count();
        $totalRejected = (clone $query)->where('status', 'rejected')->count();

        // 2. Rekapitulasi Prestasi
        $totalAchievements = AchievementCertificate::count();

        // 3. Rekapitulasi Anggaran & Pencairan
        $totalBudgetProposed = (float) (clone $query)->sum('total_budget_requested');
        $totalBudgetApproved = (float) (clone $query)->sum('total_budget_approved');

        $totalDisbursed = 0.0;
        try {
            $totalDisbursed = (float) DB::connection('sipres_finance')
                ->table('disbursements')
                ->where('status', 'disbursed')
                ->sum('amount');
        } catch (\Throwable) {
            $totalDisbursed = 0.0;
        }

        // 4. Data per Fakultas
        $facultySummary = [];
        $faculties = [
            'FTI' => ['name' => 'Fakultas Teknologi Informasi', 'pagu' => 60000000],
            'FAI' => ['name' => 'Fakultas Agama Islam', 'pagu' => 45000000],
            'FEB' => ['name' => 'Fakultas Ekonomi & Bisnis', 'pagu' => 40000000],
            'FKES' => ['name' => 'Fakultas Kesehatan', 'pagu' => 35000000],
        ];

        foreach ($faculties as $code => $fData) {
            $fSubs = Submission::where(function ($q) use ($code) {
                $prodiMap = [
                    'FTI' => ['TI', 'TE', 'SI', 'Teknologi Informasi'],
                    'FAI' => ['PAI', 'PBA', 'MPI', 'HES'],
                    'FEB' => ['MNJ', 'AKT', 'Manajemen'],
                    'FKES' => ['KPR', 'KBD', 'Keperawatan'],
                ];
                $prodis = $prodiMap[$code] ?? [$code];
                $q->whereIn('study_program_id', $prodis)->orWhereIn('study_program_name', $prodis);
                if (Schema::connection($q->getModel()->getConnectionName())->hasColumn($q->getModel()->getTable(), 'fakultas_id')) {
                    $q->orWhere('fakultas_id', $code);
                }
            });

            $fApproved = (float) (clone $fSubs)->sum('total_budget_approved');
            $fCount = (clone $fSubs)->count();

            $facultySummary[$code] = [
                'name' => $fData['name'],
                'pagu' => $fData['pagu'],
                'count' => $fCount,
                'approved' => $fApproved,
                'remaining' => max(0, $fData['pagu'] - $fApproved),
                'percent' => $fData['pagu'] > 0 ? round(($fApproved / $fData['pagu']) * 100, 1) : 0,
            ];
        }

        return view('admin.reports.index', compact(
            'submissions',
            'year',
            'faculty',
            'totalSubmissions',
            'totalApproved',
            'totalCompleted',
            'totalRejected',
            'totalAchievements',
            'totalBudgetProposed',
            'totalBudgetApproved',
            'totalDisbursed',
            'facultySummary'
        ));
    }
}
