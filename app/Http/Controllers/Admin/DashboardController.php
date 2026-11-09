<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Finance\BudgetRequest;
use App\Models\Submission\AchievementCertificate;
use App\Models\Submission\Submission;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function dashboard(Request $request)
    {
        $admin = $request->user();

        // 1. Metrik Global Universitas
        $totalSubmissions = Submission::count();

        $totalReviewKaprodi = Submission::whereIn('status', [
            'submitted', 'kaprodi_review', 'dosen_review', 'review_kaprodi', 'review_dosen',
        ])->count();

        $totalReviewDekan = Submission::whereIn('status', [
            'dekan_review', 'approved_kaprodi', 'approved_dosen', 'review_dekan',
        ])->count();

        $totalWaitingLpj = Submission::whereIn('status', [
            'approved', 'approved_final', 'waiting_lpj', 'review_lpj', 'lpj_review',
        ])->count();

        $totalCompleted = Submission::where('status', 'completed')->count();

        try {
            $totalBudgetProposed = (float) (BudgetRequest::sum('total_proposed') ?: Submission::sum('total_budget_requested'));
            $totalBudgetApproved = (float) (BudgetRequest::sum('total_approved') ?: Submission::sum('total_budget_approved'));
        } catch (\Throwable) {
            $totalBudgetProposed = (float) (Submission::sum('total_budget_requested') ?: 0);
            $totalBudgetApproved = (float) (Submission::sum('total_budget_approved') ?: 0);
        }

        try {
            $totalSkpiPoints = (float) (AchievementCertificate::sum('skpi_points_awarded') ?: 0);
        } catch (\Throwable) {
            $totalSkpiPoints = 0;
        }

        // 2. Daftar Pengajuan Menyeluruh dengan Pencarian & Filter
        $query = Submission::with(['competitionDetail', 'members', 'certificates'])->latest('id');

        if ($statusFilter = $request->query('status')) {
            if ($statusFilter === 'kaprodi') {
                $query->whereIn('status', ['submitted', 'kaprodi_review', 'dosen_review', 'review_kaprodi', 'review_dosen']);
            } elseif ($statusFilter === 'dekan') {
                $query->whereIn('status', ['dekan_review', 'approved_kaprodi', 'approved_dosen', 'review_dekan']);
            } elseif ($statusFilter === 'lpj') {
                $query->whereIn('status', ['approved', 'approved_final', 'waiting_lpj', 'review_lpj', 'lpj_review']);
            } elseif ($statusFilter === 'completed') {
                $query->where('status', 'completed');
            } elseif ($statusFilter === 'rejected') {
                $query->where('status', 'rejected');
            }
        }

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('submitter_name', 'like', "%{$search}%")
                    ->orWhere('submitter_nim', 'like', "%{$search}%")
                    ->orWhere('team_name', 'like', "%{$search}%")
                    ->orWhere('study_program_name', 'like', "%{$search}%");
            });
        }

        $submissions = $query->paginate(12)->withQueryString();

        // 3. Jejak Aktivitas / Audit Log Terbaru Sistem
        try {
            $auditLogs = AuditLog::latest('id')->take(10)->get();
        } catch (\Throwable) {
            $auditLogs = collect();
        }

        return view('admin.dashboard', compact(
            'admin',
            'totalSubmissions',
            'totalReviewKaprodi',
            'totalReviewDekan',
            'totalWaitingLpj',
            'totalCompleted',
            'totalBudgetProposed',
            'totalBudgetApproved',
            'totalSkpiPoints',
            'submissions',
            'auditLogs',
            'statusFilter'
        ));
    }
}
