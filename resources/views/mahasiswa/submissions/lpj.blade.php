@extends('layouts.app')

@section('title', 'Unggah LPJ & Prestasi — ' . $submission->code)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Breadcrumb & Header -->
    <div class="flex items-center space-x-2 text-xs text-slate-500">
        <a href="{{ route('mahasiswa.dashboard') }}" class="hover:text-blue-700 font-medium">Dashboard</a>
        <span>/</span>
        <a href="{{ route('mahasiswa.submissions.show', $submission->id) }}" class="hover:text-blue-700 font-medium">{{ $submission->code }}</a>
        <span>/</span>
        <span class="text-slate-800 font-semibold">Laporan Pertanggungjawaban (LPJ) & Klaim Sertifikat</span>
    </div>

    <div>
        <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight">Pelaporan LPJ & Klaim Prestasi</h1>
        <p class="text-xs text-slate-500">Unggah laporan kegiatan pasca-lomba, nota kuitansi pengeluaran dana, dan sertifikat juara untuk klaim poin SKPI.</p>
    </div>

    @if(auth()->user()->isAdmin())
        <div class="bg-blue-50 border border-blue-200 text-blue-950 p-4 rounded-2xl flex items-center space-x-3 text-xs">
            <i class="fa-solid fa-eye text-blue-600 text-lg"></i>
            <div>
                <strong class="font-bold">Mode Peninjauan Administrator (Read-Only):</strong>
                <span>Anda dapat melihat format pelaporan LPJ & klaim prestasi, namun pengiriman dokumen hanya dapat dilakukan oleh Mahasiswa pengusul.</span>
            </div>
        </div>
    @endif

    <form action="{{ route('mahasiswa.submissions.lpj.submit', $submission->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <!-- CARD 1: RINGKASAN LOMBA -->
        <div class="bg-blue-50 border border-blue-200 rounded-2xl p-5 text-xs text-blue-950 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <span class="text-[10px] uppercase font-bold text-blue-700">Delegasi Lomba:</span>
                <div class="font-bold text-sm text-blue-900">{{ $submission->competitionDetail?->competition_name }}</div>
                <div class="text-blue-700 mt-0.5">Penyelenggara: {{ $submission->competitionDetail?->organizer }} &bull; Tingkat: {{ $submission->competitionDetail?->level?->label() }}</div>
            </div>
            @if($decision)
                <div class="sm:text-right">
                    <span class="text-[10px] uppercase font-bold text-blue-700">Dana Dicairkan:</span>
                    <div class="font-extrabold text-base text-blue-900">Rp {{ number_format($decision->approved_amount, 0, ',', '.') }}</div>
                </div>
            @endif
        </div>

        <!-- CARD 2: LAPORAN KEGIATAN -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 space-y-4">
            <h3 class="text-sm font-bold text-slate-800 border-b border-slate-100 pb-3 flex items-center">
                <i class="fa-solid fa-file-lines text-blue-600 mr-2"></i>
                Laporan Narasi Hasil Kegiatan
            </h3>

            <div class="text-xs space-y-1">
                <label class="block font-semibold text-slate-700">Deskripsi Pelaksanaan & Capaian Lomba <span class="text-rose-500">*</span></label>
                <textarea name="report_text" rows="5" required
                    class="w-full px-3 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none leading-relaxed"
                    placeholder="Jelaskan secara ringkas jalannya perlombaan, babak yang dilalui (penyisihan, semifinal, final), dan hasil akhir delegasi...">{{ old('report_text', $submission->lpjReport?->report_text) }}</textarea>
            </div>
        </div>

        <!-- CARD 3: BUKTI NOTA PENGELUARAN KEUANGAN -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 space-y-4">
            <h3 class="text-sm font-bold text-slate-800 border-b border-slate-100 pb-3 flex items-center">
                <i class="fa-solid fa-receipt text-blue-600 mr-2"></i>
                Lampiran Nota & Kuitansi Pengeluaran
            </h3>

            <div class="text-xs space-y-2">
                <label class="block font-semibold text-slate-700">Unggah Berkas Nota / Kuitansi (PDF, JPG, PNG)</label>
                <input type="file" name="receipt_files[]" multiple accept=".pdf,.jpg,.jpeg,.png"
                    class="block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 border border-slate-300 rounded-xl cursor-pointer">
                <span class="text-[10px] text-slate-400 block">Anda dapat memilih lebih dari satu file nota sekaligus. Format yang didukung: PDF, JPG, PNG (Maksimal 5MB per berkas).</span>
            </div>
        </div>

        <!-- CARD 4: PRESTASI & SERTIFIKAT JUARA -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 space-y-4">
            <h3 class="text-sm font-bold text-slate-800 border-b border-slate-100 pb-3 flex items-center">
                <i class="fa-solid fa-award text-amber-500 mr-2"></i>
                Sertifikat Prestasi & Poin SKPI
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Capaian Prestasi / Peringkat Juara <span class="text-rose-500">*</span></label>
                    <input type="text" name="achievement_rank" value="{{ old('achievement_rank', $submission->certificates()->first()?->achievement_rank ?? 'Juara 1') }}" required
                        class="w-full px-3 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none"
                        placeholder="Contoh: Juara 1 / Juara 2 / Juara Harapan / Finalis / Best Speaker">
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Unggah Sertifikat / Piagam (PDF, JPG, PNG)</label>
                    <input type="file" name="certificate_file" accept=".pdf,.jpg,.jpeg,.png"
                        class="block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 border border-slate-300 rounded-xl cursor-pointer">
                </div>
            </div>
        </div>

        <!-- SUBMIT BUTTON -->
        <div class="flex items-center justify-end space-x-3 pt-4">
            <a href="{{ route('mahasiswa.submissions.show', $submission->id) }}" class="px-5 py-2.5 rounded-xl border border-slate-300 text-slate-700 font-bold text-xs hover:bg-slate-100 transition">
                Kembali
            </a>
            @if(auth()->user()->isAdmin())
                <button type="button" disabled class="px-6 py-2.5 rounded-xl bg-slate-300 text-slate-600 font-bold text-xs cursor-not-allowed flex items-center space-x-2" title="Mode Administrator (Read-Only)">
                    <i class="fa-solid fa-lock"></i>
                    <span>Kirim Laporan LPJ (Nonaktif untuk Admin)</span>
                </button>
            @else
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md transition flex items-center space-x-2">
                    <i class="fa-solid fa-check-to-slot"></i>
                    <span>Kirim Laporan LPJ & Sertifikat</span>
                </button>
            @endif
        </div>

    </form>
</div>
@endsection

