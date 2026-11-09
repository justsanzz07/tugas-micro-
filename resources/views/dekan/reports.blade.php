@extends('layouts.app')

@section('title', 'Rekapitulasi Prestasi Mahasiswa — Fakultas ' . $fakultasId)

@section('content')
<div class="space-y-6">

    <!-- 1. BREADCRUMB & HEADER JUDUL LAPORAN -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 print:hidden">
        <div>
            <div class="flex items-center space-x-2 text-xs text-[#8492A6] mb-1.5">
                <a href="{{ route('dekan.dashboard') }}" class="hover:text-[#7E22CE] font-medium transition flex items-center gap-1.5">
                    <i class="fa-solid fa-house text-slate-400"></i>
                    <span>Dashboard Dekan</span>
                </a>
                <span class="text-slate-300">/</span>
                <span class="text-[#172B4D] font-semibold">Rekapitulasi Prestasi</span>
            </div>
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-xl bg-[#F3E8FF] border border-purple-200 text-[#7E22CE] flex items-center justify-center text-lg shadow-xs shrink-0">
                    <i class="fa-solid fa-file-invoice"></i>
                </div>
                <div>
                    <h1 class="text-xl sm:text-2xl font-black text-[#172B4D] tracking-tight">Rekapitulasi Prestasi Mahasiswa ({{ $fakultasId }})</h1>
                    <p class="text-xs text-[#8492A6] mt-0.5">Laporan capaian lomba, partisipasi delegasi fakultas, perolehan poin SKPI, dan surat dispensasi resmi.</p>
                </div>
            </div>
        </div>

        <div class="flex items-center space-x-2.5">
            <a href="{{ route('dekan.dashboard') }}"
                class="px-4 py-2 rounded-xl border border-[#E5EAF2] bg-white hover:bg-slate-50 text-[#172B4D] font-semibold text-xs flex items-center gap-2 transition shadow-2xs">
                <i class="fa-solid fa-arrow-left text-slate-400 text-[11px]"></i>
                <span>Kembali ke Dashboard</span>
            </a>
            <button onclick="window.print()"
                class="px-4 py-2 rounded-xl bg-[#172B4D] hover:bg-slate-900 text-white font-bold text-xs flex items-center gap-2 transition shadow-xs cursor-pointer">
                <i class="fa-solid fa-print text-slate-300"></i>
                <span>Cetak / Ekspor Laporan</span>
            </button>
        </div>
    </div>

    <!-- KOP RESMI KHUSUS LEMBAR PRINT / CETAK -->
    <div class="hidden print:block text-center border-b-2 border-slate-900 pb-4 mb-6">
        <h2 class="text-base font-bold uppercase tracking-wider text-slate-800">Universitas Nurul Jadid (UNUJA)</h2>
        <h3 class="text-sm font-bold uppercase text-slate-700">Fakultas Teknologi Informasi — Pusat Prestasi & Delegasi</h3>
        <p class="text-xs text-slate-500 mt-1">Laporan Resmi Rekapitulasi Capaian Prestasi & Poin SKPI Mahasiswa</p>
        <p class="text-[10px] text-slate-400">Dicetak pada: {{ now()->translatedFormat('d F Y, H:i') }} WIB</p>
    </div>

    <!-- 2. KARTU STATISTIK RINGKASAN REKAPITULASI (4 METRIC CARDS) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 print:hidden">
        <!-- Total Delegasi -->
        <div class="bg-white rounded-2xl border border-[#E5EAF2] shadow-xs p-4 flex items-center justify-between transition hover:shadow-sm">
            <div>
                <span class="text-xs font-semibold text-[#8492A6] block">Total Delegasi</span>
                <span class="text-2xl font-black text-[#172B4D] mt-1 block leading-tight">{{ $totalSubmissions }}</span>
                <span class="text-[11px] text-[#8492A6] mt-0.5 block">Partisipasi kompetisi</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-[#EAF2FF] text-[#2454C5] flex items-center justify-center text-base shrink-0">
                <i class="fa-solid fa-trophy"></i>
            </div>
        </div>

        <!-- Prestasi & Juara -->
        <div class="bg-white rounded-2xl border border-[#E5EAF2] shadow-xs p-4 flex items-center justify-between transition hover:shadow-sm">
            <div>
                <span class="text-xs font-semibold text-[#8492A6] block">Prestasi Juara</span>
                <span class="text-2xl font-black text-[#172B4D] mt-1 block leading-tight">{{ $totalPrestasi }}</span>
                <span class="text-[11px] text-[#8492A6] mt-0.5 block">Tervalidasi & berprestasi</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-[#FEF3C7] text-[#D97706] flex items-center justify-center text-base shrink-0">
                <i class="fa-solid fa-medal"></i>
            </div>
        </div>

        <!-- Akumulasi Poin SKPI -->
        <div class="bg-white rounded-2xl border border-[#E5EAF2] shadow-xs p-4 flex items-center justify-between transition hover:shadow-sm">
            <div>
                <span class="text-xs font-semibold text-[#8492A6] block">Akumulasi Poin SKPI</span>
                <span class="text-2xl font-black text-[#7E22CE] mt-1 block leading-tight">+{{ number_format($totalSkpiPoints, 1) }}</span>
                <span class="text-[11px] text-[#8492A6] mt-0.5 block">Poin prestasi akademik</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-[#F3E8FF] text-[#7E22CE] flex items-center justify-center text-base shrink-0">
                <i class="fa-solid fa-star"></i>
            </div>
        </div>

        <!-- Surat Resmi Diterbitkan -->
        <div class="bg-white rounded-2xl border border-[#E5EAF2] shadow-xs p-4 flex items-center justify-between transition hover:shadow-sm">
            <div>
                <span class="text-xs font-semibold text-[#8492A6] block">Dokumen Resmi Terbit</span>
                <span class="text-2xl font-black text-[#172B4D] mt-1 block leading-tight">{{ $totalDispensasi }}</span>
                <span class="text-[11px] text-[#8492A6] mt-0.5 block">Dispensasi & surat tugas</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-[#DCFCE7] text-[#16A34A] flex items-center justify-center text-base shrink-0">
                <i class="fa-solid fa-stamp"></i>
            </div>
        </div>
    </div>

    <!-- 3. CARD UTAMA TABEL REKAPITULASI PRESTASI -->
    <div class="bg-white rounded-2xl border border-[#E5EAF2] shadow-xs overflow-hidden">

        <!-- Toolbar Pencarian & Filter (Hidden on Print) -->
        <div class="p-4 sm:p-5 border-b border-[#E5EAF2] bg-white print:hidden">
            <form action="{{ route('dekan.reports') }}" method="GET" class="flex flex-col md:flex-row items-stretch md:items-center gap-3">
                <!-- Input Pencarian -->
                <div class="relative flex-1">
                    <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Cari kode, nama lomba, ketua tim, atau NIM..."
                        class="w-full h-9 pl-9 pr-3 border border-[#E5EAF2] rounded-xl text-xs outline-none focus:ring-2 focus:ring-[#7E22CE]/20 focus:border-[#7E22CE] text-[#172B4D] placeholder:text-slate-400 transition">
                </div>

                <!-- Dropdown Tingkat Lomba -->
                <div class="w-full md:w-44">
                    <select name="level" onchange="this.form.submit()"
                        class="w-full h-9 px-3 border border-[#E5EAF2] rounded-xl text-xs outline-none focus:ring-2 focus:ring-[#7E22CE]/20 focus:border-[#7E22CE] text-[#172B4D] bg-white transition cursor-pointer">
                        <option value="">Semua Tingkat</option>
                        <option value="REGIONAL" {{ request('level') === 'REGIONAL' ? 'selected' : '' }}>Regional</option>
                        <option value="NASIONAL" {{ request('level') === 'NASIONAL' ? 'selected' : '' }}>Nasional</option>
                        <option value="INTERNASIONAL" {{ request('level') === 'INTERNASIONAL' ? 'selected' : '' }}>Internasional</option>
                    </select>
                </div>

                <!-- Dropdown Filter Capaian Prestasi -->
                <div class="w-full md:w-48">
                    <select name="achievement" onchange="this.form.submit()"
                        class="w-full h-9 px-3 border border-[#E5EAF2] rounded-xl text-xs outline-none focus:ring-2 focus:ring-[#7E22CE]/20 focus:border-[#7E22CE] text-[#172B4D] bg-white transition cursor-pointer">
                        <option value="">Semua Delegasi</option>
                        <option value="juara" {{ request('achievement') === 'juara' ? 'selected' : '' }}>Hanya Berprestasi (Juara)</option>
                    </select>
                </div>

                <!-- Tombol Reset Filter -->
                @if(request()->hasAny(['search', 'level', 'achievement']))
                    <a href="{{ route('dekan.reports') }}"
                        class="h-9 px-3.5 rounded-xl border border-[#E5EAF2] bg-white hover:bg-slate-50 text-slate-600 font-semibold text-xs flex items-center justify-center gap-1.5 transition shadow-2xs whitespace-nowrap">
                        <i class="fa-solid fa-rotate-left text-[11px] text-slate-400"></i>
                        <span>Reset</span>
                    </a>
                @endif
            </form>
        </div>

        <!-- Tabel Rekapitulasi Prestasi -->
        @if($submissions->isEmpty())
            <div class="p-12 text-center text-slate-400 text-xs">
                <div class="w-12 h-12 rounded-full bg-purple-50 text-[#7E22CE] flex items-center justify-center mx-auto mb-3 text-xl">
                    <i class="fa-solid fa-file-circle-xmark"></i>
                </div>
                <p class="font-bold text-[#172B4D] text-sm">Tidak ada data rekapitulasi yang ditemukan</p>
                <p class="text-[11px] text-[#8492A6] mt-0.5">Silakan sesuaikan kata kunci pencarian atau bersihkan filter yang aktif.</p>
                <a href="{{ route('dekan.reports') }}" class="inline-block mt-3 text-[#7E22CE] font-bold hover:underline">
                    Tampilkan Seluruh Data
                </a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs min-w-[850px] border-collapse">
                    <thead>
                        <tr class="bg-slate-50/80 text-[#8492A6] uppercase font-bold text-[10px] tracking-wider border-b border-[#E5EAF2]">
                            <th class="py-3 px-3 text-center w-12">No</th>
                            <th class="py-3 px-4">Kode & Lomba</th>
                            <th class="py-3 px-3">Tingkat</th>
                            <th class="py-3 px-4">Ketua Tim & Delegasi</th>
                            <th class="py-3 px-4">Capaian Prestasi</th>
                            <th class="py-3 px-3 text-center">Poin SKPI</th>
                            <th class="py-3 px-4">Surat Dispensasi</th>
                            <th class="py-3 px-3 text-right print:hidden">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#E5EAF2]/70">
                        @foreach($submissions as $idx => $sub)
                            @php
                                $cert = $sub->certificates->first();
                                $dispLetter = $sub->officialLetters->first(function ($l) {
                                    return str_contains(strtolower($l->subject ?? ''), 'dispensasi') || $l->letter_type->value === 'DISPENSASI';
                                });
                            @endphp
                            <tr class="hover:bg-slate-50/70 transition">
                                <!-- No -->
                                <td class="py-3 px-3 text-center text-slate-400 font-medium">
                                    {{ $submissions->firstItem() + $idx }}
                                </td>

                                <!-- Kode & Lomba -->
                                <td class="py-3 px-4">
                                    <span class="font-bold text-[#172B4D] block truncate max-w-xs" title="{{ $sub->competitionDetail?->competition_name ?? $sub->title }}">
                                        {{ $sub->competitionDetail?->competition_name ?? $sub->title }}
                                    </span>
                                    <span class="text-[11px] text-[#8492A6] font-mono block mt-0.5">{{ $sub->code }}</span>
                                </td>

                                <!-- Tingkat -->
                                <td class="py-3 px-3 whitespace-nowrap">
                                    @php
                                        $lvl = strtoupper($sub->competitionDetail?->level?->value ?? $sub->competitionDetail?->level?->name ?? 'NASIONAL');
                                    @endphp
                                    @if($lvl === 'INTERNASIONAL')
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-[#F3E8FF] text-[#7E22CE] border border-purple-200">
                                            Internasional
                                        </span>
                                    @elseif($lvl === 'NASIONAL')
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-[#EAF2FF] text-[#2454C5] border border-blue-200">
                                            Nasional
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                            Regional
                                        </span>
                                    @endif
                                </td>

                                <!-- Ketua Tim & Delegasi -->
                                <td class="py-3 px-4">
                                    <span class="font-bold text-[#172B4D] block">{{ $sub->submitter_name ?? $sub->submitter_nim }}</span>
                                    <div class="flex items-center gap-1.5 text-[11px] text-[#8492A6] mt-0.5">
                                        <span>NIM {{ $sub->submitter_nim }}</span>
                                        <span class="text-slate-300">•</span>
                                        <span class="text-[10px]"><i class="fa-solid fa-users text-slate-400 mr-0.5"></i>{{ $sub->members->count() + 1 }} Org</span>
                                    </div>
                                </td>

                                <!-- Capaian Prestasi -->
                                <td class="py-3 px-4 whitespace-nowrap">
                                    @if($cert && $cert->achievement_rank)
                                        <div class="flex items-center gap-1.5">
                                            <i class="fa-solid fa-medal text-amber-500 text-xs"></i>
                                            <div>
                                                <span class="font-bold text-[#7E22CE] block text-xs">{{ $cert->achievement_rank }}</span>
                                                <span class="text-[9px] font-bold px-1.5 py-0.2 rounded bg-emerald-50 text-emerald-700 border border-emerald-200 inline-block mt-0.5">
                                                    {{ $cert->validation_status->label() ?? 'VALID' }}
                                                </span>
                                            </div>
                                        </div>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-medium bg-slate-100 text-slate-500">
                                            <i class="fa-regular fa-clock text-[9px]"></i>
                                            <span>Peserta Delegasi</span>
                                        </span>
                                    @endif
                                </td>

                                <!-- Poin SKPI -->
                                <td class="py-3 px-3 text-center whitespace-nowrap">
                                    @if($cert && $cert->skpi_points > 0)
                                        <span class="font-black text-[#2454C5] text-xs">
                                            +{{ number_format($cert->skpi_points, 1) }}
                                        </span>
                                    @else
                                        <span class="text-slate-300 font-bold">-</span>
                                    @endif
                                </td>

                                <!-- Surat Dispensasi -->
                                <td class="py-3 px-4 whitespace-nowrap font-mono text-[11px] text-slate-600">
                                    @if($dispLetter && $dispLetter->letter_number)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-slate-50 border border-slate-200 font-semibold text-[#172B4D]">
                                            <i class="fa-solid fa-file-contract text-[#7E22CE] text-[10px]"></i>
                                            <span>{{ $dispLetter->letter_number }}</span>
                                        </span>
                                    @else
                                        <span class="text-slate-400 font-sans text-xs">-</span>
                                    @endif
                                </td>

                                <!-- Aksi (Hidden on Print) -->
                                <td class="py-3 px-3 text-right whitespace-nowrap print:hidden">
                                    <a href="{{ route('dekan.show', $sub->id) }}"
                                        class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg border border-[#E5EAF2] bg-white hover:bg-slate-50 text-[#172B4D] font-semibold text-xs transition shadow-2xs">
                                        <i class="fa-regular fa-eye text-slate-400"></i>
                                        <span>Lihat</span>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- 4. PAGINATION FOOTER (Hidden on Print) -->
            <div class="px-5 sm:px-6 py-3.5 border-t border-[#E5EAF2] flex flex-col sm:flex-row items-center justify-between gap-3 text-xs print:hidden">
                <div class="text-[#8492A6]">
                    Menampilkan <span class="font-bold text-[#172B4D]">{{ $submissions->firstItem() ?? 0 }}–{{ $submissions->lastItem() ?? 0 }}</span> dari <span class="font-bold text-[#172B4D]">{{ $submissions->total() }}</span> data prestasi
                </div>

                @if($submissions->hasPages())
                    <div class="flex items-center space-x-1.5">
                        {{-- Previous Button --}}
                        @if ($submissions->onFirstPage())
                            <span class="w-8 h-8 rounded-lg border border-[#E5EAF2] bg-slate-50 text-slate-300 flex items-center justify-center cursor-not-allowed">
                                <i class="fa-solid fa-chevron-left text-[10px]"></i>
                            </span>
                        @else
                            <a href="{{ $submissions->previousPageUrl() }}" class="w-8 h-8 rounded-lg border border-[#E5EAF2] bg-white text-[#172B4D] hover:bg-slate-50 flex items-center justify-center transition shadow-2xs">
                                <i class="fa-solid fa-chevron-left text-[10px]"></i>
                            </a>
                        @endif

                        {{-- Page Numbers --}}
                        @for ($i = 1; $i <= $submissions->lastPage(); $i++)
                            @if ($i == $submissions->currentPage())
                                <span class="w-8 h-8 rounded-lg bg-[#7E22CE] text-white font-bold flex items-center justify-center shadow-xs">
                                    {{ $i }}
                                </span>
                            @else
                                <a href="{{ $submissions->url($i) }}" class="w-8 h-8 rounded-lg border border-[#E5EAF2] bg-white text-[#172B4D] font-semibold hover:bg-slate-50 flex items-center justify-center transition shadow-2xs">
                                    {{ $i }}
                                </a>
                            @endif
                        @endfor

                        {{-- Next Button --}}
                        @if ($submissions->hasMorePages())
                            <a href="{{ $submissions->nextPageUrl() }}" class="w-8 h-8 rounded-lg border border-[#E5EAF2] bg-white text-[#172B4D] hover:bg-slate-50 flex items-center justify-center transition shadow-2xs">
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

    <!-- TANDA TANGAN RESMI KHUSUS LEMBAR PRINT -->
    <div class="hidden print:flex justify-between items-end mt-12 pt-6 px-4 text-xs">
        <div>
            <p class="text-slate-500">Sistem Informasi Pengajuan Lomba & Prestasi</p>
            <p class="font-bold text-slate-800">SIPRES UNUJA — Dokumen Cetak Sah</p>
        </div>
        <div class="text-center w-64">
            <p class="text-slate-600 mb-16">Paiton, {{ now()->translatedFormat('d F Y') }}<br>Dekan Fakultas Teknologi Informasi,</p>
            <p class="font-bold underline text-slate-900">{{ $dekan->name ?? 'Dr. Achmad Faisol, M.T' }}</p>
            <p class="text-slate-600">NIDN. {{ $dekan->identifier ?? '0715017803' }}</p>
        </div>
    </div>

</div>
@endsection
