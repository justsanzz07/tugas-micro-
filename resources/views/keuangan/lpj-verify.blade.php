@extends('layouts.app')

@section('title', 'Audit LPJ & Keuangan [' . $submission->code . '] — SIPRES UNUJA')

@section('content')
<div class="space-y-6">

    <!-- Header & Breadcrumb -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2 text-xs text-slate-500 mb-1">
                <a href="{{ route('keuangan.dashboard') }}" class="hover:text-blue-700">Dashboard Keuangan</a>
                <i class="fa-solid fa-chevron-right text-[10px] text-slate-400"></i>
                <span>Verifikasi Pertanggungjawaban (LPJ)</span>
            </div>
            <h1 class="text-xl font-extrabold text-slate-800 tracking-tight">Pemeriksaan LPJ & Realisasi Anggaran</h1>
            <p class="text-xs text-slate-500">Pemeriksaan bukti fisik transaksi, nota kuitansi belanja lomba, dan sisa pengembalian dana.</p>
        </div>
        <a href="{{ route('keuangan.dashboard') }}"
            class="inline-flex items-center space-x-2 px-3.5 py-2 rounded-xl text-xs font-semibold text-slate-600 bg-white border border-slate-200 hover:bg-slate-50 shadow-xs self-start sm:self-auto transition">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Kembali ke Dashboard</span>
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Kolom Kiri: Dokumen LPJ & Bukti Nota -->
        <div class="lg:col-span-2 space-y-6">

            <!-- Detail Pendanaan -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-5">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Informasi Realisasi Kas Kegiatan</h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="bg-slate-50 p-3.5 rounded-xl border border-slate-200/60">
                        <span class="text-[11px] text-slate-500 block">Dana Disetujui</span>
                        <span class="text-lg font-black text-slate-800 mt-1 block">
                            Rp {{ number_format($submission->total_budget_approved, 0, ',', '.') }}
                        </span>
                    </div>
                    <div class="bg-emerald-50 p-3.5 rounded-xl border border-emerald-200/60">
                        <span class="text-[11px] text-emerald-700 block font-semibold">Dana Telah Dicairkan</span>
                        <span class="text-lg font-black text-emerald-800 mt-1 block">
                            Rp {{ number_format($disbursement->amount ?? $submission->total_budget_approved, 0, ',', '.') }}
                        </span>
                    </div>
                    <div class="bg-blue-50 p-3.5 rounded-xl border border-blue-200/60">
                        <span class="text-[11px] text-blue-700 block font-semibold">No. Tiket SIMPAY</span>
                        <span class="text-xs font-bold text-blue-900 mt-1 block">
                            {{ $disbursement->disbursement_code ?? 'TKT-PENDING' }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Berkas LPJ yang Diunggah Mahasiswa -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-5 space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div class="flex items-center space-x-2">
                        <i class="fa-solid fa-folder-open text-blue-600"></i>
                        <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider">Berkas Pertanggungjawaban (LPJ) Mahasiswa</h3>
                    </div>
                    <span class="text-xs font-bold text-slate-500">Status: {{ ucfirst($submission->lpjReport?->status ?? 'Diunggah') }}</span>
                </div>

                @if($submission->lpjReport)
                    <div class="space-y-3">
                        <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/70 flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="w-9 h-9 rounded-lg bg-red-100 text-red-600 flex items-center justify-center text-base">
                                    <i class="fa-solid fa-file-pdf"></i>
                                </div>
                                <div>
                                    <span class="text-xs font-bold text-slate-800 block">Dokumen Narasi & Laporan LPJ</span>
                                    <span class="text-[11px] text-slate-400">Diunggah: {{ $submission->lpjReport->created_at ? $submission->lpjReport->created_at->format('d M Y H:i') : '-' }}</span>
                                </div>
                            </div>
                            @if($submission->lpjReport->report_file_path)
                                <a href="{{ asset('storage/' . $submission->lpjReport->report_file_path) }}" target="_blank"
                                    class="px-3 py-1.5 rounded-lg text-xs font-bold bg-blue-600 hover:bg-blue-700 text-white transition">
                                    <i class="fa-solid fa-download mr-1"></i> Buka LPJ
                                </a>
                            @else
                                <span class="text-xs text-slate-400 italic">File tidak tersedia</span>
                            @endif
                        </div>

                        <!-- Lampiran Kuitansi / Nota Belanja -->
                        @if($submission->lpjReport->attachments && $submission->lpjReport->attachments->count() > 0)
                            <div class="border-t border-slate-100 pt-3">
                                <span class="text-xs font-bold text-slate-700 block mb-2">Lampiran Bukti Nota & Kuitansi ({{ $submission->lpjReport->attachments->count() }} Berkas):</span>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                    @foreach($submission->lpjReport->attachments as $att)
                                        <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-between text-xs">
                                            <span class="font-medium text-slate-700 truncate max-w-[180px]">{{ $att->file_name ?? 'Bukti Nota' }}</span>
                                            <a href="{{ asset('storage/' . $att->file_path) }}" target="_blank"
                                                class="text-blue-600 hover:text-blue-800 font-bold ml-2 shrink-0">
                                                Lihat
                                            </a>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                @else
                    <div class="p-8 text-center text-xs text-slate-400">
                        <i class="fa-solid fa-file-circle-xmark text-2xl mb-2 text-slate-300"></i>
                        <p>Mahasiswa belum mengunggah berkas LPJ resmi ke sistem.</p>
                    </div>
                @endif
            </div>

        </div>

        <!-- Kolom Kanan: Form Verifikasi LPJ Keuangan -->
        <div>
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-5 space-y-4">
                <div class="flex items-center space-x-2 border-b border-slate-100 pb-3">
                    <i class="fa-solid fa-stamp text-blue-600"></i>
                    <h3 class="text-xs font-black uppercase tracking-wider text-slate-800">Audit & Validasi LPJ Keuangan</h3>
                </div>

                <form action="{{ route('keuangan.lpj.process', $submission) }}" method="POST" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Hasil Pemeriksaan LPJ <span class="text-red-500">*</span></label>
                        <div class="space-y-2">
                            <label class="flex items-center p-3 rounded-xl border border-slate-200 hover:border-emerald-500 cursor-pointer transition has-[:checked]:border-emerald-600 has-[:checked]:bg-emerald-50/50">
                                <input type="radio" name="status" value="verified" checked class="text-emerald-600 focus:ring-emerald-500">
                                <div class="ml-3">
                                    <span class="text-xs font-bold text-slate-800 block">Terverifikasi & Sah</span>
                                    <span class="text-[11px] text-slate-500 block">Kuitansi lengkap, pengeluaran sesuai anggaran.</span>
                                </div>
                            </label>

                            <label class="flex items-center p-3 rounded-xl border border-slate-200 hover:border-amber-500 cursor-pointer transition has-[:checked]:border-amber-600 has-[:checked]:bg-amber-50/50">
                                <input type="radio" name="status" value="revision" class="text-amber-600 focus:ring-amber-500">
                                <div class="ml-3">
                                    <span class="text-xs font-bold text-slate-800 block">Perlu Perbaikan</span>
                                    <span class="text-[11px] text-slate-500 block">Nota belanja kurang jelas / perlu revisi mahasiswa.</span>
                                </div>
                            </label>

                            <label class="flex items-center p-3 rounded-xl border border-slate-200 hover:border-red-500 cursor-pointer transition has-[:checked]:border-red-600 has-[:checked]:bg-red-50/50">
                                <input type="radio" name="status" value="rejected" class="text-red-600 focus:ring-red-500">
                                <div class="ml-3">
                                    <span class="text-xs font-bold text-slate-800 block">Tidak Sesuai</span>
                                    <span class="text-[11px] text-slate-500 block">Penggunaan dana tidak dapat dipertanggungjawabkan.</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Total Realisasi Belanja (Rp)</label>
                        <input type="number" name="actual_spent" value="{{ $submission->total_budget_approved }}"
                            class="w-full text-xs border border-slate-300 rounded-xl px-3 py-2 font-bold text-slate-800 focus:ring-2 focus:ring-blue-600">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Sisa Dana yang Dikembalikan (Rp) <span class="text-slate-400 font-normal">(Jika ada)</span></label>
                        <input type="number" name="refund_amount" value="0" min="0"
                            class="w-full text-xs border border-slate-300 rounded-xl px-3 py-2 focus:ring-2 focus:ring-blue-600">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Catatan Audit Keuangan</label>
                        <textarea name="notes" rows="3" placeholder="Catatan kesesuaian faktur atau rincian perbaikan..."
                            class="w-full text-xs border border-slate-300 rounded-xl p-3 focus:ring-2 focus:ring-blue-600"></textarea>
                    </div>

                    <button type="submit" class="w-full py-2.5 rounded-xl text-xs font-bold bg-blue-700 hover:bg-blue-800 text-white shadow-sm hover:shadow transition flex items-center justify-center space-x-2">
                        <i class="fa-solid fa-check"></i>
                        <span>Simpan Hasil Pemeriksaan LPJ</span>
                    </button>
                </form>
            </div>
        </div>

    </div>

</div>
@endsection
