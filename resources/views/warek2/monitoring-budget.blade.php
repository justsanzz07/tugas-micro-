@extends('layouts.app')

@section('title', 'Monitoring Anggaran Fakultas — Wakil Rektor II UNUJA')

@section('content')
<div class="space-y-6">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2 text-xs text-slate-500 mb-1">
                <a href="{{ route('warek2.dashboard') }}" class="hover:text-purple-700">Dashboard Warek II</a>
                <i class="fa-solid fa-chevron-right text-[10px] text-slate-400"></i>
                <span>Monitoring Anggaran Fakultas</span>
            </div>
            <h1 class="text-xl font-extrabold text-slate-800 tracking-tight">Monitoring Alokasi & Serapan Anggaran Lomba Antar Fakultas</h1>
            <p class="text-xs text-slate-500">Pengawasan distribusi pagu pagu anggaran delegasi dan realisasi pencairan di lingkungan Universitas Nurul Jadid.</p>
        </div>
        <div class="flex items-center space-x-2 self-start sm:self-auto">
            <button onclick="window.print()" class="inline-flex items-center space-x-2 px-3.5 py-2 rounded-xl text-xs font-bold text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 shadow-xs transition">
                <i class="fa-solid fa-print"></i>
                <span>Cetak Rekap</span>
            </button>
            <a href="{{ route('warek2.dashboard') }}" class="inline-flex items-center space-x-2 px-3.5 py-2 rounded-xl text-xs font-semibold text-slate-600 bg-white border border-slate-200 hover:bg-slate-50 shadow-xs transition">
                <i class="fa-solid fa-arrow-left"></i>
                <span>Kembali</span>
            </a>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-4">
        <form method="GET" action="{{ route('warek2.monitoring.budget') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
            <div>
                <label class="block font-semibold text-slate-600 mb-1">Tahun Anggaran</label>
                <select name="year" class="w-full border border-slate-200 rounded-xl px-3 py-2 bg-slate-50 text-xs">
                    <option value="2026" {{ $year == '2026' ? 'selected' : '' }}>2026</option>
                    <option value="2025" {{ $year == '2025' ? 'selected' : '' }}>2025</option>
                </select>
            </div>
            <div>
                <label class="block font-semibold text-slate-600 mb-1">Fakultas</label>
                <select name="faculty" class="w-full border border-slate-200 rounded-xl px-3 py-2 bg-slate-50 text-xs">
                    <option value="">Semua Fakultas</option>
                    <option value="FTI" {{ $faculty == 'FTI' ? 'selected' : '' }}>Fakultas Teknologi Informasi</option>
                    <option value="FAI" {{ $faculty == 'FAI' ? 'selected' : '' }}>Fakultas Agama Islam</option>
                    <option value="FEB" {{ $faculty == 'FEB' ? 'selected' : '' }}>Fakultas Ekonomi & Bisnis</option>
                    <option value="FKES" {{ $faculty == 'FKES' ? 'selected' : '' }}>Fakultas Kesehatan</option>
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full py-2 bg-[#7E22CE] hover:bg-[#6B21A8] text-white font-bold rounded-xl text-xs transition">
                    <i class="fa-solid fa-filter mr-1"></i> Terapkan Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Tabel Rekapitulasi Alokasi vs Serapan -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider">Distribusi Pagu Anggaran Universitas Per Fakultas</h3>
            <span class="text-xs text-purple-700 font-bold">Tahun Anggaran {{ $year }}</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 font-bold uppercase text-[11px] border-b border-slate-200">
                        <th class="py-3 px-4">Fakultas</th>
                        <th class="py-3 px-4 text-right">Pagu Alokasi</th>
                        <th class="py-3 px-4 text-right">Disetujui Final</th>
                        <th class="py-3 px-4 text-right">Telah Dicairkan</th>
                        <th class="py-3 px-4 text-right">Sisa Pagu</th>
                        <th class="py-3 px-4 text-center">Serapan (%)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($budgetReport as $code => $data)
                        <tr class="hover:bg-purple-50/20">
                            <td class="py-3.5 px-4">
                                <span class="font-extrabold text-purple-900 block">{{ $code }}</span>
                                <span class="text-[11px] text-slate-400">{{ $data['name'] }}</span>
                            </td>
                            <td class="py-3.5 px-4 text-right font-semibold text-slate-700">Rp {{ number_format($data['allocation'], 0, ',', '.') }}</td>
                            <td class="py-3.5 px-4 text-right font-black text-indigo-900">Rp {{ number_format($data['approved'], 0, ',', '.') }}</td>
                            <td class="py-3.5 px-4 text-right font-black text-emerald-700">Rp {{ number_format($data['disbursed'], 0, ',', '.') }}</td>
                            <td class="py-3.5 px-4 text-right font-medium text-slate-600">Rp {{ number_format($data['remaining'], 0, ',', '.') }}</td>
                            <td class="py-3.5 px-4 text-center">
                                <div class="inline-flex items-center space-x-2">
                                    <div class="w-16 bg-slate-100 rounded-full h-2 overflow-hidden">
                                        <div class="bg-purple-600 h-2 rounded-full" style="width: {{ min(100, $data['percent']) }}%"></div>
                                    </div>
                                    <span class="font-black text-purple-800 text-[11px]">{{ $data['percent'] }}%</span>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Delegasi yang Memperoleh Plafon -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-4 border-b border-slate-100">
            <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider">Daftar Pengajuan Delegasi Mahasiswa</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 font-bold uppercase text-[10px] border-b border-slate-200">
                        <th class="py-3 px-4">Kode & Tanggal</th>
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
                                <span class="font-bold text-purple-900 block">{{ $sub->code }}</span>
                                <span class="text-[10px] text-slate-400">{{ $sub->created_at ? $sub->created_at->format('d/m/Y') : '-' }}</span>
                            </td>
                            <td class="py-3 px-4 font-bold text-slate-700">{{ $sub->fakultas_id }}</td>
                            <td class="py-3 px-4">
                                <span class="font-semibold text-slate-800 block">{{ $sub->submitter_name }}</span>
                                <span class="text-[10px] text-slate-400">NIM: {{ $sub->submitter_nim }}</span>
                            </td>
                            <td class="py-3 px-4 text-slate-700 max-w-xs truncate">{{ $sub->competitionDetail?->name ?? $sub->title }}</td>
                            <td class="py-3 px-4 text-right font-black text-purple-900">Rp {{ number_format($sub->total_budget_approved, 0, ',', '.') }}</td>
                            <td class="py-3 px-4 text-center">
                                <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-purple-50 text-purple-700">
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
