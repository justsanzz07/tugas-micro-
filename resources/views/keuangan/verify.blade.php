@extends('layouts.app')

@section('title', 'Telaah Anggaran [' . $submission->code . '] — Keuangan SIPRES')

@section('content')
<div class="space-y-6">

    <!-- Header & Breadcrumb -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2 text-xs text-slate-500 mb-1">
                <a href="{{ route('keuangan.dashboard') }}" class="hover:text-blue-700">Dashboard Keuangan</a>
                <i class="fa-solid fa-chevron-right text-[10px] text-slate-400"></i>
                <span>Telaah & Verifikasi Anggaran</span>
            </div>
            <h1 class="text-xl font-extrabold text-slate-800 tracking-tight">Verifikasi Kelayakan Anggaran Kegiatan</h1>
            <p class="text-xs text-slate-500">Pemeriksaan komponen RAB, batas plafon fakultas, dan kelayakan biaya lomba.</p>
        </div>
        <a href="{{ route('keuangan.dashboard') }}"
            class="inline-flex items-center space-x-2 px-3.5 py-2 rounded-xl text-xs font-semibold text-slate-600 bg-white border border-slate-200 hover:bg-slate-50 shadow-xs self-start sm:self-auto transition">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Kembali ke Dashboard</span>
        </a>
    </div>

    <!-- Ringkasan Pengajuan & Rekomendasi Dekan -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Kolom Kiri: Detail Informasi & RAB -->
        <div class="lg:col-span-2 space-y-6">

            <!-- Detail Lomba & Tim -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-5 space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div class="flex items-center space-x-2.5">
                        <span class="w-8 h-8 rounded-lg bg-blue-50 text-blue-700 flex items-center justify-center text-sm font-bold">
                            <i class="fa-solid fa-trophy"></i>
                        </span>
                        <div>
                            <h2 class="text-sm font-extrabold text-slate-800">{{ $submission->competitionDetail?->name ?? $submission->title }}</h2>
                            <span class="text-xs text-slate-400 font-medium">Kode: {{ $submission->code }}</span>
                        </div>
                    </div>
                    <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200">
                        {{ $submission->competitionDetail?->level ?? 'Nasional' }}
                    </span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    <div>
                        <span class="text-slate-400 block font-medium">Ketua Pengusul</span>
                        <span class="font-bold text-slate-800 block mt-0.5">{{ $submission->submitter_name }}</span>
                        <span class="text-slate-500 text-[11px]">NIM: {{ $submission->submitter_nim }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block font-medium">Fakultas & Program Studi</span>
                        <span class="font-bold text-slate-800 block mt-0.5">{{ $submission->fakultas_id }}</span>
                        <span class="text-slate-500 text-[11px]">{{ $submission->study_program_name ?? $submission->study_program_id }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block font-medium">Dosen Pendamping</span>
                        <span class="font-bold text-slate-800 block mt-0.5">{{ $submission->supervisor_name ?? 'Belum Ditunjuk' }}</span>
                        <span class="text-slate-500 text-[11px]">NIDN: {{ $submission->supervisor_nidn ?? '-' }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block font-medium">Dokumen Proposal</span>
                        @if($submission->proposal_path)
                            <a href="{{ asset('storage/' . $submission->proposal_path) }}" target="_blank"
                                class="inline-flex items-center space-x-1.5 text-blue-600 font-bold hover:underline mt-1">
                                <i class="fa-solid fa-file-pdf"></i>
                                <span>Unduh / Buka Proposal Kegiatan</span>
                            </a>
                        @else
                            <span class="text-slate-400 italic">Tidak ada berkas terlampir</span>
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

            <!-- Tabel Rincian Anggaran Biaya (RAB) -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                <div class="p-4 border-b border-slate-100 flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        <i class="fa-solid fa-list-check text-blue-600"></i>
                        <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider">Rincian Anggaran Biaya (RAB) Mahasiswa</h3>
                    </div>
                    <span class="text-xs font-bold text-slate-500">
                        Total Usulan: <span class="text-blue-900 font-black">Rp {{ number_format($submission->total_budget_requested, 0, ',', '.') }}</span>
                    </span>
                </div>

                @if($budget && $budget->items && $budget->items->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr class="bg-slate-50 text-slate-500 font-bold uppercase text-[10px]">
                                    <th class="py-2.5 px-4">No</th>
                                    <th class="py-2.5 px-4">Nama Item / Kebutuhan</th>
                                    <th class="py-2.5 px-4">Kategori</th>
                                    <th class="py-2.5 px-4 text-center">Volume</th>
                                    <th class="py-2.5 px-4 text-right">Harga Satuan</th>
                                    <th class="py-2.5 px-4 text-right">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($budget->items as $idx => $item)
                                    <tr class="hover:bg-slate-50/50">
                                        <td class="py-2.5 px-4 text-slate-400 font-semibold">{{ $idx + 1 }}</td>
                                        <td class="py-2.5 px-4 font-bold text-slate-800">{{ $item->item_name }}</td>
                                        <td class="py-2.5 px-4">
                                            <span class="px-2 py-0.5 rounded text-[10px] bg-slate-100 text-slate-700 font-medium">
                                                {{ $item->category ?? 'Operasional' }}
                                            </span>
                                        </td>
                                        <td class="py-2.5 px-4 text-center font-medium text-slate-600">{{ $item->quantity }} {{ $item->unit ?? 'Pcs' }}</td>
                                        <td class="py-2.5 px-4 text-right text-slate-600">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                                        <td class="py-2.5 px-4 text-right font-black text-slate-800">Rp {{ number_format($item->total_price, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="bg-slate-50/80 font-black text-xs text-slate-800">
                                    <td colspan="5" class="py-3 px-4 text-right uppercase tracking-wider">Total Usulan RAB:</td>
                                    <td class="py-3 px-4 text-right text-blue-900 text-sm">Rp {{ number_format($submission->total_budget_requested, 0, ',', '.') }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                @else
                    <div class="p-6 text-center text-xs text-slate-400">
                        <i class="fa-solid fa-file-invoice text-2xl mb-2 text-slate-300"></i>
                        <p>Rincian sub-item RAB diinput secara kumulatif.</p>
                        <p class="font-bold text-slate-700 mt-1">Total Usulan Pengajuan: Rp {{ number_format($submission->total_budget_requested, 0, ',', '.') }}</p>
                    </div>
                @endif
            </div>

        </div>

        <!-- Kolom Kanan: Rekomendasi Dekan & Form Keputusan Keuangan -->
        <div class="space-y-6">

            <!-- Rekomendasi Dekan -->
            <div class="bg-gradient-to-br from-purple-50 to-indigo-50/50 rounded-2xl border border-purple-200/80 p-5 space-y-3">
                <div class="flex items-center space-x-2 text-purple-900">
                    <i class="fa-solid fa-award text-purple-700"></i>
                    <h3 class="text-xs font-black uppercase tracking-wider">Rekomendasi Dekan Fakultas</h3>
                </div>
                <div class="bg-white rounded-xl p-3 border border-purple-100 shadow-xs">
                    <span class="text-[11px] text-slate-400 block font-semibold">Plafon Dana Disetujui Dekan</span>
                    <span class="text-2xl font-black text-purple-800 mt-0.5 block">
                        Rp {{ number_format($submission->total_budget_approved ?: $submission->total_budget_requested, 0, ',', '.') }}
                    </span>
                </div>
                @if($budget && $budget->verification_notes)
                    <div class="text-xs text-purple-950/80 bg-purple-100/50 p-3 rounded-xl border border-purple-200/50">
                        <span class="font-bold block mb-1">Catatan Dekan:</span>
                        <p class="italic">"{{ $budget->verification_notes }}"</p>
                    </div>
                @endif
            </div>

            <!-- Form Keputusan Keuangan -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-5 space-y-4">
                <div class="flex items-center space-x-2 border-b border-slate-100 pb-3">
                    <i class="fa-solid fa-clipboard-check text-emerald-600"></i>
                    <h3 class="text-xs font-black uppercase tracking-wider text-slate-800">Tindakan Verifikasi Keuangan</h3>
                </div>

                <form action="{{ route('keuangan.verify.process', $submission) }}" method="POST" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Pilih Keputusan <span class="text-red-500">*</span></label>
                        <div class="space-y-2">
                            <label class="flex items-center p-3 rounded-xl border border-slate-200 hover:border-emerald-500 cursor-pointer transition has-[:checked]:border-emerald-600 has-[:checked]:bg-emerald-50/50">
                                <input type="radio" name="action" value="APPROVE" checked class="text-emerald-600 focus:ring-emerald-500">
                                <div class="ml-3">
                                    <span class="text-xs font-bold text-slate-800 block">Setujui Verifikasi Keuangan</span>
                                    <span class="text-[11px] text-slate-500 block">RAB valid, teruskan ke Warek II untuk penetapan dana final universitas.</span>
                                </div>
                            </label>

                            <label class="flex items-center p-3 rounded-xl border border-slate-200 hover:border-amber-500 cursor-pointer transition has-[:checked]:border-amber-600 has-[:checked]:bg-amber-50/50">
                                <input type="radio" name="action" value="REVISE" class="text-amber-600 focus:ring-amber-500">
                                <div class="ml-3">
                                    <span class="text-xs font-bold text-slate-800 block">Minta Perbaikan Dokumen Anggaran</span>
                                    <span class="text-[11px] text-slate-500 block">Kembalikan ke mahasiswa untuk perbaikan rincian / nota RAB.</span>
                                </div>
                            </label>

                            <label class="flex items-center p-3 rounded-xl border border-slate-200 hover:border-red-500 cursor-pointer transition has-[:checked]:border-red-600 has-[:checked]:bg-red-50/50">
                                <input type="radio" name="action" value="REJECT" class="text-red-600 focus:ring-red-500">
                                <div class="ml-3">
                                    <span class="text-xs font-bold text-slate-800 block">Tolak Pengajuan Anggaran</span>
                                    <span class="text-[11px] text-slate-500 block">Batalkan pengajuan jika tidak sesuai regulasi keuangan.</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Catatan / Telaah Keuangan</label>
                        <textarea name="notes" rows="3" placeholder="Masukkan pertimbangan, kepatuhan pagu, atau instruksi revisi..."
                            class="w-full text-xs border border-slate-300 rounded-xl p-3 focus:ring-2 focus:ring-blue-600"></textarea>
                    </div>

                    <button type="submit" class="w-full py-2.5 rounded-xl text-xs font-bold bg-blue-700 hover:bg-blue-800 text-white shadow-sm hover:shadow transition flex items-center justify-center space-x-2">
                        <i class="fa-solid fa-floppy-disk"></i>
                        <span>Simpan Keputusan Keuangan</span>
                    </button>
                </form>
            </div>

        </div>

    </div>

</div>
@endsection

