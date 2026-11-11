@extends('layouts.admin')

@section('title', 'Dashboard Administrator — SIPRES UNUJA')
@section('breadcrumb', 'Dashboard')
@section('header_title', 'Pusat Kendali Administrator')

@section('content')
<div class="space-y-6">

    <!-- A. WELCOME BANNER (NAVY BLUE ELEGAN) -->
    <div class="bg-gradient-to-r from-[#172554] via-[#1e3a8a] to-[#172554] rounded-2xl p-6 sm:p-7 text-white shadow-md relative overflow-hidden">
        <div class="absolute -right-8 -bottom-8 opacity-10 text-9xl pointer-events-none">
            <i class="fa-solid fa-shield-halved"></i>
        </div>

        <div class="relative z-10 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
            <div>
                <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-blue-500/20 border border-blue-400/30 text-blue-200 text-xs font-bold uppercase tracking-wider mb-2.5">
                    <i class="fa-solid fa-crown text-amber-400"></i>
                    <span>Super Observer & Multi-Portal Hub</span>
                </div>
                <h1 class="text-xl sm:text-2xl font-black tracking-tight text-white">
                    Pusat Kendali & Peninjauan Administrator
                </h1>
                <p class="text-xs sm:text-sm text-blue-200 mt-1 max-w-2xl leading-relaxed">
                    Selamat datang di pusat monitoring Sistem Informasi Pengajuan Lomba dan Prestasi Mahasiswa Universitas Nurul Jadid. Anda dapat memantau seluruh data operasional, statistik, dan riwayat aktivitas secara transparan.
                </p>
                <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-blue-300 mt-3 font-medium">
                    <span class="flex items-center gap-1.5 text-white">
                        <i class="fa-solid fa-user-circle text-blue-300"></i>
                        <span>Administrator: <strong>{{ $admin->name }}</strong></span>
                    </span>
                    <span class="text-blue-400">•</span>
                    <span class="flex items-center gap-1.5">
                        <i class="fa-solid fa-calendar-day text-blue-300"></i>
                        <span>{{ now()->translatedFormat('l, d F Y') }}</span>
                    </span>
                </div>
            </div>

            <div class="flex items-center space-x-3 bg-white/10 backdrop-blur-md px-4 py-3 rounded-2xl border border-white/15 shrink-0 self-start md:self-center">
                <div class="w-3 h-3 rounded-full bg-emerald-400 animate-ping mr-1"></div>
                <div>
                    <div class="text-[10px] uppercase font-bold text-blue-200">Status Sistem</div>
                    <div class="text-xs font-black text-white">Sistem Normal &amp; Berjalan (Live)</div>
                </div>
            </div>
        </div>
    </div>

    <!-- B. STATISTIK PENGAJUAN & FINANSIAL UNIVERSITAS (6 KARTU EKSEKUTIF) -->
    <div>
        <div class="flex items-center justify-between mb-3">
            <h2 class="text-xs font-extrabold uppercase tracking-wider text-slate-500 flex items-center space-x-2">
                <i class="fa-solid fa-chart-simple text-[#2454C5]"></i>
                <span>Metrik Operasional &amp; Keuangan Universitas</span>
            </h2>
            <span class="text-[11px] text-slate-400">Data sinkronisasi real-time multi-database</span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4">
            <!-- 1. Total Pengajuan -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-4 shadow-2xs hover:shadow-xs transition">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-slate-500">Total Pengajuan</span>
                    <div class="w-8 h-8 rounded-xl bg-blue-50 text-[#2454C5] flex items-center justify-center text-sm">
                        <i class="fa-solid fa-file-invoice"></i>
                    </div>
                </div>
                <div class="text-2xl font-black text-slate-900 mt-2">{{ $totalSubmissions }}</div>
                <span class="text-[11px] text-slate-400 block mt-0.5">Semua proposal masuk</span>
            </div>

            <!-- 2. Menunggu Persetujuan -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-4 shadow-2xs hover:shadow-xs transition">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-slate-500">Menunggu Review</span>
                    <div class="w-8 h-8 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-sm">
                        <i class="fa-solid fa-clock-rotate-left"></i>
                    </div>
                </div>
                <div class="text-2xl font-black text-amber-600 mt-2">{{ $totalPending }}</div>
                <span class="text-[11px] text-slate-400 block mt-0.5">Dalam tahap alur</span>
            </div>

            <!-- 3. Pengajuan Selesai -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-4 shadow-2xs hover:shadow-xs transition">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-slate-500">Pengajuan Selesai</span>
                    <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm">
                        <i class="fa-solid fa-circle-check"></i>
                    </div>
                </div>
                <div class="text-2xl font-black text-emerald-700 mt-2">{{ $totalCompleted }}</div>
                <span class="text-[11px] text-slate-400 block mt-0.5">LPJ tervalidasi</span>
            </div>

            <!-- 4. Total Prestasi -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-4 shadow-2xs hover:shadow-xs transition">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-slate-500">Total Prestasi</span>
                    <div class="w-8 h-8 rounded-xl bg-purple-50 text-purple-700 flex items-center justify-center text-sm">
                        <i class="fa-solid fa-trophy"></i>
                    </div>
                </div>
                <div class="text-2xl font-black text-purple-800 mt-2">{{ $totalAchievements }}</div>
                <span class="text-[11px] text-slate-400 block mt-0.5">Capaian &amp; sertifikat</span>
            </div>

            <!-- 5. Total Dana Disetujui -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-4 shadow-2xs hover:shadow-xs transition">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-slate-500">Dana Disetujui</span>
                    <div class="w-8 h-8 rounded-xl bg-indigo-50 text-indigo-700 flex items-center justify-center text-sm">
                        <i class="fa-solid fa-coins"></i>
                    </div>
                </div>
                <div class="text-lg font-black text-indigo-900 mt-2 truncate">Rp {{ number_format($totalApprovedBudget, 0, ',', '.') }}</div>
                <span class="text-[11px] text-slate-400 block mt-0.5">Plafon resmi Warek II</span>
            </div>

            <!-- 6. Total Dana Dicairkan -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-4 shadow-2xs hover:shadow-xs transition">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-slate-500">Dana Dicairkan</span>
                    <div class="w-8 h-8 rounded-xl bg-teal-50 text-teal-700 flex items-center justify-center text-sm">
                        <i class="fa-solid fa-wallet"></i>
                    </div>
                </div>
                <div class="text-lg font-black text-teal-800 mt-2 truncate">Rp {{ number_format($totalDisbursed, 0, ',', '.') }}</div>
                <span class="text-[11px] text-slate-400 block mt-0.5">Kas riil ditransfer</span>
            </div>
        </div>
    </div>

    <!-- C. 2 GRAFIK RINGKASAN (TREN BULANAN & DISTRIBUSI FAKULTAS) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Grafik 1: Tren Pengajuan Lomba Per Bulan -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200/80 p-5 shadow-2xs">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center space-x-2.5">
                    <div class="w-2.5 h-5 bg-[#2454C5] rounded-full"></div>
                    <div>
                        <h3 class="text-sm font-extrabold text-slate-800">Tren Pengajuan Lomba Per Bulan</h3>
                        <p class="text-xs text-slate-400">Jumlah proposal delegasi mahasiswa sepanjang tahun {{ date('Y') }}</p>
                    </div>
                </div>
                <span class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-slate-50 text-slate-600 border border-slate-200">
                    Tahun {{ date('Y') }}
                </span>
            </div>
            <div class="h-64 relative">
                <canvas id="monthlyTrendChart"></canvas>
            </div>
        </div>

        <!-- Grafik 2: Distribusi Pengajuan Berdasarkan Fakultas -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-2xs">
            <div class="flex items-center space-x-2.5 mb-4">
                <div class="w-2.5 h-5 bg-purple-600 rounded-full"></div>
                <div>
                    <h3 class="text-sm font-extrabold text-slate-800">Distribusi Fakultas</h3>
                    <p class="text-xs text-slate-400">Proporsi keikutsertaan per fakultas</p>
                </div>
            </div>
            <div class="h-64 relative flex items-center justify-center">
                <canvas id="facultyDistChart"></canvas>
            </div>
        </div>

    </div>

    <!-- D. AKSES CEPAT PORTAL (MODE PENINJAUAN AMAN / READ-ONLY) -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-2xs space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-100 pb-3">
            <div>
                <h3 class="text-sm font-extrabold text-slate-800 flex items-center space-x-2">
                    <i class="fa-solid fa-door-open text-[#2454C5]"></i>
                    <span>Akses Cepat Portal (Mode Peninjauan Observer)</span>
                </h3>
                <p class="text-xs text-slate-500">Buka dan tinjau tampilan setiap role sistem secara langsung dalam mode aman tanpa dapat mengubah keputusan.</p>
            </div>
            <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-blue-50 text-blue-700 border border-blue-200 self-start sm:self-auto">
                <i class="fa-solid fa-eye mr-1"></i> Read-Only Observer
            </span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3.5">
            <!-- 1. Portal Mahasiswa -->
            <a href="{{ route('mahasiswa.dashboard') }}"
                class="p-4 rounded-xl border border-slate-200 hover:border-blue-500 bg-slate-50/50 hover:bg-white transition group flex flex-col justify-between">
                <div>
                    <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center text-lg mb-3 group-hover:scale-105 transition-transform">
                        <i class="fa-solid fa-user-graduate"></i>
                    </div>
                    <h4 class="font-bold text-xs text-slate-800 group-hover:text-blue-700">Portal Mahasiswa</h4>
                    <p class="text-[11px] text-slate-500 mt-1 leading-snug">Formulir usulan lomba, tracking, dan unduh surat tugas.</p>
                </div>
                <div class="text-[10px] font-bold text-blue-600 mt-3 flex items-center space-x-1">
                    <span>Tinjau Portal</span>
                    <i class="fa-solid fa-arrow-right text-[9px] group-hover:translate-x-1 transition-transform"></i>
                </div>
            </a>

            <!-- 2. Portal Kaprodi -->
            <a href="{{ route('kaprodi.dashboard') }}"
                class="p-4 rounded-xl border border-slate-200 hover:border-sky-500 bg-slate-50/50 hover:bg-white transition group flex flex-col justify-between">
                <div>
                    <div class="w-10 h-10 rounded-xl bg-sky-100 text-sky-700 flex items-center justify-center text-lg mb-3 group-hover:scale-105 transition-transform">
                        <i class="fa-solid fa-user-tie"></i>
                    </div>
                    <h4 class="font-bold text-xs text-slate-800 group-hover:text-sky-700">Portal Kaprodi</h4>
                    <p class="text-[11px] text-slate-500 mt-1 leading-snug">Verifikasi awal &amp; penunjukan dosen pendamping.</p>
                </div>
                <div class="text-[10px] font-bold text-sky-600 mt-3 flex items-center space-x-1">
                    <span>Tinjau Portal</span>
                    <i class="fa-solid fa-arrow-right text-[9px] group-hover:translate-x-1 transition-transform"></i>
                </div>
            </a>

            <!-- 3. Portal Dekan -->
            <a href="{{ route('dekan.dashboard') }}"
                class="p-4 rounded-xl border border-slate-200 hover:border-indigo-500 bg-slate-50/50 hover:bg-white transition group flex flex-col justify-between">
                <div>
                    <div class="w-10 h-10 rounded-xl bg-indigo-100 text-indigo-700 flex items-center justify-center text-lg mb-3 group-hover:scale-105 transition-transform">
                        <i class="fa-solid fa-building-columns"></i>
                    </div>
                    <h4 class="font-bold text-xs text-slate-800 group-hover:text-indigo-700">Portal Dekan</h4>
                    <p class="text-[11px] text-slate-500 mt-1 leading-snug">Persetujuan fakultas &amp; rekomendasi pagu dana.</p>
                </div>
                <div class="text-[10px] font-bold text-indigo-600 mt-3 flex items-center space-x-1">
                    <span>Tinjau Portal</span>
                    <i class="fa-solid fa-arrow-right text-[9px] group-hover:translate-x-1 transition-transform"></i>
                </div>
            </a>

            <!-- 4. Portal Keuangan -->
            <a href="{{ route('keuangan.dashboard') }}"
                class="p-4 rounded-xl border border-slate-200 hover:border-emerald-500 bg-slate-50/50 hover:bg-white transition group flex flex-col justify-between">
                <div>
                    <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center text-lg mb-3 group-hover:scale-105 transition-transform">
                        <i class="fa-solid fa-calculator"></i>
                    </div>
                    <h4 class="font-bold text-xs text-slate-800 group-hover:text-emerald-700">Portal Keuangan</h4>
                    <p class="text-[11px] text-slate-500 mt-1 leading-snug">Telaah RAB, pencairan kas bank, &amp; audit kuitansi LPJ.</p>
                </div>
                <div class="text-[10px] font-bold text-emerald-600 mt-3 flex items-center space-x-1">
                    <span>Tinjau Portal</span>
                    <i class="fa-solid fa-arrow-right text-[9px] group-hover:translate-x-1 transition-transform"></i>
                </div>
            </a>

            <!-- 5. Portal Warek II -->
            <a href="{{ route('warek2.dashboard') }}"
                class="p-4 rounded-xl border border-slate-200 hover:border-purple-500 bg-slate-50/50 hover:bg-white transition group flex flex-col justify-between">
                <div>
                    <div class="w-10 h-10 rounded-xl bg-purple-100 text-purple-700 flex items-center justify-center text-lg mb-3 group-hover:scale-105 transition-transform">
                        <i class="fa-solid fa-crown"></i>
                    </div>
                    <h4 class="font-bold text-xs text-slate-800 group-hover:text-purple-700">Portal Warek II</h4>
                    <p class="text-[11px] text-slate-500 mt-1 leading-snug">Persetujuan final universitas &amp; penetapan dana definitif.</p>
                </div>
                <div class="text-[10px] font-bold text-purple-600 mt-3 flex items-center space-x-1">
                    <span>Tinjau Portal</span>
                    <i class="fa-solid fa-arrow-right text-[9px] group-hover:translate-x-1 transition-transform"></i>
                </div>
            </a>
        </div>
    </div>

    <!-- E. AKTIVITAS TERBARU (AUDIT TRAIL PREVIEW) -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-2xs overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center space-x-2.5">
                <div class="w-2.5 h-5 bg-slate-700 rounded-full"></div>
                <div>
                    <h3 class="text-sm font-extrabold text-slate-800">Aktivitas Terbaru Sistem (Audit Trail)</h3>
                    <p class="text-xs text-slate-400">Jejak log persetujuan dan transaksi terbaru dari database</p>
                </div>
            </div>
            <a href="{{ route('admin.audit.index') }}" class="text-xs font-bold text-[#2454C5] hover:underline flex items-center space-x-1">
                <span>Lihat Semua</span>
                <i class="fa-solid fa-chevron-right text-[10px]"></i>
            </a>
        </div>

        @if($recentLogs->isEmpty())
            <div class="p-8 text-center text-xs text-slate-400">
                Belum ada catatan aktivitas baru dalam log sistem.
            </div>
        @else
            <div class="divide-y divide-slate-100 text-xs">
                @foreach($recentLogs as $log)
                    <div class="p-4 hover:bg-slate-50/60 transition flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 rounded-lg bg-blue-50 text-[#2454C5] flex items-center justify-center text-xs font-bold shrink-0">
                                <i class="fa-solid fa-clock-rotate-left"></i>
                            </div>
                            <div>
                                <div class="flex items-center space-x-2">
                                    <span class="font-extrabold text-slate-800">{{ $log->user_name ?? 'Sistem' }}</span>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-slate-100 text-slate-600">
                                        {{ $log->user_role ?? 'User' }}
                                    </span>
                                </div>
                                <p class="text-[11px] text-slate-500 mt-0.5">
                                    Melakukan aksi <strong>{{ $log->action }}</strong> pada modul <code>{{ $log->resource_type ?? 'submission' }}</code>
                                    @if($log->resource_id) (ID: {{ $log->resource_id }}) @endif
                                </p>
                            </div>
                        </div>
                        <div class="text-right text-[11px] text-slate-400 shrink-0">
                            <span>{{ $log->created_at ? $log->created_at->diffForHumans() : '-' }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Chart Tren Bulanan
    const ctxMonthly = document.getElementById('monthlyTrendChart');
    if (ctxMonthly) {
        new Chart(ctxMonthly, {
            type: 'bar',
            data: {
                labels: {!! json_encode($chartMonthlyLabels) !!},
                datasets: [{
                    label: 'Pengajuan Lomba',
                    data: {!! json_encode($chartMonthlyData) !!},
                    backgroundColor: '#2454C5',
                    borderRadius: 6,
                    maxBarThickness: 32,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1, font: { size: 10 } },
                        grid: { color: '#F1F5F9' }
                    },
                    x: {
                        ticks: { font: { size: 10 } },
                        grid: { display: false }
                    }
                }
            }
        });
    }

    // 2. Chart Distribusi Fakultas
    const ctxFaculty = document.getElementById('facultyDistChart');
    if (ctxFaculty) {
        new Chart(ctxFaculty, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($chartFacultyLabels) !!},
                datasets: [{
                    data: {!! json_encode($chartFacultyData) !!},
                    backgroundColor: ['#2454C5', '#0284C7', '#7C3AED', '#059669'],
                    borderWidth: 2,
                    borderColor: '#FFFFFF',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { boxWidth: 10, font: { size: 10 } }
                    }
                },
                cutout: '65%'
            }
        });
    }
});
</script>
@endpush
