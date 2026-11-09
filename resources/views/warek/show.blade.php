@extends('layouts.app')

@section('title', 'Persetujuan Final Warek — ' . $submission->code)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Breadcrumb & Header -->
    <div class="flex items-center space-x-2 text-xs text-slate-500">
        <a href="{{ route('warek.dashboard') }}" class="hover:text-amber-700">Dashboard Warek</a>
        <span>/</span>
        <span class="text-slate-800 font-semibold">Persetujuan Final Universitas [{{ $submission->code }}]</span>
    </div>

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight">{{ $submission->competitionDetail?->competition_name }}</h1>
            <p class="text-xs text-slate-500">Fakultas: {{ $submission->fakultas_id }} &bull; Mahasiswa Ketua: {{ $submission->mahasiswa_nim }} &bull; Dosen: NIDN {{ $submission->dosen_nidn }}</p>
        </div>
        <div>
            <span class="px-3.5 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-900 border border-amber-300">
                Status: {{ $submission->current_status?->label() }}
            </span>
        </div>
    </div>

    <!-- REKAM JEJAK CATATAN PEMBIMBING & DEKAN -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
        @php
            $dosenStep = $flow?->steps()->where('step_order', 1)->first();
            $dekanStep = $flow?->steps()->where('step_order', 2)->first();
        @endphp
        <div class="bg-blue-50 border border-blue-200 p-4 rounded-2xl">
            <div class="flex items-center space-x-2 text-blue-900 font-bold mb-1">
                <i class="fa-solid fa-chalkboard-user"></i>
                <span>Rekomendasi Dosen Pembimbing (NIDN: {{ $submission->dosen_nidn }})</span>
            </div>
            <p class="text-slate-700 italic">"{{ $dosenStep?->notes ?? 'Layak diajukan untuk mewakili kampus.' }}"</p>
        </div>

        <div class="bg-purple-50 border border-purple-200 p-4 rounded-2xl">
            <div class="flex items-center space-x-2 text-purple-900 font-bold mb-1">
                <i class="fa-solid fa-building-columns"></i>
                <span>Pengesahan Fakultas (Dekan {{ $submission->fakultas_id }})</span>
            </div>
            <p class="text-slate-700 italic">"{{ $dekanStep?->notes ?? 'Izin dispensasi kuliah telah disetujui fakultas.' }}"</p>
        </div>
    </div>

    <!-- DETAIL LOMBA & DOKUMEN -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 space-y-4">
        <h3 class="text-sm font-bold text-slate-800 border-b border-slate-100 pb-3 flex items-center">
            <i class="fa-solid fa-trophy text-amber-500 mr-2"></i>
            Detail Delegasi & Berkas Proposal
        </h3>

        <div class="grid grid-cols-2 gap-4 text-xs">
            <div>
                <span class="text-slate-400 block text-[10px] uppercase font-semibold">Penyelenggara & Tingkat</span>
                <span class="font-bold text-slate-800">{{ $submission->competitionDetail?->organizer }} ({{ $submission->competitionDetail?->level?->label() }})</span>
            </div>
            <div>
                <span class="text-slate-400 block text-[10px] uppercase font-semibold">Jadwal Lomba</span>
                <span class="font-bold text-slate-800">{{ $submission->competitionDetail?->start_date?->format('d M Y') }} s/d {{ $submission->competitionDetail?->end_date?->format('d M Y') }}</span>
            </div>
            <div>
                <span class="text-slate-400 block text-[10px] uppercase font-semibold">Lokasi Perlombaan</span>
                <span class="font-bold text-slate-800">{{ $submission->competitionDetail?->location }}</span>
            </div>
            <div>
                <span class="text-slate-400 block text-[10px] uppercase font-semibold">Anggota Tim</span>
                <span class="font-bold text-slate-800">{{ $submission->members->pluck('nim')->join(', ') }}</span>
            </div>
            <div class="col-span-2">
                <span class="text-slate-400 block text-[10px] uppercase font-semibold">Dokumen Proposal</span>
                @php $doc = $submission->documents->first(); @endphp
                @if($doc)
                    <div class="mt-1 flex items-center justify-between bg-slate-50 p-3 rounded-xl border border-slate-200">
                        <div class="flex items-center space-x-2">
                            <i class="fa-solid fa-file-pdf text-rose-600 text-xl"></i>
                            <div>
                                <div class="font-bold text-slate-800">{{ $doc->file_name }}</div>
                                <div class="text-[10px] text-slate-400">{{ number_format($doc->file_size / 1024, 0) }} KB</div>
                            </div>
                        </div>
                        <span class="px-2.5 py-1 bg-emerald-100 text-emerald-800 rounded font-bold text-xs">Dokumen Sah</span>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- USULAN RAB & RINCIAN ITEM -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h3 class="text-sm font-bold text-slate-800 flex items-center">
                <i class="fa-solid fa-calculator text-emerald-600 mr-2"></i>
                Rencana Anggaran Biaya (RAB) Diajukan
            </h3>
            <span class="text-sm font-extrabold text-emerald-800">
                Total Diajukan: Rp {{ number_format($budget?->total_requested ?? 0, 0, ',', '.') }}
            </span>
        </div>

        <table class="w-full text-left text-xs">
            <thead>
                <tr class="bg-slate-50 text-slate-500 uppercase text-[10px] border-b border-slate-200">
                    <th class="py-2 px-3">Item Pengeluaran</th>
                    <th class="py-2 px-3 text-center">Vol</th>
                    <th class="py-2 px-3">Satuan</th>
                    <th class="py-2 px-3 text-right">Harga Satuan</th>
                    <th class="py-2 px-3 text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($budget?->items ?? [] as $item)
                    <tr>
                        <td class="py-2 px-3 font-medium text-slate-800">{{ $item->item_name }}</td>
                        <td class="py-2 px-3 text-center">{{ $item->quantity }}</td>
                        <td class="py-2 px-3 text-slate-500">{{ $item->unit }}</td>
                        <td class="py-2 px-3 text-right">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                        <td class="py-2 px-3 text-right font-bold text-slate-800">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- FORM KEPUTUSAN FINAL WAREK (PENETAPAN PLAFON DANA & SURAT TUGAS) -->
    @if($submission->current_status?->value === 'REVIEW_WAREK')
        <div class="bg-white rounded-2xl border-2 border-amber-300 shadow-md p-6 space-y-4">
            <div class="flex items-center space-x-2 border-b border-slate-100 pb-3">
                <i class="fa-solid fa-crown text-amber-500 text-xl"></i>
                <div>
                    <h3 class="text-sm font-bold text-slate-800">Penetapan Plafon Dana, Surat Tugas Universitas, & Tiket SIMPAY</h3>
                    <p class="text-[11px] text-slate-400">Persetujuan final Anda akan menerbitkan Surat Tugas resmi universitas ber-QR Code dan menerbitkan tiket pencairan dana ke sistem SIMPAY.</p>
                </div>
            </div>

            <form action="{{ route('warek.process', $submission->id) }}" method="POST" class="space-y-4">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    <div>
                        <label class="block font-bold text-slate-800 mb-1">
                            Nominal Dana Disetujui untuk Dicairkan (Rp) <span class="text-rose-500">*</span>
                        </label>
                        <input type="number" name="approved_amount" value="{{ old('approved_amount', $budget?->total_requested ?? 0) }}" min="0" step="10000" required
                            class="w-full px-3 py-2.5 text-sm font-bold border-2 border-amber-300 rounded-xl focus:ring-2 focus:ring-amber-500 outline-none text-emerald-800 bg-amber-50/30">
                        <span class="text-[10px] text-slate-400 mt-1 block">Nominal ini yang akan dicairkan oleh bagian keuangan melalui tiket SIMPAY.</span>
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Catatan Keputusan Warek</label>
                        <textarea name="notes" rows="2" class="w-full px-3 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-amber-500 outline-none"
                            placeholder="Catatan arahan universitas atau instruksi pendanaan...">{{ old('notes') }}</textarea>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row items-center justify-end gap-3 pt-4 border-t border-slate-100">
                    <!-- Reject -->
                    <button type="submit" name="action" value="REJECT" onclick="return confirm('Apakah Anda yakin ingin MENOLAK pengajuan ini?')"
                        class="w-full sm:w-auto px-4 py-2.5 rounded-xl border border-rose-300 text-rose-700 hover:bg-rose-50 font-bold text-xs transition flex items-center justify-center space-x-1.5">
                        <i class="fa-solid fa-xmark"></i>
                        <span>Tolak Pengajuan</span>
                    </button>

                    <!-- Revise -->
                    <button type="submit" name="action" value="REVISE"
                        class="w-full sm:w-auto px-4 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-bold text-xs transition flex items-center justify-center space-x-1.5 shadow">
                        <i class="fa-solid fa-rotate-left"></i>
                        <span>Minta Mahasiswa Revisi</span>
                    </button>

                    <!-- Approve Final -->
                    <button type="submit" name="action" value="APPROVE"
                        class="w-full sm:w-auto px-6 py-2.5 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white font-extrabold text-xs transition flex items-center justify-center space-x-1.5 shadow-md">
                        <i class="fa-solid fa-circle-check"></i>
                        <span>Setujui Final & Terbitkan Surat Tugas + SIMPAY</span>
                    </button>
                </div>
            </form>
        </div>
    @endif

</div>
@endsection

