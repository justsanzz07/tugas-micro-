@extends('layouts.admin')

@section('title', 'Laporan Rekapitulasi Eksekutif — Admin SIPRES')
@section('breadcrumb', 'Laporan')
@section('header_title', 'Laporan & Rekapitulasi Prestasi Universitas')

@section('content')
<div class="space-y-6">

    <!-- Header & Action -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h1 class="text-xl font-extrabold text-[#172554] tracking-tight">Laporan Rekapitulasi Pengajuan, Prestasi, &amp; Anggaran</h1>
            <p class="text-xs text-slate-500">Rekapitulasi komprehensif seluruh aktivitas delegasi kompetisi dan serapan anggaran tingkat universitas.</p>
        </div>
        <div class="flex items-center space-x-2 self-start sm:self-auto">
            <button onclick="window.print()"
                class="inline-flex items-center space-x-2 px-4 py-2.5 rounded-xl text-xs font-bold text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 shadow-2xs transition">
                <i class="fa-solid fa-print text-[#2454C5]"></i>
                <span>Cetak / Ekspor PDF</span>
            </button>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-4 shadow-2xs">
        <form method="GET" action="{{ route('admin.reports.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
            <div>
                <label class="block font-bold text-slate-700 mb-1">Tahun Anggaran</label>
                <select name="year" class="w-full border border-slate-300 rounded-xl p-2 focus:ring-2 focus:ring-[#2454C5]">
                    <option value="2026" {{ $year == '2026' ? 'selected' : '' }}>2026</option>
                    <option value="2025" {{ $year == '2025' ? 'selected' : '' }}>2025</option>
                </select>
            </div>
            <div>
                <label class="block font-bold text-slate-700 mb-1">Fakultas</label>
                <select name="faculty" class="w-full border border-slate-300 rounded-xl p-2 focus:ring-2 focus:ring-[#2454C5]">
                    <option value="">Semua Fakultas</option>
                    <option value="FTI" {{ $faculty == 'FTI' ? 'selected' : '' }}>Fakultas Teknologi Informasi (FTI)</option>
                    <option value="FAI" {{ $faculty == 'FAI' ? 'selected' : '' }}>Fakultas Agama Islam (FAI)</option>
                    <option value="FEB" {{ $faculty == 'FEB' ? 'selected' : '' }}>Fakultas Ekonomi &amp; Bisnis (FEB)</option>
                    <option value="FKES" {{ $faculty == 'FKES' ? 'selected' : '' }}>Fakultas Kesehatan (FKES)</option>
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full py-2 bg-[#2454C5] hover:bg-[#1d4ed8] text-white font-bold rounded-xl text-xs transition">
                    <i class="fa-solid fa-filter mr-1"></i> Terapkan Filter
                </button>
            </div>
        </form>
    </div>

    <!-- 4 KARTU REKAPITULASI RINGKAS -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- 1. Rekapitulasi Pengajuan -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-2xs space-y-2">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-400">1. Rekap Pengajuan</span>
            <div class="text-2xl font-black text-slate-900">{{ $totalSubmissions }} Proposal</div>
            <div class="text-[11px] text-slate-500 space-y-0.5 pt-1 border-t border-slate-100">
                <div class="flex justify-between"><span>Disetujui:</span> <strong>{{ $totalApproved }}</strong></div>
                <div class="flex justify-between"><span>Selesai:</span> <strong>{{ $totalCompleted }}</strong></div>
                <div class="flex justify-between"><span>Ditolak:</span> <strong>{{ $totalRejected }}</strong></div>
            </div>
        </div>

        <!-- 2. Rekapitulasi Prestasi -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-2xs space-y-2">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-400">2. Rekap Prestasi</span>
            <div class="text-2xl font-black text-amber-600">{{ $totalAchievements }} Juara</div>
            <div class="text-[11px] text-slate-500 space-y-0.5 pt-1 border-t border-slate-100">
                <div class="flex justify-between"><span>Sertifikat Terbit:</span> <strong>{{ $totalAchievements }}</strong></div>
                <div class="flex justify-between"><span>Validasi SKPI:</span> <strong>100% Sah</strong></div>
            </div>
        </div>

        <!-- 3. Rekapitulasi Anggaran -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-2xs space-y-2">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-400">3. Rekap Anggaran</span>
            <div class="text-lg font-black text-indigo-900 truncate">Rp {{ number_format($totalBudgetApproved, 0, ',', '.') }}</div>
            <div class="text-[11px] text-slate-500 space-y-0.5 pt-1 border-t border-slate-100">
                <div class="flex justify-between"><span>Total Usulan:</span> <strong>Rp {{ number_format($totalBudgetProposed, 0, ',', '.') }}</strong></div>
                <div class="flex justify-between"><span>Rasio Setuju:</span> <strong>{{ $totalBudgetProposed > 0 ? round(($totalBudgetApproved / $totalBudgetProposed) * 100, 1) : 0 }}%</strong></div>
            </div>
        </div>

        <!-- 4. Rekapitulasi Pencairan Kas -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-2xs space-y-2">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-400">4. Rekap Pencairan Kas</span>
            <div class="text-lg font-black text-emerald-700 truncate">Rp {{ number_format($totalDisbursed, 0, ',', '.') }}</div>
            <div class="text-[11px] text-slate-500 space-y-0.5 pt-1 border-t border-slate-100">
                <div class="flex justify-between"><span>Realisasi Kas:</span> <strong>Terbayar</strong></div>
                <div class="flex justify-between"><span>Pencairan via:</span> <strong>Bank Transfer</strong></div>
            </div>
        </div>
    </div>

    <!-- Tabel Rekapitulasi Anggaran Per Fakultas -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-2xs overflow-hidden">
        <div class="p-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-xs font-extrabold uppercase tracking-wider text-slate-800 flex items-center space-x-2">
                <i class="fa-solid fa-building-columns text-[#2454C5]"></i>
                <span>Distribusi Alokasi &amp; Serapan Pagu Per Fakultas</span>
            </h3>
            <span class="text-xs text-slate-400 font-semibold">Tahun {{ $year }}</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 font-bold uppercase text-[10px] border-b border-slate-200">
                        <th class="py-3 px-4">Fakultas</th>
                        <th class="py-3 px-4 text-center">Jumlah Delegasi</th>
                        <th class="py-3 px-4 text-right">Pagu Anggaran</th>
                        <th class="py-3 px-4 text-right">Dana Disetujui</th>
                        <th class="py-3 px-4 text-right">Sisa Pagu</th>
                        <th class="py-3 px-4 text-center">Persentase Serapan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($facultySummary as $code => $f)
                        <tr class="hover:bg-slate-50/50">
                            <td class="py-3.5 px-4">
                                <span class="font-extrabold text-[#172554] block">{{ $code }}</span>
                                <span class="text-[11px] text-slate-400">{{ $f['name'] }}</span>
                            </td>
                            <td class="py-3.5 px-4 text-center font-bold text-slate-700">{{ $f['count'] }}</td>
                            <td class="py-3.5 px-4 text-right font-medium text-slate-600">Rp {{ number_format($f['pagu'], 0, ',', '.') }}</td>
                            <td class="py-3.5 px-4 text-right font-black text-indigo-900">Rp {{ number_format($f['approved'], 0, ',', '.') }}</td>
                            <td class="py-3.5 px-4 text-right font-medium text-slate-500">Rp {{ number_format($f['remaining'], 0, ',', '.') }}</td>
                            <td class="py-3.5 px-4 text-center">
                                <div class="inline-flex items-center space-x-2">
                                    <div class="w-16 bg-slate-100 rounded-full h-2 overflow-hidden">
                                        <div class="bg-[#2454C5] h-2 rounded-full" style="width: {{ min(100, $f['percent']) }}%"></div>
                                    </div>
                                    <span class="font-black text-slate-700 text-[11px]">{{ $f['percent'] }}%</span>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Tabel Rincian Delegasi -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-2xs overflow-hidden">
        <div class="p-4 border-b border-slate-100">
            <h3 class="text-xs font-extrabold uppercase tracking-wider text-slate-800">Daftar Rincian Delegasi Mahasiswa</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 font-bold uppercase text-[10px] border-b border-slate-200">
                        <th class="py-3 px-4">Kode &amp; Tanggal</th>
                        <th class="py-3 px-4">Fakultas</th>
                        <th class="py-3 px-4">Ketua Delegasi</th>
                        <th class="py-3 px-4">Nama Lomba</th>
                        <th class="py-3 px-4 text-right">Plafon Disetujui</th>
                        <th class="py-3 px-4 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($submissions as $sub)
                        <tr class="hover:bg-slate-50/50">
                            <td class="py-3 px-4">
                                <span class="font-bold text-[#2454C5] block">{{ $sub->code }}</span>
                                <span class="text-[10px] text-slate-400">{{ $sub->created_at ? $sub->created_at->format('d/m/Y') : '-' }}</span>
                            </td>
                            <td class="py-3 px-4 font-bold text-slate-700">{{ $sub->fakultas_id }}</td>
                            <td class="py-3 px-4">
                                <span class="font-semibold text-slate-800 block">{{ $sub->submitter_name }}</span>
                                <span class="text-[10px] text-slate-400">{{ $sub->submitter_nim }}</span>
                            </td>
                            <td class="py-3 px-4 text-slate-700 max-w-xs truncate">{{ $sub->competitionDetail?->name ?? $sub->title }}</td>
                            <td class="py-3 px-4 text-right font-black text-slate-800">Rp {{ number_format($sub->total_budget_approved ?: 0, 0, ',', '.') }}</td>
                            <td class="py-3 px-4 text-center">
                                <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-blue-50 text-blue-700">
                                    {{ $sub->current_status->label() }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-3 border-t border-slate-100">
            {{ $submissions->links() }}
        </div>
    </div>

</div>
@endsection
