<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Submission\AchievementCertificate;
use App\Models\Submission\Submission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function dashboard(Request $request)
    {
        $admin = $request->user();

        // 1. Enam Kartu Statistik Utama Universitas
        $totalSubmissions = Submission::count();

        $totalPending = Submission::whereIn('status', [
            'submitted', 'kaprodi_review', 'dosen_review', 'review_kaprodi', 'review_dosen',
            'dekan_review', 'approved_kaprodi', 'approved_dosen', 'review_dekan',
            'keuangan_review', 'review_keuangan', 'warek_review', 'review_warek', 'review_warek_ii',
        ])->count();

        $totalCompleted = Submission::where('status', 'completed')->count();

        $totalAchievements = AchievementCertificate::count();
        if ($totalAchievements === 0) {
            $totalAchievements = $totalCompleted;
        }

        $totalApprovedBudget = (float) Submission::sum('total_budget_approved');

        $totalDisbursed = 0.0;
        try {
            $totalDisbursed = (float) DB::connection('sipres_finance')
                ->table('disbursements')
                ->where('status', 'disbursed')
                ->sum('amount');
        } catch (\Throwable) {
            $totalDisbursed = 0.0;
        }

        // 2. Data Grafik Ringkasan (Tren Bulanan & Distribusi Fakultas)
        $monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        $monthlyCounts = array_fill(1, 12, 0);

        $facultyCounts = [
            'FTI' => 0,
            'FAI' => 0,
            'FEB' => 0,
            'FKES' => 0,
        ];

        $allSubs = Submission::all();
        foreach ($allSubs as $s) {
            if ($s->created_at) {
                $m = (int) $s->created_at->format('n');
                if (isset($monthlyCounts[$m])) {
                    $monthlyCounts[$m]++;
                }
            }
            $fac = $s->fakultas_id ?? 'FTI';
            if (isset($facultyCounts[$fac])) {
                $facultyCounts[$fac]++;
            } else {
                $facultyCounts['FTI']++;
            }
        }

        $chartMonthlyLabels = $monthNames;
        $chartMonthlyData = array_values($monthlyCounts);

        $chartFacultyLabels = ['Fakultas Teknologi Informasi (FTI)', 'Fakultas Agama Islam (FAI)', 'Fakultas Ekonomi & Bisnis (FEB)', 'Fakultas Kesehatan (FKES)'];
        $chartFacultyData = array_values($facultyCounts);

        // 3. Aktivitas Terbaru dari Audit Trail
        try {
            $recentLogs = AuditLog::latest('id')->take(8)->get();
        } catch (\Throwable) {
            $recentLogs = collect();
        }

        return view('admin.dashboard', compact(
            'admin',
            'totalSubmissions',
            'totalPending',
            'totalCompleted',
            'totalAchievements',
            'totalApprovedBudget',
            'totalDisbursed',
            'chartMonthlyLabels',
            'chartMonthlyData',
            'chartFacultyLabels',
            'chartFacultyData',
            'recentLogs'
        ));
    }
}
