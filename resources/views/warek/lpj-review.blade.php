@extends('layouts.app')

@section('title', 'Validasi LPJ & SKPI — ' . $submission->code)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Breadcrumb & Header -->
    <div class="flex items-center space-x-2 text-xs text-slate-500">
        <a href="{{ route('warek.dashboard') }}" class="hover:text-amber-700">Dashboard Warek</a>
        <span>/</span>
        <span class="text-slate-800 font-semibold">Validasi LPJ & Prestasi [{{ $submission->code }}]</span>
    </div>

    <div>
        <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight">Validasi Laporan Pertanggungjawaban (LPJ) & Capaian Prestasi</h1>
        <p class="text-xs text-slate-500">Lakukan verifikasi bukti keuangan riil, pengesahan keaslian sertifikat kejuaraan, dan penetapan poin SKPI.</p>
    </div>

    <!-- INFORMASI PENCAIRAN SEBELUMNYA -->
    <div class="bg-emerald-50 border border-emerald-200 rounded-2xl p-4 text-xs text-emerald-950 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <span class="text-[10px] uppercase font-bold text-emerald-700">Lomba:</span>
            <div class="font-bold text-sm text-emerald-900">{{ $submission->competitionDetail?->competition_name }}</div>
            <div class="text-emerald-700">Ketua Tim: {{ $submission->mahasiswa_nim }} (Fakultas {{ $submission->fakultas_id }})</div>
        </div>
        @if($decision)
            <div class="sm:text-right">
                <span class="text-[10px] uppercase font-bold text-emerald-700">Plafon Dana Telah Dicairkan:</span>
                <div class="font-extrabold text-base text-emerald-900">Rp {{ number_format($decision->approved_amount, 0, ',', '.') }}</div>
            </div>
        @endif
    </div>

    <!-- LAPORAN NARATIF LPJ -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 space-y-3">
        <h3 class="text-sm font-bold text-slate-800 border-b border-slate-100 pb-3 flex items-center">
            <i class="fa-solid fa-file-lines text-purple-600 mr-2"></i>
            Laporan Narasi Hasil Perlombaan
        </h3>
        <p class="text-xs text-slate-700 leading-relaxed whitespace-pre-line bg-slate-50 p-4 rounded-xl border border-slate-200 font-mono">
            {{ $submission->lpjReport?->report_text ?? 'Belum ada narasi kegiatan.' }}
        </p>
    </div>

    <!-- BUKTI NOTA & KWITANSI KEUANGAN -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 space-y-4">
        <h3 class="text-sm font-bold text-slate-800 border-b border-slate-100 pb-3 flex items-center">
            <i class="fa-solid fa-receipt text-emerald-600 mr-2"></i>
            Bukti Fisik Nota & Kwitansi Pengeluaran
        </h3>

        @if($submission->lpjReport?->attachments?->isNotEmpty())
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                @foreach($submission->lpjReport->attachments as $att)
                    <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50 border border-slate-200">
                        <div class="flex items-center space-x-2 truncate">
                            <i class="fa-solid fa-file text-emerald-600 text-lg"></i>
                            <div class="truncate">
                                <div class="font-bold text-slate-800 truncate">{{ $att->file_name }}</div>
                                <div class="text-[10px] text-slate-400">{{ number_format($att->file_size / 1024, 0) }} KB</div>
                            </div>
                        </div>
                        <span class="text-[10px] px-2 py-0.5 rounded font-bold uppercase bg-slate-200 text-slate-700">Lampiran</span>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-slate-400 text-xs">Tidak ada lampiran nota yang diunggah.</div>
        @endif
    </div>

    <!-- SERTIFIKAT PRESTASI -->
    @php $cert = $submission->certificates->first(); @endphp
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 space-y-4">
        <h3 class="text-sm font-bold text-slate-800 border-b border-slate-100 pb-3 flex items-center">
            <i class="fa-solid fa-trophy text-amber-500 mr-2"></i>
            Capaian Prestasi & Sertifikat Juara
        </h3>

        @if($cert)
            <div class="flex items-center justify-between bg-purple-50 p-4 rounded-xl border border-purple-200 text-xs">
                <div>
                    <span class="text-[10px] uppercase font-bold text-purple-700">Peringkat yang Diajukan:</span>
                    <div class="font-extrabold text-base text-purple-950 mt-0.5">{{ $cert->achievement_rank }}</div>
                    <div class="text-[11px] text-purple-600">Berkas sertifikat: {{ basename($cert->certificate_file_path) }}</div>
                </div>
                <span class="px-3 py-1 bg-purple-600 text-white font-bold rounded-lg text-xs">Terlampir</span>
            </div>
        @else
            <div class="text-slate-400 text-xs">Tidak ada sertifikat kejuaraan yang dilampirkan.</div>
        @endif
    </div>

    <!-- FORM VALIDASI & PENGESAHAN OLEH WAREK -->
    <div class="bg-white rounded-2xl border-2 border-purple-300 shadow-md p-6 space-y-4">
        <div class="flex items-center space-x-2 border-b border-slate-100 pb-3">
            <i class="fa-solid fa-stamp text-purple-600 text-xl"></i>
            <div>
                <h3 class="text-sm font-bold text-slate-800">Form Pengesahan LPJ & Konversi Poin SKPI</h3>
                <p class="text-[11px] text-slate-400">Validasi Anda akan membuka status cekal LPJ mahasiswa dan mencatat poin prestasi ke buku SKPI.</p>
            </div>
        </div>

        <form action="{{ route('warek.lpj.process', $submission->id) }}" method="POST" class="space-y-4">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                <div>
                    <label class="block font-bold text-slate-800 mb-1">Keputusan Status LPJ <span class="text-rose-500">*</span></label>
                    <select name="is_valid" required class="w-full px-3 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-purple-500 outline-none bg-white font-semibold">
                        <option value="1" selected>VALID (Sahkan LPJ & Buka Blokir Mahasiswa)</option>
                        <option value="0">REVISI (Kembalikan ke Mahasiswa untuk Lengkapi Nota)</option>
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-slate-800 mb-1">Poin SKPI yang Disahkan</label>
                    <input type="number" name="skpi_points" value="{{ old('skpi_points', 2.00) }}" step="0.25" min="0" max="10"
                        class="w-full px-3 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-purple-500 outline-none font-bold text-purple-800">
                    <span class="text-[10px] text-slate-400 mt-1 block">Standar: Juara 1 (3.00), Juara 2 (2.50), Juara 3 (2.00), Finalis (1.00).</span>
                </div>

                <div>
                    <label class="block font-bold text-slate-800 mb-1">Nominal Bantuan Insentif Juara (Opsional - Rp)</label>
                    <input type="number" name="incentive_amount" value="{{ old('incentive_amount', 1000000) }}" step="100000" min="0"
                        class="w-full px-3 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-purple-500 outline-none font-bold text-emerald-800">
                    <span class="text-[10px] text-slate-400 mt-1 block">Insentif prestasi mahasiswa dari universitas jika meraih juara.</span>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Catatan Validasi Warek</label>
                    <textarea name="notes" rows="2" class="w-full px-3 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-purple-500 outline-none"
                        placeholder="Catatan apresiasi atau instruksi perbaikan nota...">{{ old('notes', 'LPJ dan keabsahan sertifikat telah diperiksa dan disetujui.') }}</textarea>
                </div>
            </div>

            <div class="flex items-center justify-end space-x-3 pt-4 border-t border-slate-100">
                <a href="{{ route('warek.dashboard') }}" class="px-5 py-2.5 rounded-xl border border-slate-300 text-slate-700 font-bold text-xs hover:bg-slate-100 transition">
                    Kembali
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-purple-700 hover:bg-purple-800 text-white font-extrabold text-xs shadow-md transition flex items-center space-x-1.5">
                    <i class="fa-solid fa-award"></i>
                    <span>Sahkan LPJ, Poin SKPI, & Selesaikan (Completed)</span>
                </button>
            </div>
        </form>
    </div>

</div>
@endsection

