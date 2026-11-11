@extends('layouts.admin')

@section('title', 'Pemantauan Pengajuan Lomba — Admin SIPRES')
@section('breadcrumb', 'Pengajuan Lomba')
@section('header_title', 'Pemantauan Seluruh Pengajuan Lomba Mahasiswa')

@section('content')
<div class="space-y-6">

    <!-- Header & Info Read-Only -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h1 class="text-xl font-extrabold text-[#172554] tracking-tight">Daftar Seluruh Delegasi &amp; Pengajuan Lomba</h1>
            <p class="text-xs text-slate-500">Pemantauan siklus delegasi, dokumen proposal, penetapan plafon, serta riwayat persetujuan mahasiswa.</p>
        </div>
        <div class="inline-flex items-center space-x-2 px-3 py-1.5 rounded-xl bg-blue-50 border border-blue-200 text-blue-800 text-xs font-bold self-start sm:self-auto">
            <i class="fa-solid fa-eye text-[#2454C5]"></i>
            <span>Mode Pemantauan (Read-Only)</span>
        </div>
    </div>

    <!-- Filter Bar (Search, Fakultas, Tingkat, Status) -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-4 shadow-2xs">
        <form method="GET" action="{{ route('admin.submissions.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 text-xs">
            <!-- Pencarian -->
            <div class="lg:col-span-2">
                <label class="block font-bold text-slate-700 mb-1">Pencarian</label>
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Cari kode, nama mahasiswa, NIM, atau lomba..."
                        class="w-full pl-9 pr-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-[#2454C5] focus:border-[#2454C5]">
                    <i class="fa-solid fa-magnifying-glass absolute left-3 top-2.5 text-slate-400"></i>
                </div>
            </div>

            <!-- Filter Fakultas -->
            <div>
                <label class="block font-bold text-slate-700 mb-1">Fakultas</label>
                <select name="faculty" class="w-full py-2 px-3 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-[#2454C5]">
                    <option value="">Semua Fakultas</option>
                    <option value="FTI" {{ request('faculty') === 'FTI' ? 'selected' : '' }}>Fakultas Teknologi Informasi (FTI)</option>
                    <option value="FAI" {{ request('faculty') === 'FAI' ? 'selected' : '' }}>Fakultas Agama Islam (FAI)</option>
                    <option value="FEB" {{ request('faculty') === 'FEB' ? 'selected' : '' }}>Fakultas Ekonomi &amp; Bisnis (FEB)</option>
                    <option value="FKES" {{ request('faculty') === 'FKES' ? 'selected' : '' }}>Fakultas Kesehatan (FKES)</option>
                </select>
            </div>

            <!-- Filter Tingkat -->
            <div>
                <label class="block font-bold text-slate-700 mb-1">Tingkat Lomba</label>
                <select name="level" class="w-full py-2 px-3 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-[#2454C5]">
                    <option value="">Semua Tingkat</option>
                    <option value="Nasional" {{ request('level') === 'Nasional' ? 'selected' : '' }}>Nasional</option>
                    <option value="Internasional" {{ request('level') === 'Internasional' ? 'selected' : '' }}>Internasional</option>
                    <option value="Regional" {{ request('level') === 'Regional' ? 'selected' : '' }}>Regional / Wilayah</option>
                </select>
            </div>

            <!-- Filter Status & Tombol Submit -->
            <div class="flex items-end space-x-2">
                <div class="flex-1">
                    <label class="block font-bold text-slate-700 mb-1">Status</label>
                    <select name="status" class="w-full py-2 px-3 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-[#2454C5]">
                        <option value="">Semua Status</option>
                        <option value="kaprodi" {{ request('status') === 'kaprodi' ? 'selected' : '' }}>Review Kaprodi</option>
                        <option value="dekan" {{ request('status') === 'dekan' ? 'selected' : '' }}>Review Dekan</option>
                        <option value="keuangan" {{ request('status') === 'keuangan' ? 'selected' : '' }}>Review Keuangan</option>
                        <option value="warek" {{ request('status') === 'warek' ? 'selected' : '' }}>Review Warek II</option>
                        <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Disetujui / Siap Cair</option>
                        <option value="waiting_lpj" {{ request('status') === 'waiting_lpj' ? 'selected' : '' }}>Menunggu LPJ</option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Selesai (Completed)</option>
                        <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Ditolak</option>
                    </select>
                </div>
                <button type="submit" class="px-4 py-2 bg-[#2454C5] hover:bg-[#1d4ed8] text-white font-bold rounded-xl text-xs transition shrink-0">
                    <i class="fa-solid fa-filter mr-1"></i> Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Tabel Pengajuan Lomba -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-2xs overflow-hidden">
        <div class="p-4 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center space-x-2 font-extrabold text-xs text-slate-800 uppercase tracking-wider">
                <i class="fa-solid fa-list-check text-[#2454C5]"></i>
                <span>Data Pengajuan (Total: {{ $submissions->total() }})</span>
            </div>
            @if(request()->hasAny(['search', 'faculty', 'level', 'status']))
                <a href="{{ route('admin.submissions.index') }}" class="text-xs text-rose-600 hover:underline font-semibold flex items-center space-x-1">
                    <i class="fa-solid fa-rotate-left text-[10px]"></i>
                    <span>Reset Filter</span>
                </a>
            @endif
        </div>

        @if($submissions->isEmpty())
            <div class="p-12 text-center text-xs text-slate-400">
                <div class="w-12 h-12 mx-auto rounded-xl bg-slate-100 text-slate-400 flex items-center justify-center text-xl mb-3">
                    <i class="fa-solid fa-inbox"></i>
                </div>
                <h4 class="font-bold text-slate-700">Tidak Ada Pengajuan Ditemukan</h4>
                <p class="mt-1">Coba sesuaikan kata kunci pencarian atau reset filter di atas.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-slate-50 text-slate-500 font-bold uppercase text-[10px] border-b border-slate-200">
                            <th class="py-3 px-4">Kode &amp; Tanggal</th>
                            <th class="py-3 px-4">Ketua &amp; Tim</th>
                            <th class="py-3 px-4">Fakultas &amp; Prodi</th>
                            <th class="py-3 px-4">Nama Lomba &amp; Tingkat</th>
                            <th class="py-3 px-4 text-right">Usulan RAB</th>
                            <th class="py-3 px-4 text-right">Plafon Disetujui</th>
                            <th class="py-3 px-4 text-center">Status</th>
                            <th class="py-3 px-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($submissions as $sub)
                            <tr class="hover:bg-slate-50/60 transition">
                                <td class="py-3.5 px-4">
                                    <span class="font-extrabold text-[#2454C5] block">{{ $sub->code }}</span>
                                    <span class="text-[10px] text-slate-400 block">{{ $sub->created_at ? $sub->created_at->format('d M Y') : '-' }}</span>
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="font-bold text-slate-800 block">{{ $sub->submitter_name }}</span>
                                    <span class="text-[11px] text-slate-500 block">NIM: {{ $sub->submitter_nim }}</span>
                                    @if($sub->members && $sub->members->count() > 0)
                                        <span class="text-[10px] text-blue-600 block mt-0.5">+{{ $sub->members->count() }} Anggota</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="font-bold text-slate-700 block">{{ $sub->fakultas_id }}</span>
                                    <span class="text-[11px] text-slate-400 block">{{ $sub->study_program_name ?? $sub->study_program_id }}</span>
                                </td>
                                <td class="py-3.5 px-4 max-w-xs">
                                    <span class="font-bold text-slate-800 block truncate">{{ $sub->competitionDetail?->name ?? $sub->title }}</span>
                                    <span class="inline-block mt-0.5 px-2 py-0.5 rounded text-[10px] font-semibold bg-blue-50 text-blue-700">
                                        {{ $sub->competitionDetail?->level ?? 'Nasional' }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-right font-medium text-slate-600">
                                    Rp {{ number_format($sub->total_budget_requested, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-4 text-right">
                                    <span class="font-black text-emerald-700">
                                        Rp {{ number_format($sub->total_budget_approved ?: 0, 0, ',', '.') }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold border {{ $sub->current_status->badgeColor() }}">
                                        {{ $sub->current_status->label() }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    <a href="{{ route('admin.submissions.show', $sub) }}"
                                        class="inline-flex items-center space-x-1 px-3 py-1.5 rounded-lg text-xs font-bold bg-slate-100 hover:bg-blue-50 text-slate-700 hover:text-[#2454C5] transition">
                                        <i class="fa-solid fa-eye text-xs"></i>
                                        <span>Detail</span>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="p-3 border-t border-slate-100">
                {{ $submissions->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
