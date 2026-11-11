@extends('layouts.app')

@section('title', 'Persetujuan Final Warek II [' . $submission->code . '] — SIPRES UNUJA')

@section('content')
<div class="space-y-6">

    <!-- Header & Breadcrumb -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2 text-xs text-slate-500 mb-1">
                <a href="{{ route('warek2.dashboard') }}" class="hover:text-purple-700">Dashboard Warek II</a>
                <i class="fa-solid fa-chevron-right text-[10px] text-slate-400"></i>
                <span>Persetujuan Final Universitas</span>
            </div>
            <h1 class="text-xl font-extrabold text-slate-800 tracking-tight">Keputusan Akhir Wakil Rektor II & Penetapan Dana</h1>
            <p class="text-xs text-slate-500">Persetujuan final tingkat universitas dan penerbitan tiket pencairan dana ke Biro Keuangan.</p>
        </div>
        <a href="{{ route('warek2.dashboard') }}"
            class="inline-flex items-center space-x-2 px-3.5 py-2 rounded-xl text-xs font-semibold text-slate-600 bg-white border border-slate-200 hover:bg-slate-50 shadow-xs self-start sm:self-auto transition">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Kembali ke Dashboard</span>
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Kolom Kiri: Detail Lomba, RAB, & Riwayat Review -->
        <div class="lg:col-span-2 space-y-6">

            <!-- Card Info Lomba -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-5 space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-xl bg-purple-100 text-purple-700 flex items-center justify-center text-lg font-bold">
                            <i class="fa-solid fa-trophy"></i>
                        </div>
                        <div>
                            <h2 class="text-sm font-extrabold text-slate-800">{{ $submission->competitionDetail?->name ?? $submission->title }}</h2>
                            <span class="text-xs text-slate-400">Kode: {{ $submission->code }}</span>
                        </div>
                    </div>
                    <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-purple-50 text-purple-700 border border-purple-200">
                        Tingkat {{ $submission->competitionDetail?->level ?? 'Nasional' }}
                    </span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    <div>
                        <span class="text-slate-400 block font-medium">Ketua Delegasi</span>
                        <span class="font-bold text-slate-800 block mt-0.5">{{ $submission->submitter_name }}</span>
                        <span class="text-slate-500 text-[11px]">NIM: {{ $submission->submitter_nim }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block font-medium">Fakultas / Program Studi</span>
                        <span class="font-bold text-slate-800 block mt-0.5">{{ $submission->fakultas_id }}</span>
                        <span class="text-slate-500 text-[11px]">{{ $submission->study_program_name ?? $submission->study_program_id }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block font-medium">Dosen Pendamping Resmi</span>
                        <span class="font-bold text-slate-800 block mt-0.5">{{ $submission->supervisor_name ?? 'Belum Ditunjuk' }}</span>
                        <span class="text-slate-500 text-[11px]">NIDN: {{ $submission->supervisor_nidn ?? '-' }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block font-medium">Proposal Terlampir</span>
                        @if($submission->proposal_path)
                            <a href="{{ asset('storage/' . $submission->proposal_path) }}" target="_blank"
                                class="inline-flex items-center space-x-1.5 text-purple-700 font-bold hover:underline mt-1">
                                <i class="fa-solid fa-file-pdf"></i>
                                <span>Buka Dokumen Proposal</span>
                            </a>
                        @else
                            <span class="text-slate-400 italic">Tidak ada berkas</span>
                        @endif
                    </div>
                </div>

                @if($submission->members && $submission->members->count() > 0)
                    <div class="border-t border-slate-100 pt-3">
                        <span class="text-xs font-bold text-slate-700 block mb-2">Anggota Tim Delegasi:</span>
                        <div class="flex flex-wrap gap-2">
                            @foreach($submission->members as $member)
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-slate-50 border border-slate-200 text-xs text-slate-700">
                                    <i class="fa-solid fa-user text-slate-400 mr-1.5 text-[10px]"></i>
                                    <strong>{{ $member->student_name ?? $member->name }}</strong>
                                    <span class="text-slate-400 ml-1">({{ $member->student_nim ?? $member->nim }})</span>
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <!-- Catatan Berjenjang (Dekan & Keuangan) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="p-4 rounded-2xl bg-indigo-50/60 border border-indigo-100 space-y-2">
                    <div class="flex items-center space-x-2 text-indigo-900 font-bold text-xs">
                        <i class="fa-solid fa-award text-indigo-600"></i>
                        <span>Rekomendasi Dekan Fakultas</span>
                    </div>
                    <span class="text-xl font-black text-indigo-950 block">
                        Rp {{ number_format($submission->total_budget_approved ?: $submission->total_budget_requested, 0, ',', '.') }}
                    </span>
                    <p class="text-xs text-indigo-900/70 italic">
                        {{ $budget->verification_notes ?? 'Telah diverifikasi dan disetujui di tingkat fakultas.' }}
                    </p>
                </div>

                <div class="p-4 rounded-2xl bg-emerald-50/60 border border-emerald-100 space-y-2">
                    <div class="flex items-center space-x-2 text-emerald-900 font-bold text-xs">
                        <i class="fa-solid fa-calculator text-emerald-600"></i>
                        <span>Verifikasi Administrasi Keuangan</span>
                    </div>
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                        <i class="fa-solid fa-check-circle mr-1"></i> Anggaran Lolos Telaah
                    </span>
                    <p class="text-xs text-emerald-900/70 italic">
                        Kepatuhan pagu anggaran dan rincian komponen RAB telah diverifikasi oleh Biro Keuangan.
                    </p>
                </div>
            </div>

        </div>

        <!-- Kolom Kanan: Form Penetapan Final Warek II -->
        <div>
            <div class="bg-white rounded-2xl border border-purple-200 shadow-md p-5 space-y-4">
                <div class="flex items-center space-x-2 border-b border-slate-100 pb-3">
                    <i class="fa-solid fa-crown text-[#7E22CE]"></i>
                    <h3 class="text-xs font-black uppercase tracking-wider text-slate-800">Persetujuan Final Warek II</h3>
                </div>

                <form action="{{ route('warek2.process', $submission) }}" method="POST" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Keputusan Pimpinan Universitas <span class="text-red-500">*</span></label>
                        <div class="space-y-2">
                            <label class="flex items-center p-3 rounded-xl border border-slate-200 hover:border-purple-500 cursor-pointer transition has-[:checked]:border-purple-600 has-[:checked]:bg-purple-50/50">
                                <input type="radio" name="action" value="APPROVE" checked class="text-[#7E22CE] focus:ring-purple-500">
                                <div class="ml-3">
                                    <span class="text-xs font-bold text-slate-800 block">Setujui Plafon Final Universitas</span>
                                    <span class="text-[11px] text-slate-500 block">Sah secara definitif, tiket pencairan dikirim ke Keuangan.</span>
                                </div>
                            </label>

                            <label class="flex items-center p-3 rounded-xl border border-slate-200 hover:border-amber-500 cursor-pointer transition has-[:checked]:border-amber-600 has-[:checked]:bg-amber-50/50">
                                <input type="radio" name="action" value="REVISE" class="text-amber-600 focus:ring-amber-500">
                                <div class="ml-3">
                                    <span class="text-xs font-bold text-slate-800 block">Minta Perbaikan Proposal</span>
                                    <span class="text-[11px] text-slate-500 block">Kembalikan ke pengusul untuk perbaikan substantif.</span>
                                </div>
                            </label>

                            <label class="flex items-center p-3 rounded-xl border border-slate-200 hover:border-red-500 cursor-pointer transition has-[:checked]:border-red-600 has-[:checked]:bg-red-50/50">
                                <input type="radio" name="action" value="REJECT" class="text-red-600 focus:ring-red-500">
                                <div class="ml-3">
                                    <span class="text-xs font-bold text-slate-800 block">Tolak Pengajuan</span>
                                    <span class="text-[11px] text-slate-500 block">Pengajuan tidak dapat disetujui di tingkat universitas.</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Nominal Plafon Disetujui Final (Rp)</label>
                        <input type="number" name="approved_amount"
                            value="{{ $submission->total_budget_approved ?: $submission->total_budget_requested }}"
                            min="0" required
                            class="w-full text-xs border border-slate-300 rounded-xl px-3 py-2 font-black text-purple-900 focus:ring-2 focus:ring-purple-600">
                        <span class="text-[10px] text-slate-400 mt-1 block">Dapat disesuaikan dengan pagu anggaran tahunan universitas.</span>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Catatan Pengesahan Warek II</label>
                        <textarea name="notes" rows="3" placeholder="Pesan motivasi untuk mahasiswa atau disposisi administrasi..."
                            class="w-full text-xs border border-slate-300 rounded-xl p-3 focus:ring-2 focus:ring-purple-600"></textarea>
                    </div>

                    <button type="submit" class="w-full py-2.5 rounded-xl text-xs font-bold bg-[#7E22CE] hover:bg-[#6B21A8] text-white shadow-md hover:shadow-lg transition flex items-center justify-center space-x-2">
                        <i class="fa-solid fa-stamp"></i>
                        <span>Sahkan Keputusan Final Warek II</span>
                    </button>
                </form>
            </div>
        </div>

    </div>

</div>
@endsection

