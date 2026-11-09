@extends('layouts.app')

@section('title', 'Dashboard Eksekutif Wakil Rektor — SIPRES')

@section('content')
<div class="space-y-6">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight">Portal Eksekutif Wakil Rektor (Warek)</h1>
            <p class="text-xs text-slate-500">Persetujuan final delegasi universitas, penetapan pagu dana pencairan SIMPAY, penerbitan Surat Tugas ber-QR Code, dan pengesahan SKPI.</p>
        </div>
        <div class="flex items-center space-x-2">
            <a href="{{ route('warek.finance') }}" class="inline-flex items-center space-x-1.5 bg-amber-600 hover:bg-amber-700 text-white font-bold px-3.5 py-2 rounded-xl text-xs transition shadow-sm">
                <i class="fa-solid fa-money-bill-transfer"></i>
                <span>Monitoring Tiket SIMPAY</span>
            </a>
        </div>
    </div>

    <!-- METRIK EKSEKUTIF ANGGARAN & PRESTASI -->
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
            <span class="text-xs text-slate-500 font-semibold block">Total Dana Disetujui</span>
            <span class="text-xl font-extrabold text-emerald-800 mt-1 block">Rp {{ number_format($totalApprovedFunding, 0, ',', '.') }}</span>
            <span class="text-[11px] text-slate-400">Komitmen pagu universitas</span>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
            <span class="text-xs text-slate-500 font-semibold block">Realisasi SIMPAY</span>
            <span class="text-xl font-extrabold text-blue-700 mt-1 block">Rp {{ number_format($totalDisbursed, 0, ',', '.') }}</span>
            <span class="text-[11px] text-blue-600 font-medium">Telah ditransfer ke mahasiswa</span>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
            <span class="text-xs text-slate-500 font-semibold block">Antrean Review Final</span>
            <span class="text-xl font-extrabold text-amber-600 mt-1 block">{{ $pendingFinalSubmissions->count() }} Pengajuan</span>
            <span class="text-[11px] text-amber-700 font-medium">Menunggu penetapan dana</span>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
            <span class="text-xs text-slate-500 font-semibold block">Antrean Validasi LPJ</span>
            <span class="text-xl font-extrabold text-purple-700 mt-1 block">{{ $pendingLpjSubmissions->count() }} Pengajuan</span>
            <span class="text-[11px] text-purple-700 font-medium">Klaim sertifikat & SKPI</span>
        </div>
    </div>

    <!-- TAB 1: ANTREAN PERSETUJUAN FINAL WAREK -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-amber-50/50">
            <div class="flex items-center space-x-2">
                <span class="w-2.5 h-2.5 rounded-full bg-amber-500 animate-pulse"></span>
                <h2 class="text-sm font-bold text-slate-800">Antrean Persetujuan Final & Penetapan Plafon Dana</h2>
            </div>
            <span class="text-xs font-bold px-2.5 py-0.5 rounded-full bg-amber-100 text-amber-900">
                {{ $pendingFinalSubmissions->count() }} Menunggu Keputusan Warek
            </span>
        </div>

        @if($pendingFinalSubmissions->isEmpty())
            <div class="p-8 text-center text-slate-400 text-xs">
                <i class="fa-solid fa-circle-check text-emerald-500 text-3xl mb-2 block"></i>
                Tidak ada pengajuan lomba yang menunggu persetujuan final saat ini.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="bg-slate-50 text-slate-500 uppercase text-[10px] tracking-wider border-b border-slate-200">
                            <th class="py-3 px-4">Kode & Delegasi</th>
                            <th class="py-3 px-4">Nama Lomba & Tingkat</th>
                            <th class="py-3 px-4">Fakultas</th>
                            <th class="py-3 px-4">Total RAB Diajukan</th>
                            <th class="py-3 px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($pendingFinalSubmissions as $sub)
                            @php $rab = $sub->budgetRequest(); @endphp
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-3 px-4">
                                    <span class="font-bold text-slate-800 block">{{ $sub->code }}</span>
                                    <span class="text-[10px] text-slate-400">Ketua: {{ $sub->mahasiswa_nim }}</span>
                                </td>
                                <td class="py-3 px-4">
                                    <span class="font-semibold text-slate-800 block">{{ $sub->competitionDetail?->competition_name }}</span>
                                    <span class="text-[10px] px-1.5 py-0.5 rounded font-bold uppercase bg-slate-100 text-slate-600">
                                        {{ $sub->competitionDetail?->level?->label() }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 font-semibold text-slate-700">{{ $sub->fakultas_id }}</td>
                                <td class="py-3 px-4 font-extrabold text-emerald-700">
                                    Rp {{ number_format($rab?->total_requested ?? 0, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-4 text-right">
                                    <a href="{{ route('warek.show', $sub->id) }}" class="inline-flex items-center space-x-1 px-3 py-1.5 rounded-lg bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs transition shadow-sm">
                                        <i class="fa-solid fa-gavel"></i>
                                        <span>Tetapkan Dana & Surat Tugas</span>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <!-- TAB 2: ANTREAN VALIDASI LPJ & SKPI -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-purple-50/50">
            <div class="flex items-center space-x-2">
                <span class="w-2.5 h-2.5 rounded-full bg-purple-600 animate-pulse"></span>
                <h2 class="text-sm font-bold text-slate-800">Antrean Validasi Laporan LPJ & Klaim Sertifikat Juara</h2>
            </div>
            <span class="text-xs font-bold px-2.5 py-0.5 rounded-full bg-purple-100 text-purple-900">
                {{ $pendingLpjSubmissions->count() }} Menunggu Validasi SKPI
            </span>
        </div>

        @if($pendingLpjSubmissions->isEmpty())
            <div class="p-8 text-center text-slate-400 text-xs">
                Tidak ada laporan LPJ yang menunggu validasi.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="bg-slate-50 text-slate-500 uppercase text-[10px] tracking-wider border-b border-slate-200">
                            <th class="py-3 px-4">Kode & Lomba</th>
                            <th class="py-3 px-4">Ketua Tim</th>
                            <th class="py-3 px-4">Klaim Peringkat Juara</th>
                            <th class="py-3 px-4">Tanggal Masuk LPJ</th>
                            <th class="py-3 px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($pendingLpjSubmissions as $sub)
                            @php $cert = $sub->certificates->first(); @endphp
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-3 px-4">
                                    <span class="font-bold text-slate-800 block">{{ $sub->code }}</span>
                                    <span class="text-[11px] text-slate-500 block truncate max-w-xs">{{ $sub->competitionDetail?->competition_name }}</span>
                                </td>
                                <td class="py-3 px-4 font-semibold text-slate-800">{{ $sub->mahasiswa_nim }}</td>
                                <td class="py-3 px-4">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-purple-100 text-purple-800">
                                        {{ $cert?->achievement_rank ?? 'Peserta / Finalis' }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-slate-500">{{ $sub->lpjReport?->submitted_at?->format('d M Y, H:i') }}</td>
                                <td class="py-3 px-4 text-right">
                                    <a href="{{ route('warek.lpj.show', $sub->id) }}" class="inline-flex items-center space-x-1 px-3 py-1.5 rounded-lg bg-purple-700 hover:bg-purple-800 text-white font-bold text-xs transition shadow-sm">
                                        <i class="fa-solid fa-award"></i>
                                        <span>Validasi & Sahkan SKPI</span>
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

