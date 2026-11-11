@extends('layouts.app')

@section('title', 'Laporan Realisasi Anggaran Lomba — Biro Keuangan UNUJA')

@section('content')
<div class="space-y-6">

    <!-- Header & Action -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2 text-xs text-slate-500 mb-1">
                <a href="{{ route('keuangan.dashboard') }}" class="hover:text-blue-700">Dashboard Keuangan</a>
                <i class="fa-solid fa-chevron-right text-[10px] text-slate-400"></i>
                <span>Laporan Realisasi Anggaran</span>
            </div>
            <h1 class="text-xl font-extrabold text-slate-800 tracking-tight">Laporan Realisasi Anggaran Prestasi Mahasiswa</h1>
            <p class="text-xs text-slate-500">Rekapitulasi penetapan pagu, pencairan kas, dan serapan anggaran per fakultas.</p>
        </div>
        <div class="flex items-center space-x-2 self-start sm:self-auto">
            <button onclick="window.print()"
                class="inline-flex items-center space-x-2 px-3.5 py-2 rounded-xl text-xs font-bold text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 shadow-xs transition">
                <i class="fa-solid fa-print"></i>
                <span>Cetak / Ekspor PDF</span>
            </button>
            <a href="{{ route('keuangan.dashboard') }}"
                class="inline-flex items-center space-x-2 px-3.5 py-2 rounded-xl text-xs font-semibold text-slate-600 bg-white border border-slate-200 hover:bg-slate-50 shadow-xs transition">
                <i class="fa-solid fa-arrow-left"></i>
                <span>Kembali</span>
            </a>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-4">
        <form method="GET" action="{{ route('keuangan.reports') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3 text-xs">
            <div>
                <label class="block font-semibold text-slate-600 mb-1">Tahun Anggaran</label>
                <select name="year" class="w-full border border-slate-200 rounded-xl px-3 py-2 bg-slate-50 focus:bg-white text-xs">
                    <option value="2026" {{ $year == '2026' ? 'selected' : '' }}>2026</option>
                    <option value="2025" {{ $year == '2025' ? 'selected' : '' }}>2025</option>
                </select>
            </div>
            <div>
                <label class="block font-semibold text-slate-600 mb-1">Fakultas</label>
                <select name="faculty" class="w-full border border-slate-200 rounded-xl px-3 py-2 bg-slate-50 focus:bg-white text-xs">
                    <option value="">Semua Fakultas</option>
                    <option value="FTI" {{ $faculty == 'FTI' ? 'selected' : '' }}>Fakultas Teknologi Informasi</option>
                    <option value="FAI" {{ $faculty == 'FAI' ? 'selected' : '' }}>Fakultas Agama Islam</option>
                    <option value="FEB" {{ $faculty == 'FEB' ? 'selected' : '' }}>Fakultas Ekonomi & Bisnis</option>
                    <option value="FKES" {{ $faculty == 'FKES' ? 'selected' : '' }}>Fakultas Kesehatan</option>
                </select>
            </div>
            <div>
                <label class="block font-semibold text-slate-600 mb-1">Status Pencairan</label>
                <select name="status" class="w-full border border-slate-200 rounded-xl px-3 py-2 bg-slate-50 focus:bg-white text-xs">
                    <option value="">Semua Status</option>
                    <option value="disbursed" {{ $status == 'disbursed' ? 'selected' : '' }}>Telah Dicairkan</option>
                    <option value="pending" {{ $status == 'pending' ? 'selected' : '' }}>Menunggu Pencairan</option>
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full py-2 bg-blue-700 hover:bg-blue-800 text-white font-bold rounded-xl text-xs transition">
                    <i class="fa-solid fa-filter mr-1"></i> Terapkan Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Rekapitulasi Serapan Anggaran per Fakultas -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider">Rekapitulasi Serapan Anggaran Per Fakultas</h3>
            <span class="text-xs text-slate-400 font-medium">Tahun {{ $year }}</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 font-bold uppercase text-[11px] border-b border-slate-200">
                        <th class="py-3 px-4">Fakultas</th>
                        <th class="py-3 px-4 text-right">Total Disetujui</th>
                        <th class="py-3 px-4 text-right">Realisasi Dicairkan</th>
                        <th class="py-3 px-4 text-right">Sisa Pagu</th>
                        <th class="py-3 px-4 text-center">Persentase Serapan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($facultyStats as $code => $data)
                        <tr class="hover:bg-slate-50/50">
                            <td class="py-3.5 px-4">
                                <span class="font-extrabold text-slate-800 block">{{ $code }}</span>
                                <span class="text-[11px] text-slate-400">{{ $data['name'] }}</span>
                            </td>
                            <td class="py-3.5 px-4 text-right font-bold text-slate-700">Rp {{ number_format($data['approved'], 0, ',', '.') }}</td>
                            <td class="py-3.5 px-4 text-right font-black text-emerald-700">Rp {{ number_format($data['disbursed'], 0, ',', '.') }}</td>
                            <td class="py-3.5 px-4 text-right font-medium text-slate-500">Rp {{ number_format($data['remaining'], 0, ',', '.') }}</td>
                            <td class="py-3.5 px-4 text-center">
                                <div class="inline-flex items-center space-x-2">
                                    <div class="w-20 bg-slate-100 rounded-full h-2 overflow-hidden">
                                        <div class="bg-emerald-600 h-2 rounded-full" style="width: {{ min(100, $data['percentage']) }}%"></div>
                                    </div>
                                    <span class="font-black text-slate-700 text-[11px]">{{ $data['percentage'] }}%</span>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Tabel Rincian Delegasi -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-4 border-b border-slate-100">
            <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider">Rincian Delegasi & Pencairan Kas</h3>
        </div>
        @if($submissions->isEmpty())
            <div class="p-8 text-center text-xs text-slate-400">
                Tidak ada data delegasi yang cocok dengan kriteria filter.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-slate-50 text-slate-500 font-bold uppercase text-[10px] border-b border-slate-200">
                            <th class="py-3 px-4">Kode & Tanggal</th>
                            <th class="py-3 px-4">Fakultas</th>
                            <th class="py-3 px-4">Ketua Delegasi</th>
                            <th class="py-3 px-4">Nama Lomba</th>
                            <th class="py-3 px-4 text-right">Dana Disetujui</th>
                            <th class="py-3 px-4 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($submissions as $sub)
                            <tr class="hover:bg-slate-50/50">
                                <td class="py-3 px-4">
                                    <span class="font-bold text-blue-900 block">{{ $sub->code }}</span>
                                    <span class="text-[10px] text-slate-400">{{ $sub->created_at ? $sub->created_at->format('d/m/Y') : '-' }}</span>
                                </td>
                                <td class="py-3 px-4 font-bold text-slate-700">{{ $sub->fakultas_id }}</td>
                                <td class="py-3 px-4">
                                    <span class="font-semibold text-slate-800 block">{{ $sub->submitter_name }}</span>
                                    <span class="text-[10px] text-slate-400">{{ $sub->submitter_nim }}</span>
                                </td>
                                <td class="py-3 px-4 text-slate-700 max-w-xs truncate">{{ $sub->competitionDetail?->name ?? $sub->title }}</td>
                                <td class="py-3 px-4 text-right font-black text-slate-800">Rp {{ number_format($sub->total_budget_approved, 0, ',', '.') }}</td>
                                <td class="py-3 px-4 text-center">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 text-emerald-700">
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
        @endif
    </div>

</div>
@endsection

