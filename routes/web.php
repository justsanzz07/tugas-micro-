<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Dekan\ApprovalController as DekanApprovalController;
use App\Http\Controllers\Kaprodi\ApprovalController as KaprodiApprovalController;
use App\Http\Controllers\Mahasiswa\SubmissionController as MahasiswaSubmissionController;
use App\Http\Controllers\SubmissionProposalController;
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
| 4. Portal Administrator (Super Observer / Multi-Portal Hub)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'dashboard'])->name('dashboard');
});

// Redirect warek route ke portal dekan jika masih ada yang mengakses
Route::any('/warek/{any?}', fn () => redirect()->route('dekan.dashboard'))->where('any', '.*');
