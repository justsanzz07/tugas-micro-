@extends('layouts.app')

@section('title', 'Verifikasi & Penunjukan Dosen Pendamping — SIPRES UNUJA')

@section('content')
<div class="space-y-6">

    <!-- 1. BREADCRUMB & HEADER JUDUL HALAMAN -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <div class="flex items-center space-x-2 text-xs text-[#8492A6] mb-1.5">
                <a href="{{ route('kaprodi.dashboard') }}" class="hover:text-[#2454C5] font-medium transition flex items-center gap-1.5">
                    <i class="fa-solid fa-house text-slate-400"></i>
                    <span>Dashboard</span>
                </a>
                <span class="text-slate-300">/</span>
                <span class="text-[#172B4D] font-semibold">Verifikasi & Penunjukan Dosen</span>
            </div>
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-xl bg-[#EAF2FF] border border-blue-200/60 flex items-center justify-center text-[#2454C5] shadow-xs shrink-0">
                    <i class="fa-solid fa-user-check text-lg"></i>
                </div>
                <div>
                    <h1 class="text-xl sm:text-2xl font-black text-[#172B4D] tracking-tight">Verifikasi & Penunjukan Dosen Pendamping</h1>
                    <p class="text-xs text-[#8492A6] mt-0.5">Kelola pengajuan proposal delegasi lomba mahasiswa: verifikasi, penetapan dosen pendamping, dan riwayat pengesahan.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. KARTU PROFIL KAPRODI RINGKAS (HORIZONTAL LAYOUT) -->
    <div class="bg-white rounded-2xl border border-[#E3EAF3] shadow-xs p-4 sm:p-5 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div class="flex items-center space-x-3.5">
            <div class="w-12 h-12 rounded-xl bg-[#2454C5] text-white flex items-center justify-center text-lg font-bold shadow-2xs shrink-0">
                <i class="fa-solid fa-user-tie"></i>
            </div>
            <div>
                <h2 class="text-base font-extrabold text-[#172B4D] leading-snug">{{ $kaprodi->name }}</h2>
                <div class="flex flex-wrap items-center gap-x-2.5 gap-y-1 text-xs text-[#8492A6] mt-0.5">
                    <span class="flex items-center gap-1 font-medium text-slate-700">
                        <i class="fa-solid fa-graduation-cap text-[#2454C5]"></i>
                        <span>Kaprodi {{ $kaprodi->profile?->study_program_name ?? 'Informatika' }}</span>
                    </span>
                    <span class="text-slate-300">•</span>
                    <span class="flex items-center gap-1">
                        <i class="fa-solid fa-building-columns text-slate-400"></i>
                        <span>{{ $kaprodi->profile?->faculty_name ?? 'FTI' }}</span>
                    </span>
                    <span class="text-slate-300">•</span>
                    <span class="font-medium text-slate-600">NIDN: {{ $kaprodi->identifier ?? '0720048302' }}</span>
                </div>
            </div>
        </div>

        <div class="text-left md:text-right border-t md:border-t-0 pt-3 md:pt-0 border-slate-100">
            <span class="text-[10px] tracking-wider uppercase font-bold text-[#8492A6] block">WEWENANG KAPRODI</span>
            <span class="font-bold text-[#172B4D] text-xs">Verifikasi & Penunjukan Dosen Pendamping</span>
        </div>
    </div>

    <!-- 3. DASHBOARD STATISTIK (4 KARTU BERDASARKAN DATABASE) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Kartu 1: Total Pengajuan -->
        <div class="bg-white rounded-2xl border border-[#E3EAF3] shadow-xs p-4 flex items-center space-x-3.5 transition hover:shadow-sm">
            <div class="w-11 h-11 rounded-xl bg-[#EAF2FF] border border-blue-200/60 flex items-center justify-center text-[#2454C5] shrink-0">
                <i class="fa-solid fa-file-lines text-base"></i>
            </div>
            <div>
                <span class="text-xs font-semibold text-[#8492A6] block">Total Pengajuan</span>
                <span class="text-2xl font-black text-[#172B4D] leading-tight block">{{ $totalSubmissions }}</span>
                <span class="text-[11px] text-[#8492A6] block">Seluruh proposal delegasi</span>
            </div>
        </div>

        <!-- Kartu 2: Menunggu Verifikasi -->
        <div class="bg-white rounded-2xl border border-[#E3EAF3] shadow-xs p-4 flex items-center space-x-3.5 transition hover:shadow-sm">
            <div class="w-11 h-11 rounded-xl bg-[#FFFBEB] border border-amber-200/70 flex items-center justify-center text-amber-500 shrink-0">
                <i class="fa-regular fa-clock text-base"></i>
            </div>
            <div>
                <span class="text-xs font-semibold text-[#8492A6] block">Menunggu Verifikasi</span>
                <span class="text-2xl font-black text-[#172B4D] leading-tight block">{{ $totalPending }}</span>
                <span class="text-[11px] text-[#8492A6] block">Perlu ditinjau & ditetapkan</span>
            </div>
        </div>

        <!-- Kartu 3: Sudah Ditunjuk Dosen -->
        <div class="bg-white rounded-2xl border border-[#E3EAF3] shadow-xs p-4 flex items-center space-x-3.5 transition hover:shadow-sm">
            <div class="w-11 h-11 rounded-xl bg-[#EAF2FF] border border-blue-200/60 flex items-center justify-center text-[#2454C5] shrink-0">
                <i class="fa-solid fa-users text-base"></i>
            </div>
            <div>
                <span class="text-xs font-semibold text-[#8492A6] block">Sudah Ditunjuk Dosen</span>
                <span class="text-2xl font-black text-[#172B4D] leading-tight block">{{ $totalSupervisorAssigned }}</span>
                <span class="text-[11px] text-[#8492A6] block">Memiliki dosen pendamping</span>
            </div>
        </div>

        <!-- Kartu 4: Selesai Review -->
        <div class="bg-white rounded-2xl border border-[#E3EAF3] shadow-xs p-4 flex items-center space-x-3.5 transition hover:shadow-sm">
            <div class="w-11 h-11 rounded-xl bg-[#F0FDF4] border border-emerald-200/70 flex items-center justify-center text-emerald-600 shrink-0">
                <i class="fa-regular fa-circle-check text-base"></i>
            </div>
            <div>
                <span class="text-xs font-semibold text-[#8492A6] block">Selesai Review</span>
                <span class="text-2xl font-black text-[#172B4D] leading-tight block">{{ $totalCompletedReview }}</span>
                <span class="text-[11px] text-[#8492A6] block">Proses verifikasi selesai</span>
            </div>
        </div>
    </div>

    <!-- 4. CARD UTAMA: ANTREAN VERIFIKASI & SISTEM FILTER -->
    <div class="bg-white rounded-2xl border border-[#E3EAF3] shadow-xs overflow-hidden" id="section-antrean">

        <!-- Tab Header Antrean vs Riwayat -->
        <div class="px-5 sm:px-6 pt-4 border-b border-[#E3EAF3] flex items-center space-x-6 text-xs overflow-x-auto">
            <a href="#section-antrean"
                class="pb-3.5 font-bold border-b-2 border-[#2454C5] text-[#2454C5] flex items-center space-x-2 transition whitespace-nowrap">
                <i class="fa-solid fa-list-ul"></i>
                <span>Antrean Verifikasi ({{ $totalPending }})</span>
            </a>
            <a href="#section-riwayat"
                class="pb-3.5 font-semibold text-[#8492A6] hover:text-[#172B4D] border-b-2 border-transparent flex items-center space-x-2 transition whitespace-nowrap">
                <i class="fa-regular fa-clock"></i>
                <span>Riwayat Pengesahan ({{ $totalCompletedReview }})</span>
            </a>
        </div>

        <!-- Toolbar Pencarian & Filter Dropdown -->
        <div class="p-5 sm:p-6 space-y-4">
            <form action="{{ route('kaprodi.dashboard') }}" method="GET" class="space-y-3.5">
                <div class="flex flex-col lg:flex-row items-stretch lg:items-center gap-3">
                    <!-- Kolom Pencarian -->
                    <div class="relative flex-1">
                        <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Cari kode, nama ketua, atau nama lomba..."
                            class="w-full h-10 pl-9 pr-3.5 border border-[#E3EAF3] rounded-xl text-xs outline-none focus:ring-2 focus:ring-[#2454C5]/20 focus:border-[#2454C5] text-[#172B4D] placeholder:text-slate-400 transition">
                    </div>

                    <!-- Dropdown Filter: Tingkat Lomba -->
                    <div class="w-full lg:w-48">
                        <select name="level" onchange="this.form.submit()"
                            class="w-full h-10 px-3.5 border border-[#E3EAF3] rounded-xl text-xs outline-none focus:ring-2 focus:ring-[#2454C5]/20 focus:border-[#2454C5] text-[#172B4D] bg-white transition cursor-pointer">
                            <option value="">Semua Tingkat</option>
                            <option value="REGIONAL" {{ request('level') === 'REGIONAL' ? 'selected' : '' }}>Regional</option>
                            <option value="NASIONAL" {{ request('level') === 'NASIONAL' ? 'selected' : '' }}>Nasional</option>
                            <option value="INTERNASIONAL" {{ request('level') === 'INTERNASIONAL' ? 'selected' : '' }}>Internasional</option>
                        </select>
                    </div>

                    <!-- Dropdown Filter: Status Dosen -->
                    <div class="w-full lg:w-48">
                        <select name="supervisor_status" onchange="this.form.submit()"
                            class="w-full h-10 px-3.5 border border-[#E3EAF3] rounded-xl text-xs outline-none focus:ring-2 focus:ring-[#2454C5]/20 focus:border-[#2454C5] text-[#172B4D] bg-white transition cursor-pointer">
                            <option value="">Semua Status</option>
                            <option value="belum" {{ request('supervisor_status') === 'belum' ? 'selected' : '' }}>Belum Ditunjuk</option>
                            <option value="sudah" {{ request('supervisor_status') === 'sudah' ? 'selected' : '' }}>Sudah Ditunjuk</option>
                        </select>
                    </div>

                    <!-- Tombol Reset Filter -->
                    <a href="{{ route('kaprodi.dashboard') }}"
                        class="h-10 px-4 rounded-xl border border-[#E3EAF3] bg-white hover:bg-slate-50 text-[#172B4D] font-semibold text-xs flex items-center justify-center gap-1.5 transition shadow-2xs whitespace-nowrap">
                        <i class="fa-solid fa-arrows-rotate text-[11px] text-slate-500"></i>
                        <span>Reset Filter</span>
                    </a>
                </div>

                <!-- Baris Filter Cepat (Quick Filter Pills) -->
                <div class="flex flex-wrap items-center gap-2 pt-1 text-xs">
                    <span class="text-[#8492A6] font-semibold mr-1 text-[11px]">Filter Cepat:</span>

                    <!-- Semua -->
                    <a href="{{ route('kaprodi.dashboard') }}"
                        class="px-3 py-1 rounded-full text-xs font-semibold transition border {{ !request('quick') && !request('level') && !request('supervisor_status') ? 'bg-[#2454C5] text-white border-[#2454C5] shadow-xs' : 'bg-slate-50 text-slate-600 border-[#E3EAF3] hover:bg-slate-100' }}">
                        Semua ({{ $countAll }})
                    </a>

                    <!-- Nasional -->
                    <a href="{{ route('kaprodi.dashboard', ['quick' => 'nasional']) }}"
                        class="px-3 py-1 rounded-full text-xs font-semibold transition border {{ request('quick') === 'nasional' ? 'bg-[#2454C5] text-white border-[#2454C5] shadow-xs' : 'bg-slate-50 text-slate-600 border-[#E3EAF3] hover:bg-slate-100' }}">
                        Nasional ({{ $countNasional }})
                    </a>

                    <!-- Internasional -->
                    <a href="{{ route('kaprodi.dashboard', ['quick' => 'internasional']) }}"
                        class="px-3 py-1 rounded-full text-xs font-semibold transition border {{ request('quick') === 'internasional' ? 'bg-[#2454C5] text-white border-[#2454C5] shadow-xs' : 'bg-slate-50 text-slate-600 border-[#E3EAF3] hover:bg-slate-100' }}">
                        Internasional ({{ $countInternasional }})
                    </a>

                    <!-- Menunggu Dosen -->
                    <a href="{{ route('kaprodi.dashboard', ['quick' => 'menunggu_dosen']) }}"
                        class="px-3 py-1 rounded-full text-xs font-semibold transition border {{ request('quick') === 'menunggu_dosen' ? 'bg-[#2454C5] text-white border-[#2454C5] shadow-xs' : 'bg-slate-50 text-slate-600 border-[#E3EAF3] hover:bg-slate-100' }}">
                        Menunggu Dosen ({{ $countMenungguDosen }})
                    </a>

                    <!-- Sudah Ditunjuk -->
                    <a href="{{ route('kaprodi.dashboard', ['quick' => 'sudah_ditunjuk']) }}"
                        class="px-3 py-1 rounded-full text-xs font-semibold transition border {{ request('quick') === 'sudah_ditunjuk' ? 'bg-[#2454C5] text-white border-[#2454C5] shadow-xs' : 'bg-slate-50 text-slate-600 border-[#E3EAF3] hover:bg-slate-100' }}">
                        Sudah Ditunjuk ({{ $countSudahDitunjuk }})
                    </a>
                </div>
            </form>
        </div>

        <!-- Tabel Antrean Verifikasi -->
        @if($pendingSubmissions->isEmpty())
            <div class="p-12 text-center text-slate-400 text-xs border-t border-[#E3EAF3]">
                <div class="w-12 h-12 rounded-full bg-blue-50 text-[#2454C5] flex items-center justify-center mx-auto mb-3 text-xl">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
                <p class="font-bold text-[#172B4D] text-sm">Tidak ada antrean pengajuan yang ditemukan</p>
                <p class="text-[11px] text-[#8492A6] mt-0.5">Semua proposal telah selesai diverifikasi atau tidak cocok dengan filter pencarian Anda.</p>
                @if(request()->hasAny(['search', 'level', 'supervisor_status', 'quick']))
                    <a href="{{ route('kaprodi.dashboard') }}" class="inline-block mt-3 text-[#2454C5] font-bold hover:underline">
                        Bersihkan Filter Pencarian
                    </a>
                @endif
            </div>
        @else
            <div class="overflow-x-auto border-t border-[#E3EAF3]">
                <table class="w-full text-left text-xs min-w-[700px]">
                    <thead>
                        <tr class="bg-slate-50/70 text-[#8492A6] uppercase text-[10px] font-bold tracking-wider border-b border-[#E3EAF3]">
                            <th class="py-3 px-4">Kode & Tanggal</th>
                            <th class="py-3 px-4">Ketua Tim</th>
                            <th class="py-3 px-4">Nama Lomba & Tingkat</th>
                            <th class="py-3 px-4">Status Dosen Pendamping</th>
                            <th class="py-3 px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#E3EAF3]/70">
                        @foreach($pendingSubmissions as $sub)
                            <tr class="hover:bg-slate-50/70 transition">
                                <!-- Kode & Tanggal -->
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <span class="font-bold text-[#172B4D] block">{{ $sub->code }}</span>
                                    <span class="text-[11px] text-[#8492A6] block">{{ $sub->created_at->format('d M Y') }}</span>
                                </td>

                                <!-- Ketua Tim -->
                                <td class="py-3.5 px-4">
                                    <span class="font-bold text-[#172B4D] block">{{ $sub->submitter_name ?? $sub->mahasiswa_nim }}</span>
                                    <span class="text-[11px] text-[#8492A6] flex items-center gap-1 mt-0.5">
                                        <i class="fa-solid fa-user text-slate-400 text-[10px]"></i>
                                        <span>NIM {{ $sub->submitter_nim }}</span>
                                    </span>
                                </td>

                                <!-- Nama Lomba & Tingkat -->
                                <td class="py-3.5 px-4">
                                    <span class="font-bold text-[#172B4D] block truncate max-w-xs" title="{{ $sub->competitionDetail?->competition_name ?? $sub->title }}">
                                        {{ $sub->competitionDetail?->competition_name ?? $sub->title }}
                                    </span>
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-[#EAF2FF] text-[#2454C5] border border-blue-200/60 mt-1">
                                        <i class="fa-solid fa-trophy text-[9px]"></i>
                                        <span>{{ $sub->competitionDetail?->level?->label() ?? 'Nasional' }}</span>
                                    </span>
                                </td>

                                <!-- Status Dosen Pendamping -->
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    @if($sub->supervisor_name)
                                        <div class="flex items-start gap-1.5">
                                            <i class="fa-solid fa-user-check text-[#2454C5] text-xs mt-0.5"></i>
                                            <div>
                                                <span class="font-bold text-[#172B4D] block">{{ $sub->supervisor_name }}</span>
                                                <span class="text-[10px] text-[#8492A6] block">NIDN {{ $sub->supervisor_nidn ?? '-' }}</span>
                                            </div>
                                        </div>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-semibold bg-[#FFFBEB] text-[#B45309] border border-amber-200/80 shadow-2xs">
                                            <i class="fa-regular fa-clock text-amber-500 text-xs"></i>
                                            <span>Belum Ditunjuk (Wajib Ditentukan)</span>
                                        </span>
                                    @endif
                                </td>

                                <!-- Aksi -->
                                <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                    <a href="{{ route('kaprodi.show', $sub->id) }}"
                                        class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-[#2454C5] hover:bg-[#173B8F] text-white font-bold text-xs shadow-2xs hover:shadow transition">
                                        <i class="fa-solid fa-clipboard-check text-xs"></i>
                                        <span>Tinjau & Tetapkan Dosen</span>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination Antrean Verifikasi (Default 10 per halaman) -->
            <div class="px-5 sm:px-6 py-3.5 border-t border-[#E3EAF3] flex flex-col sm:flex-row items-center justify-between gap-3 text-xs">
                <div class="text-[#8492A6]">
                    Menampilkan <span class="font-bold text-[#172B4D]">{{ $pendingSubmissions->firstItem() ?? 0 }}–{{ $pendingSubmissions->lastItem() ?? 0 }}</span> dari <span class="font-bold text-[#172B4D]">{{ $pendingSubmissions->total() }}</span> pengajuan
                </div>

                @if($pendingSubmissions->hasPages())
                    <div class="flex items-center space-x-1.5">
                        {{-- Previous Button --}}
                        @if ($pendingSubmissions->onFirstPage())
                            <span class="w-8 h-8 rounded-lg border border-[#E3EAF3] bg-slate-50 text-slate-300 flex items-center justify-center cursor-not-allowed">
                                <i class="fa-solid fa-chevron-left text-[10px]"></i>
                            </span>
                        @else
                            <a href="{{ $pendingSubmissions->previousPageUrl() }}" class="w-8 h-8 rounded-lg border border-[#E3EAF3] bg-white text-[#172B4D] hover:bg-slate-50 flex items-center justify-center transition shadow-2xs">
                                <i class="fa-solid fa-chevron-left text-[10px]"></i>
                            </a>
                        @endif

                        {{-- Page Numbers --}}
                        @for ($i = 1; $i <= $pendingSubmissions->lastPage(); $i++)
                            @if ($i == $pendingSubmissions->currentPage())
                                <span class="w-8 h-8 rounded-lg bg-[#2454C5] text-white font-bold flex items-center justify-center shadow-xs">
                                    {{ $i }}
                                </span>
                            @else
                                <a href="{{ $pendingSubmissions->url($i) }}" class="w-8 h-8 rounded-lg border border-[#E3EAF3] bg-white text-[#172B4D] font-semibold hover:bg-slate-50 flex items-center justify-center transition shadow-2xs">
                                    {{ $i }}
                                </a>
                            @endif
                        @endfor

                        {{-- Next Button --}}
                        @if ($pendingSubmissions->hasMorePages())
                            <a href="{{ $pendingSubmissions->nextPageUrl() }}" class="w-8 h-8 rounded-lg border border-[#E3EAF3] bg-white text-[#172B4D] hover:bg-slate-50 flex items-center justify-center transition shadow-2xs">
                                <i class="fa-solid fa-chevron-right text-[10px]"></i>
                            </a>
                        @else
                            <span class="w-8 h-8 rounded-lg border border-[#E3EAF3] bg-slate-50 text-slate-300 flex items-center justify-center cursor-not-allowed">
                                <i class="fa-solid fa-chevron-right text-[10px]"></i>
                            </span>
                        @endif
                    </div>
                @endif
            </div>
        @endif

    </div>

    <!-- 5. CARD TERPISAH: RIWAYAT PENGESAHAN KAPRODI -->
    <div class="bg-white rounded-2xl border border-[#E3EAF3] shadow-xs overflow-hidden" id="section-riwayat">
        <div class="p-5 sm:p-6 border-b border-[#E3EAF3] flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="flex items-center space-x-3">
                <div class="w-8 h-8 rounded-lg bg-[#EAF2FF] text-[#2454C5] flex items-center justify-center text-sm shrink-0">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-[#172B4D]">Riwayat Pengesahan Kaprodi</h2>
                    <p class="text-xs text-[#8492A6]">Daftar pengajuan yang telah diverifikasi dan disahkan oleh Kaprodi.</p>
                </div>
            </div>
            <span class="text-xs font-semibold text-[#8492A6]">
                Total: <strong class="text-[#172B4D]">{{ $historySubmissions->total() }}</strong> Riwayat
            </span>
        </div>

        @if($historySubmissions->isEmpty())
            <div class="p-10 text-center text-slate-400 text-xs">
                Belum ada riwayat pengesahan yang selesai diproses oleh Kaprodi.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs min-w-[700px]">
                    <thead>
                        <tr class="bg-slate-50/70 text-[#8492A6] uppercase text-[10px] font-bold tracking-wider border-b border-[#E3EAF3]">
                            <th class="py-3 px-4">Kode</th>
                            <th class="py-3 px-4">Nama Lomba</th>
                            <th class="py-3 px-4">Ketua Tim</th>
                            <th class="py-3 px-4">Dosen Pendamping</th>
                            <th class="py-3 px-4">Status Review</th>
                            <th class="py-3 px-4 text-right">Detail</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#E3EAF3]/70">
                        @foreach($historySubmissions as $sub)
                            <tr class="hover:bg-slate-50/70 transition">
                                <!-- Kode -->
                                <td class="py-3 px-4 font-bold text-[#172B4D] whitespace-nowrap">
                                    {{ $sub->code }}
                                </td>

                                <!-- Nama Lomba -->
                                <td class="py-3 px-4 font-semibold text-[#172B4D]">
                                    <span class="block truncate max-w-xs" title="{{ $sub->competitionDetail?->competition_name ?? $sub->title }}">
                                        {{ $sub->competitionDetail?->competition_name ?? $sub->title }}
                                    </span>
                                </td>

                                <!-- Ketua Tim -->
                                <td class="py-3 px-4 text-slate-700">
                                    {{ $sub->submitter_name ?? $sub->mahasiswa_nim }}
                                </td>

                                <!-- Dosen Pendamping -->
                                <td class="py-3 px-4 text-[#172B4D]">
                                    @if($sub->supervisor_name)
                                        <span class="font-semibold block">{{ $sub->supervisor_name }}</span>
                                        <span class="text-[10px] text-[#8492A6] block">NIDN {{ $sub->supervisor_nidn ?? '-' }}</span>
                                    @else
                                        <span class="text-slate-400">-</span>
                                    @endif
                                </td>

                                <!-- Status Review -->
                                <td class="py-3 px-4 whitespace-nowrap">
                                    @php
                                        $statusStr = strtolower($sub->status ?? '');
                                    @endphp
                                    @if(in_array($statusStr, ['approved', 'approved_final', 'completed', 'waiting_lpj', 'review_lpj', 'dekan_review', 'kemahasiswaan_review', 'warek_review']))
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-[#DCFCE7] text-[#16A34A] border border-emerald-200">
                                            <i class="fa-solid fa-check text-[10px]"></i>
                                            <span>Disahkan</span>
                                        </span>
                                    @elseif(str_contains($statusStr, 'reject'))
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                            <i class="fa-solid fa-xmark text-[10px]"></i>
                                            <span>Ditolak</span>
                                        </span>
                                    @elseif(str_contains($statusStr, 'revision'))
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-50 text-amber-800 border border-amber-200">
                                            <i class="fa-solid fa-rotate-left text-[10px]"></i>
                                            <span>Revisi</span>
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-blue-50 text-[#2454C5] border border-blue-200">
                                            <i class="fa-regular fa-clock text-[10px]"></i>
                                            <span>Review Kaprodi</span>
                                        </span>
                                    @endif
                                </td>

                                <!-- Detail -->
                                <td class="py-3 px-4 text-right whitespace-nowrap">
                                    <a href="{{ route('kaprodi.show', $sub->id) }}"
                                        class="inline-flex items-center gap-1 px-3 py-1 rounded-lg border border-[#E3EAF3] bg-white hover:bg-slate-50 text-[#172B4D] font-semibold text-xs transition shadow-2xs">
                                        <i class="fa-regular fa-eye text-slate-500"></i>
                                        <span>Lihat</span>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination Riwayat Pengesahan (Default 10 per halaman) -->
            <div class="px-5 sm:px-6 py-3.5 border-t border-[#E3EAF3] flex flex-col sm:flex-row items-center justify-between gap-3 text-xs">
                <div class="text-[#8492A6]">
                    Menampilkan <span class="font-bold text-[#172B4D]">{{ $historySubmissions->firstItem() ?? 0 }}–{{ $historySubmissions->lastItem() ?? 0 }}</span> dari <span class="font-bold text-[#172B4D]">{{ $historySubmissions->total() }}</span> riwayat
                </div>

                @if($historySubmissions->hasPages())
                    <div class="flex items-center space-x-1.5">
                        {{-- Previous Button --}}
                        @if ($historySubmissions->onFirstPage())
                            <span class="w-8 h-8 rounded-lg border border-[#E3EAF3] bg-slate-50 text-slate-300 flex items-center justify-center cursor-not-allowed">
                                <i class="fa-solid fa-chevron-left text-[10px]"></i>
                            </span>
                        @else
                            <a href="{{ $historySubmissions->previousPageUrl() }}" class="w-8 h-8 rounded-lg border border-[#E3EAF3] bg-white text-[#172B4D] hover:bg-slate-50 flex items-center justify-center transition shadow-2xs">
                                <i class="fa-solid fa-chevron-left text-[10px]"></i>
                            </a>
                        @endif

                        {{-- Page Numbers --}}
                        @for ($i = 1; $i <= $historySubmissions->lastPage(); $i++)
                            @if ($i == $historySubmissions->currentPage())
                                <span class="w-8 h-8 rounded-lg bg-[#2454C5] text-white font-bold flex items-center justify-center shadow-xs">
                                    {{ $i }}
                                </span>
                            @else
                                <a href="{{ $historySubmissions->url($i) }}" class="w-8 h-8 rounded-lg border border-[#E3EAF3] bg-white text-[#172B4D] font-semibold hover:bg-slate-50 flex items-center justify-center transition shadow-2xs">
                                    {{ $i }}
                                </a>
                            @endif
                        @endfor

                        {{-- Next Button --}}
                        @if ($historySubmissions->hasMorePages())
                            <a href="{{ $historySubmissions->nextPageUrl() }}" class="w-8 h-8 rounded-lg border border-[#E3EAF3] bg-white text-[#172B4D] hover:bg-slate-50 flex items-center justify-center transition shadow-2xs">
                                <i class="fa-solid fa-chevron-right text-[10px]"></i>
                            </a>
                        @else
                            <span class="w-8 h-8 rounded-lg border border-[#E3EAF3] bg-slate-50 text-slate-300 flex items-center justify-center cursor-not-allowed">
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
