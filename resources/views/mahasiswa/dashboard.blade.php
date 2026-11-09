@extends('layouts.app')

@section('title', 'Dashboard Mahasiswa — SIPRES')

@section('content')
<div class="space-y-6">

    <!-- 1. KARTU PROFIL AKUN MAHASISWA -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-5 sm:p-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-5">
            
            <!-- Identitas Diri Mahasiswa -->
            <div class="flex items-start sm:items-center space-x-4">
                <!-- Avatar Inisial -->
                <div class="w-16 h-16 rounded-2xl bg-gradient-to-tr from-blue-800 via-blue-700 to-indigo-700 text-white font-black text-2xl flex items-center justify-center shadow-lg shadow-blue-700/20 shrink-0">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>

                <!-- Detail Profil Pribadi -->
                <div>
                    <div class="flex flex-wrap items-center gap-2">
                        <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">{{ $user->name }}</h1>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-blue-500 mr-1.5 animate-pulse"></span>
                            Mahasiswa Aktif
                        </span>
                    </div>

                    <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-slate-500 mt-1.5">
                        <span class="font-medium">
                            <i class="fa-solid fa-id-card text-blue-500 mr-1"></i>
                            NIM: <strong class="text-slate-800 font-bold">{{ $user->identifier }}</strong>
                        </span>
                        <span class="hidden sm:inline text-slate-300">•</span>
                        <span>
                            <i class="fa-solid fa-graduation-cap text-blue-500 mr-1"></i>
                            {{ $user->profile?->study_program_name ?? 'Informatika' }}
                        </span>
                        <span class="hidden sm:inline text-slate-300">•</span>
                        <span>
                            <i class="fa-solid fa-building-columns text-blue-500 mr-1"></i>
                            {{ $user->profile?->faculty_name ?? 'Fakultas Teknologi Informasi' }}
                        </span>
                        <span class="hidden sm:inline text-slate-300">•</span>
                        <span>
                            <i class="fa-solid fa-calendar-check text-blue-500 mr-1"></i>
                            Semester {{ $user->profile?->semester ?? 5 }} ({{ $user->profile?->academic_year ?? '2025/2026' }})
                        </span>
                    </div>

                    <!-- Indikator Status Akun LPJ -->
                    <div class="mt-2.5 flex items-center gap-2">
                        @if(! $blockedSubmission)
                            <span class="inline-flex items-center space-x-1.5 text-xs text-blue-800 bg-blue-50 px-3 py-1 rounded-lg border border-blue-200/80 font-semibold">
                                <i class="fa-solid fa-circle-check text-blue-600"></i>
                                <span>Status Akun: Bebas Tunggakan LPJ (Siap Mengajukan Lomba)</span>
                            </span>
                        @else
                            <span class="inline-flex items-center space-x-1.5 text-xs text-amber-800 bg-amber-50 px-3 py-1 rounded-lg border border-amber-300 font-bold">
                                <i class="fa-solid fa-triangle-exclamation text-amber-600"></i>
                                <span>Status Akun: Ada Tunggakan LPJ (Pengajuan Baru Ditangguhkan)</span>
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Tombol Tindakan Cepat (Ajukan Lomba Baru) -->
            <div class="shrink-0 flex items-center">
                @if(! $blockedSubmission)
                    <a href="{{ route('mahasiswa.submissions.create') }}" class="w-full sm:w-auto inline-flex items-center justify-center space-x-2 bg-blue-600 hover:bg-blue-700 text-white font-bold px-5 py-3 rounded-xl text-xs transition shadow-md shadow-blue-600/20">
                        <i class="fa-solid fa-plus-circle text-sm"></i>
                        <span>Ajukan Delegasi Lomba Baru</span>
                    </a>
                @else
                    <button disabled class="w-full sm:w-auto inline-flex items-center justify-center space-x-2 bg-slate-200 text-slate-500 font-bold px-5 py-3 rounded-xl text-xs cursor-not-allowed border border-slate-300" title="Selesaikan tunggakan LPJ terlebih dahulu">
                        <i class="fa-solid fa-ban text-sm"></i>
                        <span>Pengajuan Baru Terblokir</span>
                    </button>
                @endif
            </div>

        </div>
    </div>

    <!-- 2. PERINGATAN TUNGGAKAN LPJ (Hanya tampil jika ada tunggakan) -->
    @if($blockedSubmission)
        <div class="bg-amber-50 border-2 border-amber-400 p-5 rounded-2xl shadow-sm flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div class="flex items-start space-x-3.5">
                <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-800 flex items-center justify-center font-bold text-lg shrink-0 mt-0.5">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-amber-950">TUNGGAKAN LPJ: {{ $blockedSubmission->code }} — {{ $blockedSubmission->competitionDetail?->competition_name }}</h3>
                    <p class="text-xs text-amber-900 mt-1 leading-relaxed">
                        Anda telah menyelesaikan perlombaan ini namun belum mengunggah Laporan Pertanggungjawaban (LPJ). Segera unggah dokumen LPJ dan bukti kuitansi pengeluaran agar akun Anda dapat kembali mengajukan lomba baru.
                    </p>
                </div>
            </div>
            <a href="{{ route('mahasiswa.submissions.lpj', $blockedSubmission->id) }}" class="shrink-0 inline-flex items-center space-x-1.5 bg-amber-700 hover:bg-amber-800 text-white px-4 py-2.5 rounded-xl text-xs font-bold transition shadow">
                <i class="fa-solid fa-file-invoice"></i>
                <span>Unggah LPJ Sekarang</span>
            </a>
        </div>
    @endif

    <!-- 3. RINGKASAN METRIK PRIBADI (4 KARTU BERSIH & FOKUS) -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        
        <!-- Total Lomba Diajukan -->
        <a href="{{ route('mahasiswa.dashboard', ['tab' => 'all']) }}" class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/80 shadow-sm hover:border-blue-300 transition block">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-500">Total Pengajuan</span>
                <div class="w-8 h-8 rounded-lg bg-slate-100 text-slate-600 flex items-center justify-center text-xs">
                    <i class="fa-solid fa-folder-open"></i>
                </div>
            </div>
            <span class="text-2xl sm:text-3xl font-black text-slate-800 mt-2 block">{{ $totalAll }}</span>
            <span class="text-[11px] text-slate-400 mt-0.5 block">Semua riwayat lomba saya</span>
        </a>

        <!-- Sedang Dalam Proses -->
        <a href="{{ route('mahasiswa.dashboard', ['tab' => 'active']) }}" class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/80 shadow-sm hover:border-blue-400 transition block">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-500">Dalam Proses</span>
                <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center text-xs">
                    <i class="fa-solid fa-spinner fa-spin-pulse"></i>
                </div>
            </div>
            <span class="text-2xl sm:text-3xl font-black text-blue-600 mt-2 block">{{ $totalActive }}</span>
            <span class="text-[11px] text-blue-500 mt-0.5 block">Review Kaprodi / Dekan</span>
        </a>

        <!-- Perlu Perbaikan / Revisi -->
        <a href="{{ route('mahasiswa.dashboard', ['tab' => 'revision']) }}" class="bg-white p-4 sm:p-5 rounded-2xl border {{ $totalRevision > 0 ? 'border-amber-300 bg-amber-50/20' : 'border-slate-200/80' }} shadow-sm hover:border-amber-400 transition block">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold {{ $totalRevision > 0 ? 'text-amber-800 font-bold' : 'text-slate-500' }}">Perlu Revisi</span>
                <div class="w-8 h-8 rounded-lg {{ $totalRevision > 0 ? 'bg-amber-100 text-amber-700 font-bold' : 'bg-slate-100 text-slate-400' }} flex items-center justify-center text-xs">
                    <i class="fa-solid fa-pen-ruler"></i>
                </div>
            </div>
            <span class="text-2xl sm:text-3xl font-black {{ $totalRevision > 0 ? 'text-amber-600' : 'text-slate-800' }} mt-2 block">{{ $totalRevision }}</span>
            <span class="text-[11px] {{ $totalRevision > 0 ? 'text-amber-700 font-medium' : 'text-slate-400' }} mt-0.5 block">
                {{ $totalRevision > 0 ? 'Butuh perbaikan mahasiswa' : 'Tidak ada revisi aktif' }}
            </span>
        </a>

        <!-- Selesai & Poin SKPI -->
        <a href="{{ route('mahasiswa.dashboard', ['tab' => 'completed']) }}" class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/80 shadow-sm hover:border-blue-400 transition block">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-500">Selesai & SKPI</span>
                <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-700 flex items-center justify-center text-xs">
                    <i class="fa-solid fa-award"></i>
                </div>
            </div>
            <div class="flex items-baseline space-x-2 mt-2">
                <span class="text-2xl sm:text-3xl font-black text-blue-700">{{ $totalCompleted }}</span>
                <span class="text-xs font-bold text-blue-700 bg-blue-50 px-2 py-0.5 rounded-md">
                    +{{ number_format($totalSkpiPoints, 1) }} Poin
                </span>
            </div>
            <span class="text-[11px] text-blue-600 mt-0.5 block">Prestasi diakui universitas</span>
        </a>

    </div>

    <!-- 4. DAFTAR RIWAYAT PENGAJUAN LOMBA MAHASISWA -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        
        <!-- Header Tabel & Filter Bar -->
        <div class="p-5 border-b border-slate-100 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            
            <!-- Tab Filter Kategori -->
            <div class="flex items-center space-x-1 sm:space-x-2 overflow-x-auto pb-1 md:pb-0 text-xs font-semibold">
                <a href="{{ route('mahasiswa.dashboard', ['tab' => 'all', 'search' => request('search')]) }}" class="px-3.5 py-2 rounded-xl transition flex items-center space-x-1.5 {{ $activeTab === 'all' ? 'bg-slate-900 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    <span>Semua</span>
                    <span class="text-[10px] px-1.5 py-0.2 rounded-full {{ $activeTab === 'all' ? 'bg-white/20 text-white' : 'bg-slate-200 text-slate-700' }}">{{ $totalAll }}</span>
                </a>

                <a href="{{ route('mahasiswa.dashboard', ['tab' => 'active', 'search' => request('search')]) }}" class="px-3.5 py-2 rounded-xl transition flex items-center space-x-1.5 {{ $activeTab === 'active' ? 'bg-blue-600 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    <span>Dalam Proses</span>
                    <span class="text-[10px] px-1.5 py-0.2 rounded-full {{ $activeTab === 'active' ? 'bg-white/20 text-white' : 'bg-slate-200 text-slate-700' }}">{{ $totalActive }}</span>
                </a>

                <a href="{{ route('mahasiswa.dashboard', ['tab' => 'revision', 'search' => request('search')]) }}" class="px-3.5 py-2 rounded-xl transition flex items-center space-x-1.5 {{ $activeTab === 'revision' ? 'bg-amber-600 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    <span>Perlu Revisi</span>
                    @if($totalRevision > 0)
                        <span class="text-[10px] px-1.5 py-0.2 rounded-full {{ $activeTab === 'revision' ? 'bg-white/20 text-white' : 'bg-amber-200 text-amber-800 font-bold' }}">{{ $totalRevision }}</span>
                    @endif
                </a>

                <a href="{{ route('mahasiswa.dashboard', ['tab' => 'waiting_lpj', 'search' => request('search')]) }}" class="px-3.5 py-2 rounded-xl transition flex items-center space-x-1.5 {{ $activeTab === 'waiting_lpj' ? 'bg-indigo-600 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    <span>Menunggu LPJ</span>
                    <span class="text-[10px] px-1.5 py-0.2 rounded-full {{ $activeTab === 'waiting_lpj' ? 'bg-white/20 text-white' : 'bg-slate-200 text-slate-700' }}">{{ $totalWaitingLpj }}</span>
                </a>

                <a href="{{ route('mahasiswa.dashboard', ['tab' => 'completed', 'search' => request('search')]) }}" class="px-3.5 py-2 rounded-xl transition flex items-center space-x-1.5 {{ $activeTab === 'completed' ? 'bg-blue-800 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    <span>Selesai</span>
                    <span class="text-[10px] px-1.5 py-0.2 rounded-full {{ $activeTab === 'completed' ? 'bg-white/20 text-white' : 'bg-slate-200 text-slate-700' }}">{{ $totalCompleted }}</span>
                </a>
            </div>

            <!-- Form Pencarian Cepat -->
            <form action="{{ route('mahasiswa.dashboard') }}" method="GET" class="flex items-center space-x-2">
                <input type="hidden" name="tab" value="{{ $activeTab }}">
                <div class="relative w-full sm:w-64">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400 text-xs">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama lomba/penyelenggara..." class="w-full text-xs pl-8 pr-3 py-2 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                </div>
                @if(request('search'))
                    <a href="{{ route('mahasiswa.dashboard', ['tab' => $activeTab]) }}" class="px-2.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs transition" title="Reset Pencarian">
                        <i class="fa-solid fa-xmark"></i>
                    </a>
                @endif
            </form>

        </div>

        <!-- Tabel Riwayat Lomba -->
        @if($submissions->isEmpty())
            <div class="p-12 text-center">
                <div class="w-16 h-16 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto text-2xl mb-3">
                    <i class="fa-solid fa-file-circle-question"></i>
                </div>
                <h3 class="text-sm font-bold text-slate-800">
                    {{ request('search') ? 'Tidak Ditemukan Pengajuan yang Sesuai' : 'Belum Ada Pengajuan Lomba' }}
                </h3>
                <p class="text-xs text-slate-500 max-w-md mx-auto mt-1 mb-5 leading-relaxed">
                    {{ request('search') ? 'Silakan periksa kata kunci pencarian Anda atau reset filter tab di atas.' : 'Anda belum memiliki riwayat pengajuan delegasi lomba pada kategori ini. Klik tombol di bawah untuk membuat pengajuan baru.' }}
                </p>
                @if(! $blockedSubmission && ! request('search'))
                    <a href="{{ route('mahasiswa.submissions.create') }}" class="inline-flex items-center space-x-2 bg-blue-600 hover:bg-blue-700 text-white font-bold px-4 py-2.5 rounded-xl text-xs transition shadow-sm">
                        <i class="fa-solid fa-plus-circle"></i>
                        <span>Mulai Buat Pengajuan Lomba</span>
                    </a>
                @elseif(request('search'))
                    <a href="{{ route('mahasiswa.dashboard') }}" class="inline-flex items-center space-x-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold px-4 py-2 rounded-xl text-xs transition">
                        <i class="fa-solid fa-rotate-left"></i>
                        <span>Kembali ke Semua Pengajuan</span>
                    </a>
                @endif
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-slate-50 text-slate-500 uppercase font-bold text-[10px] tracking-wider border-b border-slate-200/80">
                            <th class="py-3 px-4">Kode & Tanggal</th>
                            <th class="py-3 px-4">Nama Lomba & Penyelenggara</th>
                            <th class="py-3 px-4">Dosen Pendamping</th>
                            <th class="py-3 px-4">Status Terkini</th>
                            <th class="py-3 px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($submissions as $sub)
                            @php
                                $statusEnum = $sub->current_status;
                                $statusVal = $statusEnum?->value ?? 'DRAFT';

                                $badgeConfig = match($statusVal) {
                                    'DRAFT' => [
                                        'bg' => 'bg-slate-100 text-slate-700 border-slate-200',
                                        'icon' => 'fa-file-lines',
                                        'text' => 'Draft Pengajuan'
                                    ],
                                    'SUBMITTED', 'REVIEW_DOSEN', 'REVIEW_KAPRODI' => [
                                        'bg' => 'bg-blue-50 text-blue-700 border-blue-200',
                                        'icon' => 'fa-hourglass-half',
                                        'text' => 'Menunggu Review Kaprodi'
                                    ],
                                    'REVISION_DOSEN', 'REVISION_KAPRODI' => [
                                        'bg' => 'bg-amber-50 text-amber-800 border-amber-300 font-bold',
                                        'icon' => 'fa-pen-to-square',
                                        'text' => 'Perlu Revisi (Kaprodi)'
                                    ],
                                    'APPROVED_DOSEN', 'APPROVED_KAPRODI', 'REVIEW_DEKAN' => [
                                        'bg' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                                        'icon' => 'fa-building-columns',
                                        'text' => 'Menunggu Persetujuan Dekan'
                                    ],
                                    'REVISION_DEKAN' => [
                                        'bg' => 'bg-amber-50 text-amber-800 border-amber-300 font-bold',
                                        'icon' => 'fa-pen-to-square',
                                        'text' => 'Perlu Revisi (Dekan)'
                                    ],
                                    'APPROVED_FINAL' => [
                                        'bg' => 'bg-blue-50 text-blue-800 border-blue-300 font-bold',
                                        'icon' => 'fa-check-circle',
                                        'text' => 'Disetujui Dekan'
                                    ],
                                    'WAITING_LPJ' => [
                                        'bg' => 'bg-orange-50 text-orange-800 border-orange-300 font-bold',
                                        'icon' => 'fa-file-invoice',
                                        'text' => 'Wajib Unggah LPJ'
                                    ],
                                    'REVIEW_LPJ' => [
                                        'bg' => 'bg-blue-50 text-blue-700 border-blue-200',
                                        'icon' => 'fa-clock-rotate-left',
                                        'text' => 'LPJ Sedang Diverifikasi'
                                    ],
                                    'COMPLETED' => [
                                        'bg' => 'bg-blue-100 text-blue-900 border-blue-300 font-extrabold',
                                        'icon' => 'fa-award',
                                        'text' => 'Selesai (Poin SKPI Sah)'
                                    ],
                                    'REJECTED' => [
                                        'bg' => 'bg-rose-50 text-rose-700 border-rose-200',
                                        'icon' => 'fa-circle-xmark',
                                        'text' => 'Ditolak'
                                    ],
                                    default => [
                                        'bg' => 'bg-slate-100 text-slate-700 border-slate-200',
                                        'icon' => 'fa-circle-info',
                                        'text' => $statusEnum?->label() ?? 'Dalam Proses'
                                    ],
                                };
                            @endphp
                            <tr class="hover:bg-slate-50/80 transition">
                                <!-- Kode & Tanggal -->
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <span class="font-bold text-slate-800 block">{{ $sub->code }}</span>
                                    <span class="text-[11px] text-slate-400 block">{{ $sub->created_at->format('d M Y') }}</span>
                                </td>

                                <!-- Nama Lomba & Penyelenggara -->
                                <td class="py-3.5 px-4">
                                    <div class="font-bold text-slate-800 leading-snug">
                                        {{ $sub->competitionDetail?->competition_name ?? $sub->title }}
                                    </div>
                                    <div class="flex flex-wrap items-center gap-1.5 text-[11px] text-slate-500 mt-1">
                                        <span><i class="fa-solid fa-sitemap text-slate-400 mr-0.5"></i>{{ $sub->competitionDetail?->organizer ?? '-' }}</span>
                                        <span class="text-slate-300">•</span>
                                        <span class="px-1.5 py-0.2 rounded text-[10px] font-bold bg-slate-100 text-slate-700 uppercase">
                                            {{ $sub->competitionDetail?->level?->label() ?? 'REGIONAL' }}
                                        </span>
                                        @if($sub->team_name)
                                            <span class="text-slate-300">•</span>
                                            <span class="text-slate-600"><i class="fa-solid fa-users text-slate-400 mr-0.5"></i>Tim: {{ $sub->team_name }}</span>
                                        @endif
                                    </div>
                                </td>

                                <!-- Dosen Pendamping -->
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    @if($sub->supervisor_name)
                                        <div class="font-semibold text-slate-700">{{ $sub->supervisor_name }}</div>
                                        <span class="text-[10px] text-slate-400 block">NIDN: {{ $sub->supervisor_nidn ?? '-' }}</span>
                                    @else
                                        <span class="text-slate-400 italic">Belum ditentukan Kaprodi</span>
                                    @endif
                                </td>

                                <!-- Status Terkini -->
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <span class="inline-flex items-center space-x-1.5 px-2.5 py-1 rounded-full text-[11px] border {{ $badgeConfig['bg'] }}">
                                        <i class="fa-solid {{ $badgeConfig['icon'] }} text-[10px]"></i>
                                        <span>{{ $badgeConfig['text'] }}</span>
                                    </span>
                                </td>

                                <!-- Tombol Aksi -->
                                <td class="py-3.5 px-4 text-right space-x-1.5 whitespace-nowrap">
                                    <!-- Detail & Tracking -->
                                    <a href="{{ route('mahasiswa.submissions.show', $sub->id) }}" class="inline-flex items-center space-x-1 px-3 py-1.5 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-700 text-xs font-semibold transition" title="Lihat Progress & Detail">
                                        <i class="fa-solid fa-eye"></i>
                                        <span>Detail</span>
                                    </a>

                                    <!-- Tombol Perbaiki jika Perlu Revisi -->
                                    @if($sub->isEditable())
                                        <a href="{{ route('mahasiswa.submissions.edit', $sub->id) }}" class="inline-flex items-center space-x-1 px-3 py-1.5 rounded-lg bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold transition shadow-sm" title="Perbaiki Dokumen/RAB">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                            <span>Perbaiki</span>
                                        </a>
                                    @endif

                                    <!-- Tombol Unggah LPJ jika Disetujui/Waiting LPJ -->
                                    @if(in_array($statusVal, ['WAITING_LPJ', 'APPROVED_FINAL']))
                                        <a href="{{ route('mahasiswa.submissions.lpj', $sub->id) }}" class="inline-flex items-center space-x-1 px-3 py-1.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold transition shadow-sm" title="Unggah Laporan LPJ & Prestasi">
                                            <i class="fa-solid fa-file-invoice"></i>
                                            <span>Unggah LPJ</span>
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-5 py-4 border-t border-slate-100 bg-slate-50/50">
                {{ $submissions->links() }}
            </div>
        @endif

    </div>

</div>
@endsection
