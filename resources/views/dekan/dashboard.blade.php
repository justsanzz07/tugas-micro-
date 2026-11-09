@extends('layouts.app')

@section('title', 'Dashboard Dekan — SIPRES UNUJA')

@section('content')
<div class="space-y-6">

    <!-- 1. KARTU PROFIL DEKAN (HORIZONTAL LAYOUT ELEGAN DENGAN AKSEN UNGU) -->
    <div class="bg-white rounded-2xl border border-[#E5EAF2] shadow-xs p-5 sm:p-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4 relative overflow-hidden">
        <!-- Dekorasi Gradasi Halus di Pojok Kanan Atas -->
        <div class="absolute -right-16 -top-16 w-56 h-56 rounded-full bg-purple-50/70 pointer-events-none"></div>

        <div class="flex items-center space-x-4 relative z-10">
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-[#7E22CE] to-[#581C87] text-white flex items-center justify-center text-xl font-bold shadow-md shadow-purple-500/20 shrink-0">
                <i class="fa-solid fa-award"></i>
            </div>
            <div>
                <div class="flex flex-wrap items-center gap-2">
                    <h1 class="text-xl font-extrabold text-[#172B4D] tracking-tight">{{ $dekan->name }}</h1>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-[#F3E8FF] text-[#7E22CE] border border-purple-200">
                        Dekan Fakultas ({{ $fakultasId }})
                    </span>
                </div>
                <div class="flex flex-wrap items-center gap-x-2.5 gap-y-1 text-xs text-[#8492A6] mt-1">
                    <span class="flex items-center gap-1 font-medium text-slate-700">
                        <i class="fa-solid fa-building-columns text-slate-400"></i>
                        <span>{{ $dekan->profile?->faculty_name ?? 'Fakultas Teknologi Informasi' }}</span>
                    </span>
                    <span class="text-slate-300">•</span>
                    <span class="font-medium text-slate-600">NIDN: {{ $dekan->identifier ?? '0715017803' }}</span>
                </div>
            </div>
        </div>

        <div class="flex items-center relative z-10">
            <a href="{{ route('dekan.reports') }}"
                class="inline-flex items-center space-x-2 bg-gradient-to-r from-[#7E22CE] to-[#6B21A8] hover:from-[#6B21A8] hover:to-[#581C87] text-white font-bold px-4 py-2.5 rounded-xl text-xs transition shadow-sm hover:shadow">
                <i class="fa-solid fa-file-invoice text-sm"></i>
                <span>Lihat Laporan Prestasi Fakultas</span>
                <i class="fa-solid fa-chevron-right text-[10px]"></i>
            </a>
        </div>
    </div>

    <!-- 2. DASHBOARD STATISTIK (4 KARTU HORIZONTAL DENGAN DATA AKTUAL DATABASE) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Kartu 1: Antrean Proposal -->
        <div class="bg-white rounded-2xl border border-[#E5EAF2] shadow-xs p-4 sm:p-5 flex items-center justify-between transition hover:shadow-sm">
            <div>
                <span class="text-xs font-semibold text-[#8492A6] block">Antrean Proposal</span>
                <span class="text-3xl font-black text-[#172B4D] mt-1 block leading-tight">{{ $totalPendingProposal }}</span>
                <span class="text-[11px] text-[#8492A6] mt-0.5 block">Menunggu persetujuan final</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-[#F3E8FF] text-[#7E22CE] flex items-center justify-center text-base shrink-0">
                <i class="fa-solid fa-upload"></i>
            </div>
        </div>

        <!-- Kartu 2: Antrean LPJ & SKPI -->
        <div class="bg-white rounded-2xl border border-[#E5EAF2] shadow-xs p-4 sm:p-5 flex items-center justify-between transition hover:shadow-sm">
            <div>
                <span class="text-xs font-semibold text-[#8492A6] block">Antrean LPJ & SKPI</span>
                <span class="text-3xl font-black text-[#172B4D] mt-1 block leading-tight">{{ $totalLpj }}</span>
                <span class="text-[11px] text-[#8492A6] mt-0.5 block">Perlu pengesahan Dekan</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-[#FFF7ED] text-[#EA580C] flex items-center justify-center text-base shrink-0">
                <i class="fa-solid fa-file-lines"></i>
            </div>
        </div>

        <!-- Kartu 3: Lomba Aktif -->
        <div class="bg-white rounded-2xl border border-[#E5EAF2] shadow-xs p-4 sm:p-5 flex items-center justify-between transition hover:shadow-sm">
            <div>
                <span class="text-xs font-semibold text-[#8492A6] block">Lomba Aktif</span>
                <span class="text-3xl font-black text-[#172B4D] mt-1 block leading-tight">{{ $totalActive }}</span>
                <span class="text-[11px] text-[#8492A6] mt-0.5 block">Partisipasi mahasiswa fakultas</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-[#EAF2FF] text-[#2454C5] flex items-center justify-center text-base shrink-0">
                <i class="fa-solid fa-trophy"></i>
            </div>
        </div>

        <!-- Kartu 4: Prestasi Selesai -->
        <div class="bg-white rounded-2xl border border-[#E5EAF2] shadow-xs p-4 sm:p-5 flex items-center justify-between transition hover:shadow-sm">
            <div>
                <span class="text-xs font-semibold text-[#8492A6] block">Prestasi Selesai</span>
                <span class="text-3xl font-black text-[#172B4D] mt-1 block leading-tight">{{ $totalCompleted }}</span>
                <span class="text-[11px] text-[#8492A6] mt-0.5 block">LPJ & SKPI tervalidasi</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-[#F3E8FF] text-[#7E22CE] flex items-center justify-center text-base shrink-0">
                <i class="fa-solid fa-award"></i>
            </div>
        </div>
    </div>

    <!-- 3. CARD 1: ANTREAN PERSETUJUAN FINAL FAKULTAS & PENETAPAN DANA -->
    <div class="bg-white rounded-2xl border border-[#E5EAF2] shadow-xs overflow-hidden">
        <!-- Header Card -->
        <div class="p-5 sm:p-6 border-b border-[#E5EAF2] flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="flex items-center">
                <div class="w-1 h-8 bg-[#7E22CE] rounded-full mr-3 shrink-0"></div>
                <div class="w-8 h-8 rounded-lg bg-[#F3E8FF] text-[#7E22CE] flex items-center justify-center text-sm mr-2.5 shrink-0">
                    <i class="fa-solid fa-layer-group"></i>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-[#172B4D]">Antrean Persetujuan Final Fakultas & Penetapan Dana</h2>
                    <p class="text-xs text-[#8492A6]">Proposal yang telah diverifikasi Kaprodi dan menunggu pengesahan Surat Tugas & dana oleh Dekan.</p>
                </div>
            </div>
            <div>
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-[#F3E8FF] text-[#7E22CE] border border-purple-200">
                    <i class="fa-regular fa-clock text-[11px]"></i>
                    <span>{{ $pendingSubmissions->count() }} Pengajuan Perlu Validasi</span>
                </span>
            </div>
        </div>

        @if($pendingSubmissions->isEmpty())
            <div class="p-10 text-center text-slate-400 text-xs">
                <div class="w-12 h-12 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center mx-auto mb-3 text-xl">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
                <p class="font-bold text-[#172B4D] text-sm">Tidak ada antrean persetujuan proposal</p>
                <p class="text-[11px] text-[#8492A6] mt-0.5">Semua proposal mahasiswa fakultas yang diajukan telah selesai diproses oleh Dekan.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs min-w-[760px]">
                    <thead>
                        <tr class="bg-slate-50/70 text-[#8492A6] uppercase font-bold text-[10px] tracking-wider border-b border-[#E5EAF2]">
                            <th class="py-3 px-4">Kode & Tanggal</th>
                            <th class="py-3 px-4">Ketua & Anggota Tim</th>
                            <th class="py-3 px-4">Nama Lomba & Tingkat</th>
                            <th class="py-3 px-4">Dosen Pendamping</th>
                            <th class="py-3 px-4">Usulan Dana</th>
                            <th class="py-3 px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#E5EAF2]/70">
                        @foreach($pendingSubmissions as $sub)
                            <tr class="hover:bg-slate-50/70 transition">
                                <!-- Kode & Tanggal -->
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <span class="font-bold text-[#172B4D] block">{{ $sub->code }}</span>
                                    <span class="text-[11px] text-[#8492A6] block">{{ $sub->created_at->format('d M Y') }}</span>
                                </td>

                                <!-- Ketua & Anggota Tim -->
                                <td class="py-3.5 px-4">
                                    <span class="font-bold text-[#172B4D] block">{{ $sub->submitter_name ?? $sub->submitter_nim }}</span>
                                    <span class="text-[11px] text-[#8492A6] block">NIM: {{ $sub->submitter_nim }}</span>
                                </td>

                                <!-- Nama Lomba & Tingkat -->
                                <td class="py-3.5 px-4">
                                    <span class="font-bold text-[#172B4D] block truncate max-w-xs" title="{{ $sub->competitionDetail?->competition_name ?? $sub->title }}">
                                        {{ $sub->competitionDetail?->competition_name ?? $sub->title }}
                                    </span>
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-[#EAF2FF] text-[#2454C5] border border-blue-200/60 mt-1">
                                        {{ strtoupper($sub->competitionDetail?->level?->label() ?? 'NASIONAL') }}
                                    </span>
                                </td>

                                <!-- Dosen Pendamping -->
                                <td class="py-3.5 px-4">
                                    <span class="font-semibold text-[#172B4D] block">{{ $sub->supervisor_name ?? '-' }}</span>
                                    <span class="text-[10px] text-[#8492A6] block">NIDN: {{ $sub->supervisor_nidn ?? '-' }}</span>
                                </td>

                                <!-- Usulan Dana -->
                                <td class="py-3.5 px-4 whitespace-nowrap font-bold text-[#172B4D]">
                                    Rp {{ number_format($sub->total_budget_requested ?? 0, 0, ',', '.') }}
                                </td>

                                <!-- Aksi -->
                                <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                    <a href="{{ route('dekan.show', $sub->id) }}"
                                        class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-[#7E22CE] hover:bg-[#6B21A8] text-white font-bold text-xs shadow-2xs hover:shadow transition">
                                        <i class="fa-solid fa-user-check text-xs"></i>
                                        <span>Proses & Sahkan Final</span>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <!-- 4. CARD 2: PARTISIPASI & CAPAIAN PRESTASI MAHASISWA FAKULTAS (DENGAN TAB, SEARCH, FILTER & PAGINATION 10 DATA) -->
    <div class="bg-white rounded-2xl border border-[#E5EAF2] shadow-xs overflow-hidden">
        <!-- Header Card -->
        <div class="p-5 sm:p-6 border-b border-[#E5EAF2] flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="flex items-center">
                <div class="w-1 h-8 bg-[#7E22CE] rounded-full mr-3 shrink-0"></div>
                <div class="w-8 h-8 rounded-lg bg-[#F3E8FF] text-[#7E22CE] flex items-center justify-center text-sm mr-2.5 shrink-0">
                    <i class="fa-solid fa-trophy"></i>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-[#172B4D]">Partisipasi & Capaian Prestasi Mahasiswa Fakultas</h2>
                    <p class="text-xs text-[#8492A6]">Daftar seluruh delegasi lomba mahasiswa di tingkat fakultas.</p>
                </div>
            </div>
            <div>
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-[#F3E8FF] text-[#7E22CE] border border-purple-200">
                    <i class="fa-solid fa-chart-simple text-[11px]"></i>
                    <span>Total: {{ $allFacultySubmissions->total() }} Pengajuan</span>
                </span>
            </div>
        </div>

        <!-- Toolbar: Tabs Filter Cepat & Search/Dropdown Status -->
        <div class="p-5 sm:p-6 pb-4">
            <form action="{{ route('dekan.dashboard') }}" method="GET" class="flex flex-col lg:flex-row items-stretch lg:items-center justify-between gap-3">
                <!-- Sisi Kiri: Tab Filter Cepat Status -->
                <div class="flex items-center space-x-1.5 overflow-x-auto text-xs pb-1 lg:pb-0">
                    <!-- Semua -->
                    <a href="{{ route('dekan.dashboard', array_merge(request()->except('tab', 'page'), ['tab' => 'all'])) }}"
                        class="px-3.5 py-1.5 rounded-xl font-bold transition flex items-center gap-1.5 whitespace-nowrap {{ ($activeTab === 'all' || !$activeTab) ? 'bg-[#7E22CE] text-white shadow-xs' : 'bg-slate-50 text-slate-600 border border-[#E5EAF2] hover:bg-slate-100' }}">
                        <i class="fa-solid fa-list-ul text-[10px]"></i>
                        <span>Semua ({{ $countAll }})</span>
                    </a>

                    <!-- Proses -->
                    <a href="{{ route('dekan.dashboard', array_merge(request()->except('tab', 'page'), ['tab' => 'proses'])) }}"
                        class="px-3.5 py-1.5 rounded-xl font-semibold transition flex items-center gap-1.5 whitespace-nowrap {{ $activeTab === 'proses' ? 'bg-[#7E22CE] text-white font-bold shadow-xs' : 'bg-slate-50 text-slate-600 border border-[#E5EAF2] hover:bg-slate-100' }}">
                        <i class="fa-regular fa-clock text-[10px]"></i>
                        <span>Proses ({{ $countProses }})</span>
                    </a>

                    <!-- Disetujui -->
                    <a href="{{ route('dekan.dashboard', array_merge(request()->except('tab', 'page'), ['tab' => 'disetujui'])) }}"
                        class="px-3.5 py-1.5 rounded-xl font-semibold transition flex items-center gap-1.5 whitespace-nowrap {{ $activeTab === 'disetujui' ? 'bg-[#7E22CE] text-white font-bold shadow-xs' : 'bg-slate-50 text-slate-600 border border-[#E5EAF2] hover:bg-slate-100' }}">
                        <i class="fa-regular fa-circle-check text-[10px]"></i>
                        <span>Disetujui ({{ $countDisetujui }})</span>
                    </a>

                    <!-- Selesai -->
                    <a href="{{ route('dekan.dashboard', array_merge(request()->except('tab', 'page'), ['tab' => 'selesai'])) }}"
                        class="px-3.5 py-1.5 rounded-xl font-semibold transition flex items-center gap-1.5 whitespace-nowrap {{ $activeTab === 'selesai' ? 'bg-[#7E22CE] text-white font-bold shadow-xs' : 'bg-slate-50 text-slate-600 border border-[#E5EAF2] hover:bg-slate-100' }}">
                        <i class="fa-solid fa-trophy text-[10px]"></i>
                        <span>Selesai ({{ $countSelesai }})</span>
                    </a>
                </div>

                <!-- Sisi Kanan: Input Search & Dropdown Filter Status -->
                <div class="flex items-center gap-2.5">
                    @if(request('tab') && request('tab') !== 'all')
                        <input type="hidden" name="tab" value="{{ request('tab') }}">
                    @endif

                    <!-- Input Pencarian -->
                    <div class="relative flex-1 sm:w-64">
                        <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Cari kode, nama lomba, atau ketua tim..."
                            class="w-full h-9 pl-9 pr-3 border border-[#E5EAF2] rounded-xl text-xs outline-none focus:ring-2 focus:ring-[#7E22CE]/20 focus:border-[#7E22CE] text-[#172B4D] placeholder:text-slate-400 transition">
                    </div>

                    <!-- Dropdown Filter Status -->
                    <div class="w-40">
                        <select name="status" onchange="this.form.submit()"
                            class="w-full h-9 px-3 border border-[#E5EAF2] rounded-xl text-xs outline-none focus:ring-2 focus:ring-[#7E22CE]/20 focus:border-[#7E22CE] text-[#172B4D] bg-white transition cursor-pointer">
                            <option value="">Semua Status</option>
                            <option value="review_kaprodi" {{ request('status') === 'review_kaprodi' ? 'selected' : '' }}>Review Kaprodi</option>
                            <option value="review_dekan" {{ request('status') === 'review_dekan' ? 'selected' : '' }}>Review Dekan</option>
                            <option value="disetujui" {{ request('status') === 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                            <option value="selesai" {{ request('status') === 'selesai' ? 'selected' : '' }}>Selesai</option>
                        </select>
                    </div>

                    @if(request()->hasAny(['search', 'status', 'tab']))
                        <a href="{{ route('dekan.dashboard') }}"
                            class="h-9 w-9 rounded-xl border border-[#E5EAF2] bg-white hover:bg-slate-50 text-slate-500 flex items-center justify-center transition shadow-2xs shrink-0"
                            title="Reset Filter">
                            <i class="fa-solid fa-rotate-left text-xs"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Tabel Partisipasi Mahasiswa -->
        @if($allFacultySubmissions->isEmpty())
            <div class="p-12 text-center text-slate-400 text-xs border-t border-[#E5EAF2]">
                <div class="w-12 h-12 rounded-full bg-purple-50 text-[#7E22CE] flex items-center justify-center mx-auto mb-3 text-xl">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </div>
                <p class="font-bold text-[#172B4D] text-sm">Tidak ada data pengajuan yang sesuai</p>
                <p class="text-[11px] text-[#8492A6] mt-0.5">Silakan sesuaikan kriteria pencarian atau pilih tab status lain.</p>
                <a href="{{ route('dekan.dashboard') }}" class="inline-block mt-3 text-[#7E22CE] font-bold hover:underline">
                    Lihat Semua Pengajuan
                </a>
            </div>
        @else
            <div class="overflow-x-auto border-t border-[#E5EAF2]">
                <table class="w-full text-left text-xs min-w-[650px]">
                    <thead>
                        <tr class="bg-slate-50/70 text-[#8492A6] uppercase font-bold text-[10px] tracking-wider border-b border-[#E5EAF2]">
                            <th class="py-3 px-4">Kode</th>
                            <th class="py-3 px-4">Nama Lomba</th>
                            <th class="py-3 px-4">Ketua Tim</th>
                            <th class="py-3 px-4">Status Pengajuan</th>
                            <th class="py-3 px-4 text-right">Detail</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#E5EAF2]/70">
                        @foreach($allFacultySubmissions as $sub)
                            <tr class="hover:bg-slate-50/70 transition">
                                <!-- Kode -->
                                <td class="py-3.5 px-4 font-bold text-[#172B4D] whitespace-nowrap">
                                    {{ $sub->code }}
                                </td>

                                <!-- Nama Lomba -->
                                <td class="py-3.5 px-4 font-semibold text-[#172B4D]">
                                    <span class="block truncate max-w-sm" title="{{ $sub->competitionDetail?->competition_name ?? $sub->title }}">
                                        {{ $sub->competitionDetail?->competition_name ?? $sub->title }}
                                    </span>
                                </td>

                                <!-- Ketua Tim -->
                                <td class="py-3.5 px-4 text-slate-700">
                                    {{ $sub->submitter_name ?? $sub->submitter_nim }}
                                </td>

                                <!-- Status Pengajuan -->
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    @php
                                        $s = strtolower($sub->status ?? '');
                                    @endphp
                                    @if(in_array($s, ['dekan_review', 'approved_kaprodi', 'approved_dosen', 'review_dekan']))
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-[#F3E8FF] text-[#7E22CE] border border-purple-200">
                                            Review Dekan
                                        </span>
                                    @elseif(in_array($s, ['submitted', 'kaprodi_review', 'dosen_review', 'review_kaprodi', 'review_dosen']))
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-[#EAF2FF] text-[#2454C5] border border-blue-200">
                                            Review Kaprodi
                                        </span>
                                    @elseif(in_array($s, ['approved', 'approved_final', 'waiting_lpj', 'review_lpj', 'lpj_review']))
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-[#DCFCE7] text-[#16A34A] border border-emerald-200">
                                            Disetujui
                                        </span>
                                    @elseif($s === 'completed')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-[#DCFCE7] text-[#16A34A] border border-emerald-200">
                                            Selesai
                                        </span>
                                    @elseif(str_contains($s, 'revision'))
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-50 text-amber-800 border border-amber-200">
                                            Perlu Revisi
                                        </span>
                                    @elseif(str_contains($s, 'reject'))
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                            Ditolak
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-slate-100 text-slate-700">
                                            {{ $sub->current_status?->label() ?? 'Proses' }}
                                        </span>
                                    @endif
                                </td>

                                <!-- Detail -->
                                <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                    <a href="{{ route('dekan.show', $sub->id) }}"
                                        class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg border border-[#E5EAF2] bg-white hover:bg-slate-50 text-[#172B4D] font-semibold text-xs transition shadow-2xs">
                                        <i class="fa-regular fa-eye text-slate-400"></i>
                                        <span>Lihat</span>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination Bar (Default 10 per halaman) -->
            <div class="px-5 sm:px-6 py-3.5 border-t border-[#E5EAF2] flex flex-col sm:flex-row items-center justify-between gap-3 text-xs">
                <div class="text-[#8492A6]">
                    Menampilkan <span class="font-bold text-[#172B4D]">{{ $allFacultySubmissions->firstItem() ?? 0 }}–{{ $allFacultySubmissions->lastItem() ?? 0 }}</span> dari <span class="font-bold text-[#172B4D]">{{ $allFacultySubmissions->total() }}</span> hasil
                </div>

                @if($allFacultySubmissions->hasPages())
                    <div class="flex items-center space-x-1.5">
                        {{-- Previous Button --}}
                        @if ($allFacultySubmissions->onFirstPage())
                            <span class="w-8 h-8 rounded-lg border border-[#E5EAF2] bg-slate-50 text-slate-300 flex items-center justify-center cursor-not-allowed">
                                <i class="fa-solid fa-chevron-left text-[10px]"></i>
                            </span>
                        @else
                            <a href="{{ $allFacultySubmissions->previousPageUrl() }}" class="w-8 h-8 rounded-lg border border-[#E5EAF2] bg-white text-[#172B4D] hover:bg-slate-50 flex items-center justify-center transition shadow-2xs">
                                <i class="fa-solid fa-chevron-left text-[10px]"></i>
                            </a>
                        @endif

                        {{-- Page Numbers --}}
                        @for ($i = 1; $i <= $allFacultySubmissions->lastPage(); $i++)
                            @if ($i == $allFacultySubmissions->currentPage())
                                <span class="w-8 h-8 rounded-lg bg-[#7E22CE] text-white font-bold flex items-center justify-center shadow-xs">
                                    {{ $i }}
                                </span>
                            @else
                                <a href="{{ $allFacultySubmissions->url($i) }}" class="w-8 h-8 rounded-lg border border-[#E5EAF2] bg-white text-[#172B4D] font-semibold hover:bg-slate-50 flex items-center justify-center transition shadow-2xs">
                                    {{ $i }}
                                </a>
                            @endif
                        @endfor

                        {{-- Next Button --}}
                        @if ($allFacultySubmissions->hasMorePages())
                            <a href="{{ $allFacultySubmissions->nextPageUrl() }}" class="w-8 h-8 rounded-lg border border-[#E5EAF2] bg-white text-[#172B4D] hover:bg-slate-50 flex items-center justify-center transition shadow-2xs">
                                <i class="fa-solid fa-chevron-right text-[10px]"></i>
                            </a>
                        @else
                            <span class="w-8 h-8 rounded-lg border border-[#E5EAF2] bg-slate-50 text-slate-300 flex items-center justify-center cursor-not-allowed">
                                <i class="fa-solid fa-chevron-right text-[10px]"></i>
                            </span>
                        @endif
                    </div>
                @endif
            </div>
        @endif
    </div>

</div>
@endsection
