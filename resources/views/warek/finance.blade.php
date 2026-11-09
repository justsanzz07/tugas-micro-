@extends('layouts.app')

@section('title', 'Monitoring SIMPAY — SIPRES')

@section('content')
<div class="space-y-6">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2 text-xs text-slate-500 mb-1">
                <a href="{{ route('warek.dashboard') }}" class="hover:text-amber-700">Dashboard Warek</a>
                <span>/</span>
                <span class="text-slate-800 font-semibold">Integrasi SIMPAY</span>
            </div>
            <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight">Monitoring Tiket Pencairan Dana SIMPAY</h1>
            <p class="text-xs text-slate-500">Sinkronisasi status tiket pencairan dana bantuan lomba mahasiswa ke sistem keuangan kampus (SIMPAY).</p>
        </div>
    </div>

    <!-- METRIK SIMPAY -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
            <span class="text-xs text-slate-500 font-semibold block">Total Plafon Disetujui</span>
            <span class="text-xl font-extrabold text-slate-800 mt-1 block">Rp {{ number_format($totalApproved, 0, ',', '.') }}</span>
            <span class="text-[11px] text-slate-400">Total komitmen dana perlombaan</span>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
            <span class="text-xs text-slate-500 font-semibold block">Telah Dicairkan (Disbursed)</span>
            <span class="text-xl font-extrabold text-emerald-700 mt-1 block">Rp {{ number_format($totalDisbursed, 0, ',', '.') }}</span>
            <span class="text-[11px] text-emerald-600 font-medium">Berhasil ditransfer ke mahasiswa</span>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
            <span class="text-xs text-slate-500 font-semibold block">Menunggu Pencairan (Pending)</span>
            <span class="text-xl font-extrabold text-amber-600 mt-1 block">Rp {{ number_format($totalPending, 0, ',', '.') }}</span>
            <span class="text-[11px] text-amber-700 font-medium">Dalam antrean verifikasi bagian keuangan</span>
        </div>
    </div>

    <!-- TABEL TIKET DISBURSEMENT -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h2 class="text-sm font-bold text-slate-800">Daftar Tiket Pencairan SIMPAY</h2>
            <span class="text-xs text-slate-400">Total: {{ $tickets->total() }} Tiket</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 uppercase text-[10px] tracking-wider border-b border-slate-200">
                        <th class="py-3 px-4">Kode Tiket</th>
                        <th class="py-3 px-4">Penerima (NIM Mahasiswa)</th>
                        <th class="py-3 px-4">Nominal Disetujui</th>
                        <th class="py-3 px-4">Status SIMPAY</th>
                        <th class="py-3 px-4">Waktu Sinkronisasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($tickets as $t)
                        <tr class="hover:bg-slate-50/80">
                            <td class="py-3 px-4 font-mono font-bold text-slate-800">{{ $t->ticket_code }}</td>
                            <td class="py-3 px-4 font-semibold text-slate-800">{{ $t->recipient_nim }}</td>
                            <td class="py-3 px-4 font-extrabold text-emerald-800">Rp {{ number_format($t->amount, 0, ',', '.') }}</td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $t->simpay_status->value === 'DISBURSED' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                    {{ $t->simpay_status->label() }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-slate-500">{{ $t->synced_at?->format('d M Y, H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-slate-400">Belum ada tiket pencairan yang diterbitkan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t border-slate-100">
            {{ $tickets->links() }}
        </div>
    </div>

</div>
@endsection

