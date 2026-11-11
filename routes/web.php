<?php

use App\Http\Controllers\Admin\AuditTrailController as AdminAuditController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ReportController as AdminReportController;
use App\Http\Controllers\Admin\SubmissionMonitorController as AdminSubmissionController;
use App\Http\Controllers\Admin\UserManagementController as AdminUserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Dekan\ApprovalController as DekanApprovalController;
use App\Http\Controllers\Kaprodi\ApprovalController as KaprodiApprovalController;
use App\Http\Controllers\Keuangan\FinanceController;
use App\Http\Controllers\Mahasiswa\SubmissionController as MahasiswaSubmissionController;
use App\Http\Controllers\SubmissionProposalController;
use App\Http\Controllers\Warek\WarekTwoController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - SIPRES UNUJA
|--------------------------------------------------------------------------
*/

// Redirect root to login / dashboard
Route::get('/', [AuthController::class, 'showLogin'])->name('home');

// Authentication & Demo Role Switcher
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/demo-login/{role}', [AuthController::class, 'demoLogin'])->name('demo.login');

/*
|--------------------------------------------------------------------------
| 1. Portal Mahasiswa
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:mahasiswa'])->prefix('mahasiswa')->name('mahasiswa.')->group(function () {
    Route::get('/dashboard', [MahasiswaSubmissionController::class, 'dashboard'])->name('dashboard');

    // Pengajuan Baru (Dilindungi Cekal LPJ)
    Route::middleware('check.lpj')->group(function () {
        Route::get('/pengajuan/baru', [MahasiswaSubmissionController::class, 'create'])->name('submissions.create');
        Route::post('/pengajuan', [MahasiswaSubmissionController::class, 'store'])->name('submissions.store');
    });

    // Detail & Tracking Pengajuan
    Route::get('/pengajuan/{submission}', [MahasiswaSubmissionController::class, 'show'])->name('submissions.show');
    Route::get('/pengajuan/{submission}/proposal', [SubmissionProposalController::class, 'show'])->name('submissions.proposal');

    // Perbaikan Revisi
    Route::get('/pengajuan/{submission}/edit', [MahasiswaSubmissionController::class, 'edit'])->name('submissions.edit');
    Route::post('/pengajuan/{submission}/resubmit', [MahasiswaSubmissionController::class, 'resubmit'])->name('submissions.resubmit');

    // Pelaporan LPJ & Prestasi
    Route::get('/pengajuan/{submission}/lpj', [MahasiswaSubmissionController::class, 'lpjForm'])->name('submissions.lpj');
    Route::post('/pengajuan/{submission}/lpj', [MahasiswaSubmissionController::class, 'submitLpj'])->name('submissions.lpj.submit');
});

/*
|--------------------------------------------------------------------------
| 2. Portal Kaprodi (Ketua Program Studi)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:kaprodi,dosen'])->prefix('kaprodi')->name('kaprodi.')->group(function () {
    Route::get('/dashboard', [KaprodiApprovalController::class, 'dashboard'])->name('dashboard');
    Route::get('/persetujuan/{submission}', [KaprodiApprovalController::class, 'show'])->name('show');
    Route::get('/persetujuan/{submission}/proposal', [SubmissionProposalController::class, 'show'])->name('proposal');
    Route::post('/persetujuan/{submission}', [KaprodiApprovalController::class, 'process'])->name('process');
    Route::post('/persetujuan/{submission}/update-dosen', [KaprodiApprovalController::class, 'updateSupervisor'])->name('updateSupervisor');
});

// Alias kompatibilitas portal dosen
Route::middleware(['auth', 'role:kaprodi,dosen'])->prefix('dosen')->name('dosen.')->group(function () {
    Route::get('/dashboard', [KaprodiApprovalController::class, 'dashboard'])->name('dashboard');
    Route::get('/verifikasi/{submission}', [KaprodiApprovalController::class, 'show'])->name('show');
    Route::get('/verifikasi/{submission}/proposal', [SubmissionProposalController::class, 'show'])->name('proposal');
    Route::post('/verifikasi/{submission}', [KaprodiApprovalController::class, 'process'])->name('process');
    Route::post('/verifikasi/{submission}/update-dosen', [KaprodiApprovalController::class, 'updateSupervisor'])->name('updateSupervisor');
});

/*
|--------------------------------------------------------------------------
| 3. Portal Dekan Fakultas (Persetujuan Final, Dana, & Validasi LPJ)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:dekan'])->prefix('dekan')->name('dekan.')->group(function () {
    Route::get('/dashboard', [DekanApprovalController::class, 'dashboard'])->name('dashboard');
    Route::get('/persetujuan/{submission}', [DekanApprovalController::class, 'show'])->name('show');
    Route::get('/persetujuan/{submission}/proposal', [SubmissionProposalController::class, 'show'])->name('proposal');
    Route::post('/persetujuan/{submission}', [DekanApprovalController::class, 'process'])->name('process');
    Route::get('/validasi-lpj/{submission}', [DekanApprovalController::class, 'showLpj'])->name('lpj.show');
    Route::post('/validasi-lpj/{submission}', [DekanApprovalController::class, 'processLpj'])->name('lpj.process');
    Route::get('/laporan-prestasi', [DekanApprovalController::class, 'reports'])->name('reports');
});

/*
|--------------------------------------------------------------------------
| 4. Portal Keuangan (Verifikasi Anggaran, Pencairan Dana, & Audit LPJ)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:keuangan'])->prefix('keuangan')->name('keuangan.')->group(function () {
    Route::get('/dashboard', [FinanceController::class, 'dashboard'])->name('dashboard');
    Route::get('/verifikasi/{submission}', [FinanceController::class, 'show'])->name('verify.show');
    Route::post('/verifikasi/{submission}', [FinanceController::class, 'verifyBudget'])->name('verify.process');
    Route::post('/pencairan/{submission}', [FinanceController::class, 'disburse'])->name('disburse.process');
    Route::get('/lpj/{submission}', [FinanceController::class, 'lpjShow'])->name('lpj.show');
    Route::post('/lpj/{submission}', [FinanceController::class, 'verifyLpj'])->name('lpj.process');
    Route::get('/laporan', [FinanceController::class, 'reports'])->name('reports');
});

/*
|--------------------------------------------------------------------------
| 5. Portal Wakil Rektor II / Warek II (Persetujuan Final & Anggaran Universitas)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:warek,warek_ii,warek2'])->prefix('warek2')->name('warek2.')->group(function () {
    Route::get('/dashboard', [WarekTwoController::class, 'dashboard'])->name('dashboard');
    Route::get('/persetujuan/{submission}', [WarekTwoController::class, 'show'])->name('show');
    Route::post('/persetujuan/{submission}', [WarekTwoController::class, 'process'])->name('process');
    Route::get('/monitoring-anggaran', [WarekTwoController::class, 'monitoringBudget'])->name('monitoring.budget');
    Route::get('/monitoring-prestasi', [WarekTwoController::class, 'monitoringAchievements'])->name('monitoring.achievements');
});

// Alias & redirect kompatibilitas warek lama
Route::redirect('/warek', '/warek2/dashboard')->name('warek.dashboard');
Route::any('/warek/{any?}', fn () => redirect()->route('warek2.dashboard'))->where('any', '.*');

/*
|--------------------------------------------------------------------------
| 6. Portal Administrator (Super Observer / Multi-Portal Hub)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // 1. Dashboard
    Route::get('/', fn () => redirect()->route('admin.dashboard'));
    Route::get('/dashboard', [AdminDashboardController::class, 'dashboard'])->name('dashboard');

    // 2. Pengajuan Lomba
    Route::get('/pengajuan', [AdminSubmissionController::class, 'index'])->name('submissions.index');
    Route::get('/pengajuan/{submission}', [AdminSubmissionController::class, 'show'])->name('submissions.show');

    // 3. Manajemen Pengguna
    Route::get('/pengguna', [AdminUserController::class, 'index'])->name('users.index');
    Route::post('/pengguna', [AdminUserController::class, 'store'])->name('users.store');
    Route::put('/pengguna/{user}', [AdminUserController::class, 'update'])->name('users.update');
    Route::post('/pengguna/{user}/toggle', [AdminUserController::class, 'toggleStatus'])->name('users.toggle');
    Route::post('/pengguna/{user}/reset-password', [AdminUserController::class, 'resetPassword'])->name('users.resetPassword');

    // 4. Audit Trail
    Route::get('/audit-trail', [AdminAuditController::class, 'index'])->name('audit.index');

    // 5. Laporan
    Route::get('/laporan', [AdminReportController::class, 'index'])->name('reports.index');
});
