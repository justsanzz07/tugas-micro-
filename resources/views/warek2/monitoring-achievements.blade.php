@extends('layouts.app')

@section('title', 'Monitoring Prestasi Universitas — Wakil Rektor II UNUJA')

@section('content')
<div class="space-y-6">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2 text-xs text-slate-500 mb-1">
                <a href="{{ route('warek2.dashboard') }}" class="hover:text-purple-700">Dashboard Warek II</a>
                <i class="fa-solid fa-chevron-right text-[10px] text-slate-400"></i>
                <span>Monitoring Capaian Prestasi</span>
            </div>
            <h1 class="text-xl font-extrabold text-slate-800 tracking-tight">Rekapitulasi Capaian Prestasi Mahasiswa Universitas</h1>
            <p class="text-xs text-slate-500">Pangkalan data prestasi, perolehan medali, dan delegasi kompetisi tingkat wilayah, nasional, hingga internasional.</p>
        </div>
        <div class="flex items-center space-x-2 self-start sm:self-auto">
            <button onclick="window.print()" class="inline-flex items-center space-x-2 px-3.5 py-2 rounded-xl text-xs font-bold text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 shadow-xs transition">
                <i class="fa-solid fa-print"></i>
                <span>Cetak Laporan</span>
            </button>
            <a href="{{ route('warek2.dashboard') }}" class="inline-flex items-center space-x-2 px-3.5 py-2 rounded-xl text-xs font-semibold text-slate-600 bg-white border border-slate-200 hover:bg-slate-50 shadow-xs transition">
                <i class="fa-solid fa-arrow-left"></i>
                <span>Kembali</span>
            </a>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-4">
        <form method="GET" action="{{ route('warek2.monitoring.achievements') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
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
            <div>
                <label class="block font-semibold text-slate-600 mb-1">Tingkat Perlombaan</label>
                <select name="level" class="w-full border border-slate-200 rounded-xl px-3 py-2 bg-slate-50 text-xs">
                    <option value="">Semua Tingkat</option>
                    <option value="Nasional" {{ $level == 'Nasional' ? 'selected' : '' }}>Nasional</option>
                    <option value="Internasional" {{ $level == 'Internasional' ? 'selected' : '' }}>Internasional</option>
                    <option value="Regional" {{ $level == 'Regional' ? 'selected' : '' }}>Regional / Wilayah</option>
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full py-2 bg-[#7E22CE] hover:bg-[#6B21A8] text-white font-bold rounded-xl text-xs transition">
                    <i class="fa-solid fa-filter mr-1"></i> Saring Data Prestasi
                </button>
            </div>
        </form>
    </div>

    <!-- Tabel Pangkalan Data Prestasi -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider">Pangkalan Data Delegasi & Prestasi Mahasiswa UNUJA</h3>
            <span class="text-xs text-purple-700 font-bold">Total Terdata: {{ $submissions->total() }} Kegiatan</span>
        </div>

        @if($submissions->isEmpty())
            <div class="p-8 text-center text-xs text-slate-400">
                Belum ada data delegasi lomba yang sesuai filter.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-slate-50 text-slate-500 font-bold uppercase text-[10px] border-b border-slate-200">
                            <th class="py-3 px-4">No</th>
                            <th class="py-3 px-4">Kode & Lomba</th>
                            <th class="py-3 px-4">Tingkat</th>
                            <th class="py-3 px-4">Fakultas</th>
                            <th class="py-3 px-4">Ketua Delegasi</th>
                            <th class="py-3 px-4">Sertifikat</th>
                            <th class="py-3 px-4 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($submissions as $idx => $item)
                            <tr class="hover:bg-purple-50/20">
                                <td class="py-3 px-4 text-slate-400 font-bold">{{ $submissions->firstItem() + $idx }}</td>
                                <td class="py-3 px-4">
                                    <span class="font-extrabold text-purple-900 block">{{ $item->competitionDetail?->name ?? $item->title }}</span>
                                    <span class="text-[10px] text-slate-400">{{ $item->code }}</span>
                                </td>
                                <td class="py-3 px-4">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-purple-50 text-purple-700">
                                        {{ $item->competitionDetail?->level ?? 'Nasional' }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 font-bold text-slate-700">{{ $item->fakultas_id }}</td>
                                <td class="py-3 px-4">
                                    <span class="font-semibold text-slate-800 block">{{ $item->submitter_name }}</span>
                                    <span class="text-[10px] text-slate-400">{{ $item->submitter_nim }}</span>
                                </td>
                                <td class="py-3 px-4">
                                    @if($item->certificates && $item->certificates->count() > 0)
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            <i class="fa-solid fa-certificate mr-1"></i> {{ $item->certificates->count() }} Sertifikat
                                        </span>
                                    @else
                                        <span class="text-slate-400 italic text-[11px]">-</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $item->status === 'completed' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-600' }}">
                                        {{ $item->current_status->label() }}
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

