@extends('layouts.app')

@section('title', 'Dashboard Administrator — SIPRES UNUJA')

@section('content')
<div class="space-y-6">

    <!-- HERO HEADER & ADMIN STATUS BANNER -->
    <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 rounded-3xl p-6 sm:p-8 text-white shadow-xl relative overflow-hidden">
        <div class="absolute -right-10 -bottom-10 opacity-10 text-9xl">
            <i class="fa-solid fa-shield-halved"></i>
        </div>

        <div class="relative z-10 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
            <div>
                <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-indigo-500/20 border border-indigo-400/40 text-indigo-300 text-xs font-bold uppercase tracking-wider mb-3">
                    <i class="fa-solid fa-crown text-amber-400"></i>
                    <span>Super Observer & Multi-Portal Hub</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-white">
                    Pusat Kendali & Peninjauan Administrator
                </h1>
                <p class="text-xs sm:text-sm text-indigo-200 mt-1 max-w-2xl leading-relaxed">
                    Sistem pemantauan terpusat pengajuan lomba se-Universitas Nurul Jadid. Anda memiliki wewenang untuk meninjau seluruh data, alur persetujuan, dan fitur setiap role dalam mode peninjauan aman <strong>(Read-Only)</strong>.
                </p>
            </div>

            <div class="flex items-center space-x-2 bg-white/10 backdrop-blur-md px-4 py-2.5 rounded-2xl border border-white/15 shrink-0">
                <div class="w-3 h-3 rounded-full bg-cyan-400 animate-ping mr-1"></div>
                <div class="text-left">
                    <div class="text-[10px] uppercase font-bold text-indigo-200">Mode Sistem</div>
                    <div class="text-xs font-black text-white">Observer Aktif (Live)</div>
                </div>
            </div>
        </div>
    </div>

    <!-- QUICK ACCESS TO ROLES (PORTAL SWITCHER) -->
    <div>
        <div class="flex items-center justify-between mb-3">
            <h2 class="text-xs font-extrabold uppercase tracking-wider text-slate-500 flex items-center space-x-2">
                <i class="fa-solid fa-door-open text-indigo-600"></i>
                <span>Akses Masuk & Fitur Setiap Role (Jelajah Multi-Portal)</span>
            </h2>
            <span class="text-[11px] text-slate-400">Klik untuk masuk dan melihat tampilan masing-masing role</span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- 1. Portal Mahasiswa -->
            <div class="bg-white rounded-2xl border border-slate-200 hover:border-blue-400 p-5 shadow-sm hover:shadow-md transition-all group relative overflow-hidden flex flex-col justify-between">
                <div class="space-y-3">
                    <div class="w-12 h-12 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-xl group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-user-graduate"></i>
                    </div>
                    <div>
                        <h3 class="font-extrabold text-sm text-slate-900 group-hover:text-blue-700 transition-colors">Portal Mahasiswa</h3>
                        <p class="text-[11px] text-slate-500 mt-1 leading-relaxed">
                            Tinjau daftar lomba, formulir pengajuan delegasi, pelaporan LPJ, serta unduhan surat tugas mahasiswa.
                        </p>
                    </div>
                </div>
                <div class="pt-4 mt-2 border-t border-slate-100">
                    <a href="{{ route('mahasiswa.dashboard') }}" class="inline-flex items-center text-xs font-bold text-blue-700 hover:text-blue-900 space-x-1.5">
                        <span>Masuk ke Portal Mahasiswa</span>
                        <i class="fa-solid fa-arrow-right text-[10px] group-hover:translate-x-1 transition-transform"></i>
                    </a>
                </div>
            </div>

            <!-- 2. Portal Kaprodi -->
            <div class="bg-white rounded-2xl border border-slate-200 hover:border-teal-400 p-5 shadow-sm hover:shadow-md transition-all group relative overflow-hidden flex flex-col justify-between">
                <div class="space-y-3">
                    <div class="w-12 h-12 rounded-xl bg-teal-100 text-teal-800 flex items-center justify-center font-bold text-xl group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-user-tie"></i>
                    </div>
                    <div>
                        <h3 class="font-extrabold text-sm text-slate-900 group-hover:text-teal-800 transition-colors">Portal Kaprodi</h3>
                        <p class="text-[11px] text-slate-500 mt-1 leading-relaxed">
                            Tinjau antrean proposal prodi, kelayakan substansi lomba, dan penetapan dosen pendamping resmi.
                        </p>
                    </div>
                </div>
                <div class="pt-4 mt-2 border-t border-slate-100">
                    <a href="{{ route('kaprodi.dashboard') }}" class="inline-flex items-center text-xs font-bold text-teal-700 hover:text-teal-900 space-x-1.5">
                        <span>Masuk ke Portal Kaprodi</span>
                        <i class="fa-solid fa-arrow-right text-[10px] group-hover:translate-x-1 transition-transform"></i>
                    </a>
                </div>
            </div>

            <!-- 3. Portal Dekan -->
            <div class="bg-white rounded-2xl border border-slate-200 hover:border-purple-400 p-5 shadow-sm hover:shadow-md transition-all group relative overflow-hidden flex flex-col justify-between">
                <div class="space-y-3">
                    <div class="w-12 h-12 rounded-xl bg-purple-100 text-purple-700 flex items-center justify-center font-bold text-xl group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-award"></i>
                    </div>
                    <div>
                        <h3 class="font-extrabold text-sm text-slate-900 group-hover:text-purple-800 transition-colors">Portal Dekan Fakultas</h3>
                        <p class="text-[11px] text-slate-500 mt-1 leading-relaxed">
                            Tinjau validasi final fakultas, plafon RAB disetujui, penerbitan surat ber-QR, dan pengesahan LPJ.
                        </p>
                    </div>
                </div>
                <div class="pt-4 mt-2 border-t border-slate-100">
                    <a href="{{ route('dekan.dashboard') }}" class="inline-flex items-center text-xs font-bold text-purple-700 hover:text-purple-900 space-x-1.5">
                        <span>Masuk ke Portal Dekan</span>
                        <i class="fa-solid fa-arrow-right text-[10px] group-hover:translate-x-1 transition-transform"></i>
                    </a>
                </div>
            </div>

            <!-- 4. Laporan Prestasi & SKPI -->
            <div class="bg-white rounded-2xl border border-slate-200 hover:border-amber-400 p-5 shadow-sm hover:shadow-md transition-all group relative overflow-hidden flex flex-col justify-between">
                <div class="space-y-3">
                    <div class="w-12 h-12 rounded-xl bg-amber-100 text-amber-800 flex items-center justify-center font-bold text-xl group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-chart-column"></i>
                    </div>
                    <div>
                        <h3 class="font-extrabold text-sm text-slate-900 group-hover:text-amber-800 transition-colors">Laporan Prestasi & SKPI</h3>
                        <p class="text-[11px] text-slate-500 mt-1 leading-relaxed">
                            Tinjau rekapitulasi prestasi kejuaraan, distribusi medali, dan akumulasi poin SKPI mahasiswa.
                        </p>
                    </div>
                </div>
                <div class="pt-4 mt-2 border-t border-slate-100">
                    <a href="{{ route('dekan.reports') }}" class="inline-flex items-center text-xs font-bold text-amber-800 hover:text-amber-950 space-x-1.5">
                        <span>Buka Laporan Prestasi</span>
                        <i class="fa-solid fa-arrow-right text-[10px] group-hover:translate-x-1 transition-transform"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- GLOBAL METRIC KPI CARDS -->
    <div>
        <h2 class="text-xs font-extrabold uppercase tracking-wider text-slate-500 mb-3 flex items-center space-x-2">
            <i class="fa-solid fa-chart-pie text-indigo-600"></i>
            <span>Statistik Pengajuan & Finansial Universitas</span>
        </h2>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
            <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-sm">
                <div class="text-[10px] font-bold uppercase text-slate-400">Total Pengajuan</div>
                <div class="text-2xl font-black text-slate-800 mt-1">{{ number_format($totalSubmissions) }}</div>
                <div class="text-[10px] text-slate-500 mt-0.5">Seluruh jenjang</div>
            </div>

            <div class="bg-white rounded-2xl p-4 border border-teal-200 bg-teal-50/20 shadow-sm">
                <div class="text-[10px] font-bold uppercase text-teal-700">Review Kaprodi</div>
                <div class="text-2xl font-black text-teal-800 mt-1">{{ number_format($totalReviewKaprodi) }}</div>
                <div class="text-[10px] text-teal-600 mt-0.5">Penetapan Dosen</div>
            </div>

            <div class="bg-white rounded-2xl p-4 border border-purple-200 bg-purple-50/20 shadow-sm">
                <div class="text-[10px] font-bold uppercase text-purple-700">Review Dekan</div>
                <div class="text-2xl font-black text-purple-800 mt-1">{{ number_format($totalReviewDekan) }}</div>
                <div class="text-[10px] text-purple-600 mt-0.5">Plafon Anggaran & Surat</div>
            </div>

            <div class="bg-white rounded-2xl p-4 border border-amber-200 bg-amber-50/20 shadow-sm">
                <div class="text-[10px] font-bold uppercase text-amber-700">Proses LPJ</div>
                <div class="text-2xl font-black text-amber-800 mt-1">{{ number_format($totalWaitingLpj) }}</div>
                <div class="text-[10px] text-amber-600 mt-0.5">Pasca-Lomba</div>
            </div>

            <div class="bg-white rounded-2xl p-4 border border-blue-200 bg-blue-50/30 shadow-sm">
                <div class="text-[10px] font-bold uppercase text-blue-700">Prestasi Selesai</div>
                <div class="text-2xl font-black text-blue-900 mt-1">{{ number_format($totalCompleted) }}</div>
                <div class="text-[10px] text-blue-600 mt-0.5">Status Completed</div>
            </div>

            <div class="bg-white rounded-2xl p-4 border border-indigo-200 bg-indigo-50/20 shadow-sm">
                <div class="text-[10px] font-bold uppercase text-indigo-700">Dana Disetujui</div>
                <div class="text-base font-black text-indigo-900 mt-1.5 truncate" title="Rp {{ number_format($totalBudgetApproved, 0, ',', '.') }}">
                    Rp {{ number_format($totalBudgetApproved / 1000000, 1) }} Jt
                </div>
                <div class="text-[10px] text-indigo-600 mt-0.5">Plafon Fakultas</div>
            </div>
        </div>
    </div>

    <!-- MASTER SUBMISSION TABLE -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <!-- Table Header & Filters -->
        <div class="p-5 border-b border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h3 class="font-extrabold text-base text-slate-900">Master Pengajuan Delegasi Lomba</h3>
                <p class="text-xs text-slate-500 mt-0.5">Daftar seluruh pengajuan lomba dari seluruh Program Studi dan Fakultas.</p>
            </div>

            <form method="GET" action="{{ route('admin.dashboard') }}" class="flex flex-col sm:flex-row items-center gap-2">
                <div class="relative w-full sm:w-64">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400 text-xs">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}"
                        class="w-full pl-9 pr-3 py-2 text-xs border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none"
                        placeholder="Cari judul, NIM, ketua...">
                </div>

                <select name="status" onchange="this.form.submit()"
                    class="w-full sm:w-auto text-xs px-3 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none bg-white">
                    <option value="">Semua Status</option>
                    <option value="kaprodi" {{ request('status') === 'kaprodi' ? 'selected' : '' }}>Review Kaprodi</option>
                    <option value="dekan" {{ request('status') === 'dekan' ? 'selected' : '' }}>Review Dekan</option>
                    <option value="lpj" {{ request('status') === 'lpj' ? 'selected' : '' }}>Menunggu / Review LPJ</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Selesai (Completed)</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Ditolak</option>
                </select>

                <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold transition shadow-sm">
                    Filter
                </button>

                @if(request('search') || request('status'))
                    <a href="{{ route('admin.dashboard') }}" class="px-3 py-2 border border-slate-200 text-slate-600 hover:bg-slate-50 rounded-xl text-xs font-medium transition" title="Reset filter">
                        <i class="fa-solid fa-rotate-left"></i>
                    </a>
                @endif
            </form>
        </div>

        <!-- Table View -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50/80 text-[11px] font-bold text-slate-500 uppercase tracking-wider border-b border-slate-100">
                        <th class="py-3 px-4">Kode & Judul Lomba</th>
                        <th class="py-3 px-4">Ketua Pengusul & Prodi</th>
                        <th class="py-3 px-4">Dosen Pendamping</th>
                        <th class="py-3 px-4">Anggaran</th>
                        <th class="py-3 px-4">Status Alur</th>
                        <th class="py-3 px-4 text-center">Tinjau Role</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($submissions as $sub)
                        @php
                            $badgeColor = match($sub->status) {
                                'draft' => 'bg-slate-100 text-slate-700 border-slate-200',
                                'submitted', 'review_kaprodi', 'kaprodi_review', 'dosen_review', 'review_dosen' => 'bg-teal-50 text-teal-800 border-teal-200',
                                'approved_kaprodi', 'approved_dosen', 'review_dekan', 'dekan_review' => 'bg-purple-50 text-purple-800 border-purple-200',
                                'approved_final', 'waiting_lpj', 'review_lpj', 'lpj_review' => 'bg-amber-50 text-amber-800 border-amber-200',
                                'completed' => 'bg-blue-100 text-blue-900 border-blue-300',
                                'rejected' => 'bg-rose-50 text-rose-800 border-rose-200',
                                default => 'bg-slate-100 text-slate-700 border-slate-200',
                            };
                        @endphp
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="py-3.5 px-4 align-top">
                                <span class="font-mono text-[10px] font-bold text-slate-400 block">{{ $sub->code }}</span>
                                <div class="font-bold text-slate-900 mt-0.5 line-clamp-2">{{ $sub->title }}</div>
                                <div class="text-[10px] text-slate-500 mt-0.5">
                                    <i class="fa-solid fa-trophy text-amber-500 mr-1"></i>
                                    {{ $sub->competitionDetail?->competition_name ?? 'Kompetisi Delegasi' }}
                                    ({{ $sub->competitionDetail?->level?->label() ?? 'Nasional' }})
                                </div>
                            </td>

                            <td class="py-3.5 px-4 align-top">
                                <div class="font-bold text-slate-800">{{ $sub->submitter_name }}</div>
                                <div class="text-[10px] text-slate-500">NIM: {{ $sub->submitter_nim }}</div>
                                <span class="inline-block mt-1 px-2 py-0.5 rounded text-[10px] font-medium bg-slate-100 text-slate-600">
                                    {{ $sub->study_program_id ?? $sub->study_program_name ?? 'Teknologi Informasi' }}
                                </span>
                            </td>

                            <td class="py-3.5 px-4 align-top">
                                @if($sub->supervisor_name)
                                    <div class="font-semibold text-slate-800 text-[11px]">{{ $sub->supervisor_name }}</div>
                                    <div class="text-[10px] text-slate-500">NIDN: {{ $sub->supervisor_nidn ?? '-' }}</div>
                                    <span class="text-[10px] text-teal-700 font-medium"><i class="fa-solid fa-circle-check"></i> Ditugaskan</span>
                                @else
                                    <span class="text-[11px] text-amber-600 font-semibold italic">Belum ditentukan Kaprodi</span>
                                @endif
                            </td>

                            <td class="py-3.5 px-4 align-top">
                                <div class="font-bold text-slate-800 text-[11px]">
                                    Rp {{ number_format($sub->total_budget_approved ?: $sub->total_budget_requested, 0, ',', '.') }}
                                </div>
                                <div class="text-[10px] text-slate-400">
                                    {{ $sub->total_budget_approved ? 'Disetujui Dekan' : 'Usulan RAB' }}
                                </div>
                            </td>

                            <td class="py-3.5 px-4 align-top">
                                <span class="inline-block px-2.5 py-1 rounded-full text-[10px] font-bold border uppercase tracking-wider {{ $badgeColor }}">
                                    {{ $sub->status_label ?? $sub->status }}
                                </span>
                            </td>

                            <td class="py-3.5 px-4 align-top text-center">
                                <div class="flex items-center justify-center space-x-1.5">
                                    <!-- View as Mahasiswa -->
                                    <a href="{{ route('mahasiswa.submissions.show', $sub->id) }}"
                                        class="p-1.5 rounded-lg border border-blue-200 text-blue-700 hover:bg-blue-50 text-[11px] font-semibold transition"
                                        title="Tinjau Sudut Pandang Mahasiswa">
                                        <i class="fa-solid fa-user-graduate"></i>
                                    </a>

                                    <!-- View as Kaprodi -->
                                    <a href="{{ route('kaprodi.show', $sub->id) }}"
                                        class="p-1.5 rounded-lg border border-teal-200 text-teal-700 hover:bg-teal-50 text-[11px] font-semibold transition"
                                        title="Tinjau Verifikasi Kaprodi">
                                        <i class="fa-solid fa-user-tie"></i>
                                    </a>

                                    <!-- View as Dekan -->
                                    <a href="{{ route('dekan.show', $sub->id) }}"
                                        class="p-1.5 rounded-lg border border-purple-200 text-purple-700 hover:bg-purple-50 text-[11px] font-semibold transition"
                                        title="Tinjau Validasi Dekan Fakultas">
                                        <i class="fa-solid fa-award"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-400 text-xs">
                                Tidak ada data pengajuan yang sesuai dengan filter pencarian.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($submissions->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $submissions->links() }}
            </div>
        @endif
    </div>

    <!-- RECENT SYSTEM AUDIT TRAIL FEED -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <div class="flex items-center space-x-2.5">
                <div class="w-8 h-8 rounded-lg bg-slate-100 text-slate-700 flex items-center justify-center font-bold text-xs">
                    <i class="fa-solid fa-clock-rotate-left"></i>
                </div>
                <div>
                    <h3 class="font-extrabold text-sm text-slate-900">Jejak Aktivitas & Audit Trail Sistem</h3>
                    <p class="text-[11px] text-slate-500">Log transaksi approval, perbaikan proposal, dan keputusan pengguna secara real-time.</p>
                </div>
            </div>
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider bg-slate-50 px-2.5 py-1 rounded-md border border-slate-200">
                10 Aktivitas Terakhir
            </span>
        </div>

        <div class="divide-y divide-slate-100 text-xs">
            @forelse($auditLogs as $log)
                <div class="py-3 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                    <div class="flex items-start space-x-3">
                        <div class="w-7 h-7 rounded-lg bg-indigo-50 text-indigo-700 flex items-center justify-center font-bold text-xs shrink-0 mt-0.5">
                            <i class="fa-solid fa-bolt"></i>
                        </div>
                        <div>
                            <div class="flex items-center space-x-2">
                                <span class="font-bold text-slate-800">{{ $log->user_name ?? 'Sistem' }}</span>
                                <span class="px-2 py-0.2 rounded text-[10px] font-semibold uppercase bg-slate-100 text-slate-600">
                                    {{ $log->user_role ?? 'System' }}
                                </span>
                            </div>
                            <p class="text-[11px] text-slate-500 mt-0.5">
                                Melakukan aksi <strong class="text-slate-700">{{ $log->action }}</strong> pada <span class="font-mono text-slate-600">{{ $log->resource_type }} #{{ $log->resource_id }}</span>
                                @if($log->service_name)
                                    <span class="text-slate-400">({{ $log->service_name }})</span>
                                @endif
                            </p>
                        </div>
                    </div>
                    <div class="text-[11px] text-slate-400 sm:text-right shrink-0">
                        {{ \Carbon\Carbon::parse($log->created_at)->diffForHumans() }}
                        <span class="text-[10px] block font-mono text-slate-300">{{ $log->created_at }}</span>
                    </div>
                </div>
            @empty
                <div class="py-4 text-center text-slate-400 text-xs">
                    Belum ada jejak log aktivitas sistem yang tercatat.
                </div>
            @endforelse
        </div>
    </div>

</div>
@endsection

