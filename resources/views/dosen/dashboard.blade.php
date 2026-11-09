@extends('layouts.app')

@section('title', 'Dashboard Dosen Pembimbing — SIPRES')

@section('content')
<div class="space-y-6">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight">Portal Dosen Pembimbing</h1>
            <p class="text-xs text-slate-500">Verifikasi kelayakan teknis proposal lomba mahasiswa bimbingan sebelum diajukan ke Dekan Fakultas.</p>
        </div>
        <div class="flex items-center space-x-2 text-xs">
            <span class="px-3 py-1.5 rounded-xl bg-blue-50 border border-blue-200 text-blue-800 font-bold">
                <i class="fa-solid fa-id-card-clip mr-1"></i> NIDN: {{ auth()->user()->identifier ?? '0715088901' }}
            </span>
        </div>
    </div>

    <!-- ANTREAN MENUNGGU VERIFIKASI -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-blue-50/50">
            <div class="flex items-center space-x-2">
                <span class="w-2.5 h-2.5 rounded-full bg-blue-600 animate-pulse"></span>
                <h2 class="text-sm font-bold text-slate-800">Antrean Menunggu Verifikasi Pembimbing</h2>
            </div>
            <span class="text-xs font-bold px-2.5 py-0.5 rounded-full bg-blue-100 text-blue-800">
                {{ $pendingSubmissions->count() }} Pengajuan Menunggu
            </span>
        </div>

        @if($pendingSubmissions->isEmpty())
            <div class="p-8 text-center text-slate-400 text-xs">
                <i class="fa-solid fa-circle-check text-blue-500 text-3xl mb-2 block"></i>
                Tidak ada pengajuan lomba mahasiswa yang menunggu verifikasi Anda saat ini.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="bg-slate-50 text-slate-500 uppercase text-[10px] tracking-wider border-b border-slate-200">
                            <th class="py-3 px-4">Kode & Tanggal</th>
                            <th class="py-3 px-4">Mahasiswa Pengaju (Ketua)</th>
                            <th class="py-3 px-4">Nama Lomba & Tingkat</th>
                            <th class="py-3 px-4">Jadwal Pelaksanaan</th>
                            <th class="py-3 px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($pendingSubmissions as $sub)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-3 px-4 font-bold text-slate-800">{{ $sub->code }}</td>
                                <td class="py-3 px-4">
                                    <span class="font-bold text-slate-800 block">{{ $sub->mahasiswa_nim }}</span>
                                    <span class="text-[10px] text-slate-400">Total Tim: {{ $sub->members->count() }} Orang</span>
                                </td>
                                <td class="py-3 px-4">
                                    <span class="font-semibold text-slate-800 block">{{ $sub->competitionDetail?->competition_name }}</span>
                                    <span class="text-[10px] px-1.5 py-0.5 rounded font-bold uppercase bg-slate-100 text-slate-600">
                                        {{ $sub->competitionDetail?->level?->label() }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-slate-600">
                                    {{ $sub->competitionDetail?->start_date?->format('d M') }} - {{ $sub->competitionDetail?->end_date?->format('d M Y') }}
                                </td>
                                <td class="py-3 px-4 text-right">
                                    <a href="{{ route('dosen.show', $sub->id) }}" class="inline-flex items-center space-x-1 px-3 py-1.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs transition shadow-sm">
                                        <i class="fa-solid fa-clipboard-check"></i>
                                        <span>Tinjau & Verifikasi</span>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <!-- RIWAYAT BIMBINGAN -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100">
            <h2 class="text-sm font-bold text-slate-800">Riwayat Bimbingan Lomba Mahasiswa</h2>
            <p class="text-xs text-slate-400">Daftar delegasi mahasiswa yang telah diverifikasi atau telah selesai berprestasi.</p>
        </div>

        @if($historySubmissions->isEmpty())
            <div class="p-8 text-center text-slate-400 text-xs">
                Belum ada riwayat bimbingan.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="bg-slate-50 text-slate-500 uppercase text-[10px] tracking-wider border-b border-slate-200">
                            <th class="py-3 px-4">Kode</th>
                            <th class="py-3 px-4">Nama Lomba</th>
                            <th class="py-3 px-4">Ketua Tim</th>
                            <th class="py-3 px-4">Status Terkini</th>
                            <th class="py-3 px-4 text-right">Detail</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($historySubmissions as $sub)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-3 px-4 font-bold text-slate-800">{{ $sub->code }}</td>
                                <td class="py-3 px-4 font-semibold text-slate-800">{{ $sub->competitionDetail?->competition_name }}</td>
                                <td class="py-3 px-4 text-slate-600">{{ $sub->mahasiswa_nim }}</td>
                                <td class="py-3 px-4">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700">
                                        {{ $sub->current_status?->label() }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-right">
                                    <a href="{{ route('dosen.show', $sub->id) }}" class="text-blue-600 hover:text-blue-800 font-bold">
                                        Lihat
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

</div>
@endsection

