@extends('layouts.app')

@section('title', 'Dashboard Wakil Rektor II — SIPRES UNUJA')

@section('content')
<div class="space-y-6">

    <!-- 1. KARTU PROFIL WAREK II (UNGU EKSEKUTIF ELEGAN) -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-5 sm:p-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4 relative overflow-hidden">
        <div class="absolute -right-16 -top-16 w-56 h-56 rounded-full bg-purple-50 pointer-events-none"></div>

        <div class="flex items-center space-x-4 relative z-10">
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-[#7E22CE] via-[#6B21A8] to-[#4C1D95] text-white flex items-center justify-center text-xl font-bold shadow-md shadow-purple-500/20 shrink-0">
                <i class="fa-solid fa-crown text-amber-300"></i>
            </div>
            <div>
                <div class="flex flex-wrap items-center gap-2">
                    <h1 class="text-xl font-extrabold text-slate-800 tracking-tight">{{ auth()->user()->name }}</h1>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-[#F3E8FF] text-[#7E22CE] border border-purple-200">
                        Wakil Rektor II (Administrasi Umum & Keuangan)
                    </span>
                </div>
                <div class="flex flex-wrap items-center gap-x-2.5 gap-y-1 text-xs text-slate-500 mt-1">
                    <span class="flex items-center gap-1 font-medium text-slate-700">
                        <i class="fa-solid fa-building-columns text-slate-400"></i>
                        <span>Universitas Nurul Jadid</span>
                    </span>
                    <span class="text-slate-300">•</span>
                    <span class="font-medium text-slate-600">NIDN: {{ auth()->user()->identifier ?? '0701017503' }}</span>
                </div>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-2 relative z-10">
            <a href="{{ route('warek2.monitoring.budget') }}"
                class="inline-flex items-center space-x-2 bg-gradient-to-r from-[#7E22CE] to-[#581C87] hover:from-[#6B21A8] hover:to-[#4C1D95] text-white font-bold px-4 py-2.5 rounded-xl text-xs transition shadow-sm hover:shadow">
                <i class="fa-solid fa-chart-line text-purple-200"></i>
                <span>Monitoring Anggaran Fakultas</span>
                <i class="fa-solid fa-chevron-right text-[10px]"></i>
            </a>
            <a href="{{ route('warek2.monitoring.achievements') }}"
                class="inline-flex items-center space-x-2 bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 font-bold px-3.5 py-2.5 rounded-xl text-xs transition shadow-xs">
                <i class="fa-solid fa-trophy text-amber-500"></i>
                <span>Prestasi Universitas</span>
            </a>
        </div>
    </div>

    <!-- 2. KARTU STATISTIK WAREK II (4 KARTU EKSEKUTIF) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Kartu 1: Menunggu Persetujuan Final -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-4 sm:p-5 flex items-center justify-between transition hover:shadow-sm">
            <div>
                <span class="text-xs font-semibold text-slate-500 block">Menunggu Persetujuan</span>
                <span class="text-3xl font-black text-slate-800 mt-1 block leading-tight">{{ $totalPendingFinal }}</span>
                <span class="text-[11px] text-[#7E22CE] font-medium mt-0.5 block flex items-center gap-1">
                    <i class="fa-solid fa-stamp"></i> Tahap Akhir Universitas
                </span>
            </div>
            <div class="w-11 h-11 rounded-xl bg-purple-50 text-[#7E22CE] flex items-center justify-center text-lg shrink-0 border border-purple-100">
                <i class="fa-solid fa-clipboard-check"></i>
            </div>
        </div>

        <!-- Kartu 2: Total Anggaran Disetujui -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-4 sm:p-5 flex items-center justify-between transition hover:shadow-sm">
            <div>
                <span class="text-xs font-semibold text-slate-500 block">Plafon Disetujui</span>
                <span class="text-2xl font-black text-indigo-900 mt-1 block leading-tight">Rp {{ number_format($totalApprovedFunding, 0, ',', '.') }}</span>
                <span class="text-[11px] text-indigo-600 font-medium mt-0.5 block flex items-center gap-1">
                    <i class="fa-solid fa-check-double"></i> Universitas Total
                </span>
            </div>
            <div class="w-11 h-11 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-lg shrink-0 border border-indigo-100">
                <i class="fa-solid fa-coins"></i>
            </div>
        </div>

        <!-- Kartu 3: Total Dana Dicairkan -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-4 sm:p-5 flex items-center justify-between transition hover:shadow-sm">
            <div>
                <span class="text-xs font-semibold text-slate-500 block">Realisasi Pencairan</span>
                <span class="text-2xl font-black text-emerald-700 mt-1 block leading-tight">Rp {{ number_format($totalDisbursed, 0, ',', '.') }}</span>
                <span class="text-[11px] text-emerald-600 font-medium mt-0.5 block flex items-center gap-1">
                    <i class="fa-solid fa-receipt"></i> Kas Dicairkan
                </span>
            </div>
            <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg shrink-0 border border-emerald-100">
                <i class="fa-solid fa-wallet"></i>
            </div>
        </div>

        <!-- Kartu 4: Total Prestasi Universitas -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-4 sm:p-5 flex items-center justify-between transition hover:shadow-sm">
            <div>
                <span class="text-xs font-semibold text-slate-500 block">Prestasi Terverifikasi</span>
                <span class="text-3xl font-black text-amber-600 mt-1 block leading-tight">{{ $totalAchievements }}</span>
                <span class="text-[11px] text-amber-600 font-medium mt-0.5 block flex items-center gap-1">
                    <i class="fa-solid fa-trophy"></i> Capaian Juara
                </span>
            </div>
            <div class="w-11 h-11 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-lg shrink-0 border border-amber-100">
                <i class="fa-solid fa-award"></i>
            </div>
        </div>
    </div>

    <!-- 3. ANTREAN PERSETUJUAN FINAL TINGKAT UNIVERSITAS -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="flex items-center space-x-3">
                <div class="w-2.5 h-6 bg-[#7E22CE] rounded-full"></div>
                <div>
                    <h2 class="text-base font-extrabold text-slate-800">Antrean Persetujuan Final Universitas & Penetapan Plafon</h2>
                    <p class="text-xs text-slate-500">Proposal yang telah lolos telaah Keuangan dan memerlukan persetujuan akhir serta penetapan dana oleh Wakil Rektor II.</p>
                </div>
            </div>
            <span class="px-3 py-1 rounded-full text-xs font-bold bg-purple-50 text-purple-700 border border-purple-200 self-start sm:self-auto">
                <i class="fa-solid fa-bell mr-1"></i> {{ $totalPendingFinal }} Menunggu Keputusan
            </span>
        </div>

        @if($pendingSubmissions->isEmpty())
            <div class="p-12 text-center">
                <div class="w-14 h-14 mx-auto rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center text-xl mb-3">
                    <i class="fa-solid fa-shield-check"></i>
                </div>
                <h3 class="text-sm font-bold text-slate-700">Tidak Ada Antrean Persetujuan Warek II</h3>
                <p class="text-xs text-slate-400 mt-1">Seluruh berkas pengajuan telah diputuskan secara tuntas.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-slate-50/70 border-b border-slate-200 text-slate-500 font-bold uppercase tracking-wider text-[11px]">
                            <th class="py-3 px-4">Kode & Tanggal</th>
                            <th class="py-3 px-4">Fakultas & Prodi</th>
                            <th class="py-3 px-4">Ketua & Anggota Tim</th>
                            <th class="py-3 px-4">Nama Lomba & Tingkat</th>
                            <th class="py-3 px-4 text-right">Rekomendasi Plafon</th>
                            <th class="py-3 px-4 text-center">Verifikasi Keuangan</th>
                            <th class="py-3 px-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($pendingSubmissions as $item)
                            <tr class="hover:bg-purple-50/20 transition">
                                <td class="py-3.5 px-4">
                                    <span class="font-extrabold text-purple-900 block">{{ $item->code }}</span>
                                    <span class="text-[11px] text-slate-400 block">{{ $item->created_at ? $item->created_at->format('d M Y') : '-' }}</span>
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="font-bold text-slate-800 block">{{ $item->fakultas_id }}</span>
                                    <span class="text-[11px] text-slate-500 block">{{ $item->study_program_name ?? $item->study_program_id }}</span>
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="font-bold text-slate-800 block">{{ $item->submitter_name }}</span>
                                    <span class="text-[11px] text-slate-500 block">NIM: {{ $item->submitter_nim }}</span>
                                </td>
                                <td class="py-3.5 px-4 max-w-xs">
                                    <span class="font-bold text-slate-800 block truncate">{{ $item->competitionDetail?->name ?? $item->title }}</span>
                                    <span class="inline-block mt-1 px-2 py-0.5 rounded text-[10px] font-semibold bg-purple-50 text-purple-700">
                                        {{ $item->competitionDetail?->level ?? 'Nasional' }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-right font-black text-purple-900 text-sm">
                                    Rp {{ number_format($item->total_budget_approved ?: $item->total_budget_requested, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <i class="fa-solid fa-check mr-0.5"></i> Lolos Keuangan
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    <a href="{{ route('warek2.show', $item) }}"
                                        class="inline-flex items-center space-x-1.5 px-3 py-1.5 rounded-lg text-xs font-bold bg-[#7E22CE] hover:bg-[#6B21A8] text-white shadow-xs transition">
                                        <i class="fa-solid fa-gavel"></i>
                                        <span>Tinjau & Putuskan</span>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <!-- 4. MONITORING ANGGARAN PER FAKULTAS -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-2.5 h-6 bg-indigo-600 rounded-full"></div>
                <div>
                    <h2 class="text-base font-extrabold text-slate-800">Monitoring Alokasi Anggaran Fakultas</h2>
                    <p class="text-xs text-slate-500">Pemantauan distribusi pagu dan realisasi belanja lomba antar fakultas se-lingkungan UNUJA.</p>
                </div>
            </div>
            <a href="{{ route('warek2.monitoring.budget') }}" class="text-xs font-bold text-purple-700 hover:text-purple-900 flex items-center space-x-1">
                <span>Detail Lengkap</span>
                <i class="fa-solid fa-chevron-right text-[10px]"></i>
            </a>
        </div>

        <div class="p-5 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach($facultyBudgetSummary as $code => $fac)
                @php
                    $pct = $fac['allocated'] > 0 ? min(100, round(($fac['used'] / $fac['allocated']) * 100, 1)) : 0;
                @endphp
                <div class="p-4 rounded-xl border border-slate-100 bg-slate-50/50 space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="font-extrabold text-slate-800 text-sm">{{ $code }}</span>
                        <span class="text-xs font-black text-purple-700">{{ $pct }}%</span>
                    </div>
                    <span class="text-[11px] text-slate-400 block truncate">{{ $fac['name'] }}</span>
                    <div class="w-full bg-slate-200 rounded-full h-2 overflow-hidden">
                        <div class="bg-purple-600 h-2 rounded-full" style="width: {{ $pct }}%"></div>
                    </div>
                    <div class="flex items-center justify-between text-[11px] pt-1">
                        <span class="text-slate-500">Terpakai: <strong>Rp {{ number_format($fac['used'], 0, ',', '.') }}</strong></span>
                        <span class="text-slate-400">Pagu: {{ number_format($fac['allocated'] / 1000000, 0) }} Jt</span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

</div>
@endsection

