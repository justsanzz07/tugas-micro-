@extends('layouts.admin')

@section('title', 'Detail Pengajuan [' . $submission->code . '] — Admin SIPRES')
@section('breadcrumb', 'Detail Pengajuan')
@section('header_title', 'Detail Pengajuan: ' . $submission->code)

@section('content')
<div class="space-y-6">

    <!-- Top Action & Info -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <div class="flex items-center space-x-2 text-xs text-slate-400 mb-1">
                <a href="{{ route('admin.submissions.index') }}" class="hover:text-[#2454C5]">Pengajuan Lomba</a>
                <i class="fa-solid fa-chevron-right text-[10px] text-slate-300"></i>
                <span class="text-slate-600 font-bold">{{ $submission->code }}</span>
            </div>
            <h1 class="text-xl font-extrabold text-[#172554] tracking-tight">Detail Pengajuan &amp; Riwayat Alur Persetujuan</h1>
        </div>
        <div class="flex items-center space-x-2 self-start sm:self-auto">
            <a href="{{ route('admin.submissions.index') }}"
                class="inline-flex items-center space-x-1.5 px-3.5 py-2 rounded-xl text-xs font-semibold text-slate-600 bg-white border border-slate-200 hover:bg-slate-50 shadow-2xs transition">
                <i class="fa-solid fa-arrow-left"></i>
                <span>Kembali ke Daftar</span>
            </a>
        </div>
    </div>

    <!-- Read-Only Observer Banner -->
    <div class="p-4 rounded-2xl bg-blue-50/80 border border-blue-200 flex items-center space-x-3 text-xs text-blue-900">
        <div class="w-8 h-8 rounded-xl bg-[#2454C5] text-white flex items-center justify-center text-sm shrink-0">
            <i class="fa-solid fa-eye"></i>
        </div>
        <div>
            <span class="font-extrabold block">Mode Pemantauan Administrator (Read-Only)</span>
            <span class="text-blue-700">Administrator memiliki wewenang pengawasan seluruh pengajuan namun tidak dapat memodifikasi atau mengambil keputusan approval atas nama pejabat.</span>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Kolom Kiri: Detail Lomba, Anggota, & RAB -->
        <div class="lg:col-span-2 space-y-6">

            <!-- Informasi Kompetisi -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-2xs p-5 space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-xl bg-blue-50 text-[#2454C5] flex items-center justify-center text-lg font-bold">
                            <i class="fa-solid fa-trophy text-amber-500"></i>
                        </div>
                        <div>
                            <h2 class="text-base font-extrabold text-slate-800">{{ $submission->competitionDetail?->name ?? $submission->title }}</h2>
                            <span class="text-xs text-slate-400">Kode Pengajuan: <strong>{{ $submission->code }}</strong></span>
                        </div>
                    </div>
                    <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-blue-50 text-[#2454C5] border border-blue-200">
                        {{ $submission->competitionDetail?->level ?? 'Nasional' }}
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
                        <span class="text-slate-400 block font-medium">Dosen Pendamping</span>
                        <span class="font-bold text-slate-800 block mt-0.5">{{ $submission->supervisor_name ?? 'Belum Ditunjuk' }}</span>
                        <span class="text-slate-500 text-[11px]">NIDN: {{ $submission->supervisor_nidn ?? '-' }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block font-medium">Berkas Proposal</span>
                        @if($submission->proposal_path)
                            <a href="{{ asset('storage/' . $submission->proposal_path) }}" target="_blank"
                                class="inline-flex items-center space-x-1.5 text-[#2454C5] font-bold hover:underline mt-1">
                                <i class="fa-solid fa-file-pdf"></i>
                                <span>Unduh / Buka Proposal Kegiatan</span>
                            </a>
                        @else
                            <span class="text-slate-400 italic">Tidak ada berkas</span>
                        @endif
                    </div>
                </div>

                <!-- Anggota Tim -->
                @if($submission->members && $submission->members->count() > 0)
                    <div class="border-t border-slate-100 pt-3">
                        <span class="text-xs font-bold text-slate-700 block mb-2">Anggota Tim Delegasi ({{ $submission->members->count() }} Mahasiswa):</span>
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

            <!-- Rincian Anggaran (RAB) -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-2xs overflow-hidden">
                <div class="p-4 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="text-xs font-extrabold uppercase tracking-wider text-slate-800 flex items-center space-x-2">
                        <i class="fa-solid fa-list-check text-[#2454C5]"></i>
                        <span>Rincian Anggaran Biaya (RAB)</span>
                    </h3>
                    <div class="text-xs font-semibold">
                        <span>Plafon Disetujui: </span>
                        <span class="font-extrabold text-emerald-700">Rp {{ number_format($submission->total_budget_approved ?: 0, 0, ',', '.') }}</span>
                    </div>
                </div>

                @if($budget && $budget->items && $budget->items->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr class="bg-slate-50 text-slate-500 font-bold uppercase text-[10px]">
                                    <th class="py-2.5 px-4">No</th>
                                    <th class="py-2.5 px-4">Item Kebutuhan</th>
                                    <th class="py-2.5 px-4">Kategori</th>
                                    <th class="py-2.5 px-4 text-center">Volume</th>
                                    <th class="py-2.5 px-4 text-right">Harga Satuan</th>
                                    <th class="py-2.5 px-4 text-right">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($budget->items as $idx => $item)
                                    <tr>
                                        <td class="py-2.5 px-4 text-slate-400">{{ $idx + 1 }}</td>
                                        <td class="py-2.5 px-4 font-bold text-slate-800">{{ $item->item_name }}</td>
                                        <td class="py-2.5 px-4">
                                            <span class="px-2 py-0.5 rounded text-[10px] bg-slate-100 text-slate-700 font-medium">
                                                {{ $item->category ?? 'Operasional' }}
                                            </span>
                                        </td>
                                        <td class="py-2.5 px-4 text-center">{{ $item->quantity }} {{ $item->unit ?? 'Pcs' }}</td>
                                        <td class="py-2.5 px-4 text-right">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                                        <td class="py-2.5 px-4 text-right font-black text-slate-800">Rp {{ number_format($item->total_price, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="p-6 text-center text-xs text-slate-400">
                        <p>Total Usulan Pengajuan: <strong>Rp {{ number_format($submission->total_budget_requested, 0, ',', '.') }}</strong></p>
                    </div>
                @endif
            </div>

        </div>

        <!-- Kolom Kanan: Status & Riwayat Alur Persetujuan -->
        <div class="space-y-6">

            <!-- Card Status Saat Ini -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-2xs p-5 space-y-3">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400 block">Status Terkini</span>
                <div class="flex items-center space-x-2">
                    <span class="px-3 py-1.5 rounded-xl text-xs font-bold border {{ $submission->current_status->badgeColor() }}">
                        {{ $submission->current_status->label() }}
                    </span>
                </div>
                <div class="text-xs text-slate-500 pt-2 border-t border-slate-100 space-y-1">
                    <div>Diajukan pada: <strong>{{ $submission->created_at ? $submission->created_at->format('d M Y, H:i') : '-' }}</strong></div>
                    <div>Pembaruan: <strong>{{ $submission->updated_at ? $submission->updated_at->format('d M Y, H:i') : '-' }}</strong></div>
                </div>
            </div>

            <!-- Tahapan Alur Persetujuan (Workflow Timeline) -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-2xs p-5 space-y-4">
                <h3 class="text-xs font-extrabold uppercase tracking-wider text-slate-800 flex items-center space-x-2">
                    <i class="fa-solid fa-timeline text-[#2454C5]"></i>
                    <span>Riwayat Alur Persetujuan 4 Tahap</span>
                </h3>

                <div class="relative pl-6 space-y-5 before:absolute before:left-2 before:top-2 before:bottom-2 before:w-0.5 before:bg-slate-200">
                    
                    <!-- Tahap 1: Kaprodi -->
                    <div class="relative">
                        <div class="absolute -left-6 top-0.5 w-4 h-4 rounded-full bg-blue-600 border-2 border-white"></div>
                        <span class="text-xs font-bold text-slate-800 block">1. Kaprodi</span>
                        <p class="text-[11px] text-slate-500">Penunjukan Dosen Pendamping &amp; Verifikasi Awal</p>
                    </div>

                    <!-- Tahap 2: Dekan -->
                    <div class="relative">
                        <div class="absolute -left-6 top-0.5 w-4 h-4 rounded-full bg-indigo-600 border-2 border-white"></div>
                        <span class="text-xs font-bold text-slate-800 block">2. Dekan Fakultas</span>
                        <p class="text-[11px] text-slate-500">Persetujuan Fakultas &amp; Rekomendasi Plafon Dana</p>
                    </div>

                    <!-- Tahap 3: Keuangan -->
                    <div class="relative">
                        <div class="absolute -left-6 top-0.5 w-4 h-4 rounded-full bg-emerald-600 border-2 border-white"></div>
                        <span class="text-xs font-bold text-slate-800 block">3. Biro Keuangan</span>
                        <p class="text-[11px] text-slate-500">Telaah Administrasi Anggaran &amp; Kelayakan RAB</p>
                    </div>

                    <!-- Tahap 4: Warek II -->
                    <div class="relative">
                        <div class="absolute -left-6 top-0.5 w-4 h-4 rounded-full bg-purple-600 border-2 border-white"></div>
                        <span class="text-xs font-bold text-slate-800 block">4. Wakil Rektor II</span>
                        <p class="text-[11px] text-slate-500">Persetujuan Final &amp; Penetapan Plafon Universitas</p>
                    </div>

                </div>
            </div>

        </div>

    </div>

</div>
@endsection
