@extends('layouts.app')

@section('title', 'Validasi LPJ & SKPI — ' . $submission->code)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Breadcrumb & Header -->
    <div class="flex items-center space-x-2 text-xs text-slate-500">
        <a href="{{ route('dekan.dashboard') }}" class="hover:text-purple-700">Dashboard Dekan</a>
        <span>/</span>
        <span class="text-slate-800 font-semibold">Validasi LPJ & SKPI [{{ $submission->code }}]</span>
    </div>

    <div>
        <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight">Validasi Laporan Pertanggungjawaban (LPJ) & Poin SKPI</h1>
        <p class="text-xs text-slate-500">Verifikasi bukti kuitansi pengeluaran riil, pengesahan keaslian sertifikat kejuaraan, dan pencatatan poin SKPI mahasiswa.</p>
    </div>

    <!-- INFORMASI PENCAIRAN SEBELUMNYA -->
    <div class="bg-blue-50 border border-blue-200 rounded-2xl p-4 text-xs text-blue-950 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <span class="text-[10px] uppercase font-bold text-blue-700">Kegiatan Perlombaan:</span>
            <div class="font-bold text-sm text-blue-900">{{ $submission->competitionDetail?->competition_name ?? $submission->title }}</div>
            <div class="text-blue-700">Ketua Tim: {{ $submission->submitter_name ?? $submission->submitter_nim }} (Fakultas {{ $submission->fakultas_id }})</div>
        </div>
        @if($decision)
            <div class="sm:text-right">
                <span class="text-[10px] uppercase font-bold text-blue-700">Plafon Bantuan Dana Disetujui:</span>
                <div class="font-extrabold text-base text-blue-900">Rp {{ number_format($decision->approved_amount, 0, ',', '.') }}</div>
            </div>
        @endif
    </div>

    <!-- LAPORAN NARATIF LPJ -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 space-y-3">
        <h3 class="text-sm font-bold text-slate-800 border-b border-slate-100 pb-3 flex items-center">
            <i class="fa-solid fa-file-lines text-blue-600 mr-2"></i>
            Laporan Narasi Hasil Perlombaan
        </h3>
        <p class="text-xs text-slate-700 leading-relaxed whitespace-pre-line bg-slate-50 p-4 rounded-xl border border-slate-200 font-mono">
            {{ $submission->lpjReport?->report_text ?? 'Belum ada narasi kegiatan.' }}
        </p>
    </div>

    <!-- BUKTI NOTA & KWITANSI KEUANGAN -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 space-y-4">
        <h3 class="text-sm font-bold text-slate-800 border-b border-slate-100 pb-3 flex items-center">
            <i class="fa-solid fa-receipt text-blue-600 mr-2"></i>
            Bukti Fisik Nota & Kwitansi Pengeluaran Riil
        </h3>

        @if($submission->lpjReport?->attachments?->isNotEmpty())
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                @foreach($submission->lpjReport->attachments as $att)
                    <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50 border border-slate-200">
                        <div class="flex items-center space-x-2 truncate">
                            <i class="fa-solid fa-file text-blue-600 text-lg"></i>
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
            <div class="flex items-center justify-between bg-blue-50 p-4 rounded-xl border border-blue-200 text-xs">
                <div>
                    <span class="text-[10px] uppercase font-bold text-blue-700">Peringkat yang Diraih:</span>
                    <div class="font-extrabold text-base text-blue-950 mt-0.5">{{ $cert->achievement_rank }}</div>
                    <div class="text-[11px] text-blue-600">Berkas sertifikat: {{ basename($cert->certificate_file_path) }}</div>
                </div>
                <span class="px-3 py-1 bg-blue-600 text-white font-bold rounded-lg text-xs">Terlampir</span>
            </div>
        @else
            <div class="text-slate-400 text-xs">Tidak ada sertifikat kejuaraan yang dilampirkan.</div>
        @endif
    </div>

    <!-- FORM VALIDASI & PENGESAHAN OLEH DEKAN -->
    @if(auth()->user()->isAdmin())
        <div class="bg-blue-50/70 border-2 border-blue-200 rounded-2xl p-6 text-blue-950 flex items-start space-x-3 shadow-sm">
            <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-sm shrink-0">
                <i class="fa-solid fa-eye"></i>
            </div>
            <div>
                <h3 class="text-sm font-bold text-slate-800">Mode Peninjauan Administrator (Read-Only)</h3>
                <p class="text-xs text-blue-700 mt-1 leading-relaxed">
                    Anda sedang meninjau laporan LPJ dan sertifikat mahasiswa ini sebagai Administrator. Pengesahan status LPJ, pembukaan blokir mahasiswa, dan konversi poin SKPI hanya dapat dieksekusi oleh Dekan Fakultas.
                </p>
                <div class="mt-3">
                    <a href="{{ route('dekan.dashboard') }}" class="inline-flex items-center space-x-1.5 px-4 py-2 rounded-xl bg-white border border-slate-200 text-xs font-bold text-slate-700 hover:bg-slate-50 transition">
                        <i class="fa-solid fa-arrow-left"></i>
                        <span>Kembali ke Dashboard Dekan</span>
                    </a>
                </div>
            </div>
        </div>
    @else
        <div class="bg-white rounded-2xl border-2 border-blue-300 shadow-md p-6 space-y-4">
            <div class="flex items-center space-x-2 border-b border-slate-100 pb-3">
                <i class="fa-solid fa-stamp text-blue-600 text-xl"></i>
                <div>
                    <h3 class="text-sm font-bold text-slate-800">Form Pengesahan LPJ & Konversi Poin SKPI oleh Dekan</h3>
                    <p class="text-[11px] text-slate-400">Pengesahan Dekan akan otomatis membuka status cekal LPJ mahasiswa dan mencatat poin prestasi ke SKPI.</p>
                </div>
            </div>

            <form action="{{ route('dekan.lpj.process', $submission->id) }}" method="POST" class="space-y-4">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    <div>
                        <label class="block font-bold text-slate-800 mb-1">Keputusan Status LPJ <span class="text-rose-500">*</span></label>
                        <select name="is_valid" required class="w-full px-3 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none bg-white font-semibold">
                            <option value="1" selected>VALID (Sahkan LPJ & Buka Blokir Mahasiswa)</option>
                            <option value="0">REVISI (Kembalikan ke Mahasiswa untuk Lengkapi Nota)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-800 mb-1">Poin SKPI yang Disahkan</label>
                        <input type="number" name="skpi_points" value="{{ old('skpi_points', 2.00) }}" step="0.25" min="0" max="10"
                            class="w-full px-3 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none font-bold text-blue-900">
                        <span class="text-[10px] text-slate-400 mt-1 block">Standar: Juara 1 (3.00), Juara 2 (2.50), Juara 3 (2.00), Finalis (1.00).</span>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-800 mb-1">Nominal Bantuan Insentif Juara (Opsional - Rp)</label>
                        <input type="number" name="incentive_amount" value="{{ old('incentive_amount', 1000000) }}" step="100000" min="0"
                            class="w-full px-3 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none font-bold text-blue-900">
                        <span class="text-[10px] text-slate-400 mt-1 block">Insentif prestasi mahasiswa dari fakultas jika meraih juara.</span>
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Catatan Validasi Dekan</label>
                        <textarea name="notes" rows="2" class="w-full px-3 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none"
                            placeholder="Catatan apresiasi atau instruksi perbaikan nota...">{{ old('notes', 'LPJ dan keabsahan sertifikat telah diverifikasi dan disahkan oleh Dekan Fakultas.') }}</textarea>
                    </div>
                </div>

                <div class="flex items-center justify-end space-x-3 pt-4 border-t border-slate-100">
                    <a href="{{ route('dekan.dashboard') }}" class="px-5 py-2.5 rounded-xl border border-slate-300 text-slate-700 font-bold text-xs hover:bg-slate-100 transition">
                        Kembali
                    </a>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-extrabold text-xs shadow-md transition flex items-center space-x-1.5">
                        <i class="fa-solid fa-award"></i>
                        <span>Sahkan LPJ, Poin SKPI, & Selesaikan (Completed)</span>
                    </button>
                </div>
            </form>
        </div>
    @endif

</div>
@endsection

