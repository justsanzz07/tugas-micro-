@extends('layouts.admin')

@section('title', 'Audit Trail Sistem — Admin SIPRES')
@section('breadcrumb', 'Audit Trail')
@section('header_title', 'Jejak Audit & Riwayat Transaksi Sistem')

@section('content')
<div class="space-y-6">

    <!-- Header & Info -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h1 class="text-xl font-extrabold text-[#172554] tracking-tight">Audit Trail &amp; Rekam Jejak Aktivitas</h1>
            <p class="text-xs text-slate-500">Log transaksi audit otomatis yang mencatat setiap pengajuan, keputusan approval, pencairan kas, dan perubahan akun.</p>
        </div>
        <div class="inline-flex items-center space-x-2 px-3 py-1.5 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 text-xs font-bold self-start sm:self-auto">
            <i class="fa-solid fa-lock text-amber-600"></i>
            <span>Log Bersifat Permanen &amp; Read-Only</span>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-4 shadow-2xs">
        <form method="GET" action="{{ route('admin.audit.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 text-xs">
            <div class="lg:col-span-2">
                <label class="block font-bold text-slate-700 mb-1">Cari Aktivitas</label>
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Cari nama pengguna, aksi, atau ID pengajuan..."
                        class="w-full pl-9 pr-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-[#2454C5]">
                    <i class="fa-solid fa-magnifying-glass absolute left-3 top-2.5 text-slate-400"></i>
                </div>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">Jenis Aksi</label>
                <select name="action" class="w-full py-2 px-3 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-[#2454C5]">
                    <option value="">Semua Aksi</option>
                    <option value="SUBMIT" {{ request('action') === 'SUBMIT' ? 'selected' : '' }}>SUBMIT (Pengajuan)</option>
                    <option value="APPROVE" {{ request('action') === 'APPROVE' ? 'selected' : '' }}>APPROVE (Persetujuan)</option>
                    <option value="REVISE" {{ request('action') === 'REVISE' ? 'selected' : '' }}>REVISE (Permintaan Revisi)</option>
                    <option value="REJECT" {{ request('action') === 'REJECT' ? 'selected' : '' }}>REJECT (Penolakan)</option>
                    <option value="DISBURSE" {{ request('action') === 'DISBURSE' ? 'selected' : '' }}>DISBURSE (Pencairan Kas)</option>
                    <option value="VERIFY_LPJ" {{ request('action') === 'VERIFY_LPJ' ? 'selected' : '' }}>VERIFY_LPJ (Audit LPJ)</option>
                    <option value="USER_CREATED" {{ request('action') === 'USER_CREATED' ? 'selected' : '' }}>USER_CREATED (Buat User)</option>
                    <option value="USER_UPDATED" {{ request('action') === 'USER_UPDATED' ? 'selected' : '' }}>USER_UPDATED (Ubah User)</option>
                </select>
            </div>

            <div class="flex items-end space-x-2">
                <div class="flex-1">
                    <label class="block font-bold text-slate-700 mb-1">Peran (Role)</label>
                    <select name="role" class="w-full py-2 px-3 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-[#2454C5]">
                        <option value="">Semua Role</option>
                        <option value="mahasiswa" {{ request('role') === 'mahasiswa' ? 'selected' : '' }}>Mahasiswa</option>
                        <option value="kaprodi" {{ request('role') === 'kaprodi' ? 'selected' : '' }}>Kaprodi</option>
                        <option value="dekan" {{ request('role') === 'dekan' ? 'selected' : '' }}>Dekan</option>
                        <option value="keuangan" {{ request('role') === 'keuangan' ? 'selected' : '' }}>Keuangan</option>
                        <option value="warek_ii" {{ request('role') === 'warek_ii' ? 'selected' : '' }}>Warek II</option>
                        <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                    </select>
                </div>
                <button type="submit" class="px-4 py-2 bg-[#2454C5] hover:bg-[#1d4ed8] text-white font-bold rounded-xl text-xs transition shrink-0">
                    <i class="fa-solid fa-filter mr-1"></i> Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Tabel Audit Trail -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-2xs overflow-hidden">
        <div class="p-4 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center space-x-2 font-extrabold text-xs text-slate-800 uppercase tracking-wider">
                <i class="fa-solid fa-clock-rotate-left text-[#2454C5]"></i>
                <span>Jejak Audit Log (Total: {{ $logs->total() }})</span>
            </div>
            @if(request()->hasAny(['search', 'action', 'role']))
                <a href="{{ route('admin.audit.index') }}" class="text-xs text-rose-600 hover:underline font-semibold flex items-center space-x-1">
                    <i class="fa-solid fa-rotate-left text-[10px]"></i>
                    <span>Reset Filter</span>
                </a>
            @endif
        </div>

        @if($logs->isEmpty())
            <div class="p-12 text-center text-xs text-slate-400">
                <i class="fa-solid fa-receipt text-3xl text-slate-300 mb-2"></i>
                <p>Tidak ada catatan jejak audit yang sesuai filter.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-slate-50 text-slate-500 font-bold uppercase text-[10px] border-b border-slate-200">
                            <th class="py-3 px-4">Waktu &amp; IP</th>
                            <th class="py-3 px-4">Pengguna &amp; Role</th>
                            <th class="py-3 px-4 text-center">Aksi (Action)</th>
                            <th class="py-3 px-4">Modul / Resource</th>
                            <th class="py-3 px-4">Rincian Data (Payload)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($logs as $log)
                            <tr class="hover:bg-slate-50/60 transition">
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <span class="font-extrabold text-slate-800 block">
                                        {{ $log->created_at ? $log->created_at->format('d M Y, H:i:s') : '-' }}
                                    </span>
                                    <span class="text-[10px] font-mono text-slate-400 block mt-0.5">
                                        IP: {{ $log->ip_address ?? '127.0.0.1' }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="font-bold text-slate-800 block">{{ $log->user_name ?? 'Sistem' }}</span>
                                    <span class="inline-block mt-0.5 px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-slate-100 text-slate-600">
                                        {{ $log->user_role ?? 'User' }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                    @php
                                        $actionBadge = match($log->action) {
                                            'APPROVE', 'DISBURSE' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                            'REVISE' => 'bg-amber-50 text-amber-700 border-amber-200',
                                            'REJECT' => 'bg-rose-50 text-rose-700 border-rose-200',
                                            'SUBMIT' => 'bg-blue-50 text-blue-700 border-blue-200',
                                            default => 'bg-slate-100 text-slate-700 border-slate-200',
                                        };
                                    @endphp
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-black border uppercase {{ $actionBadge }}">
                                        {{ $log->action }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="font-bold text-slate-700 block">{{ ucfirst($log->resource_type ?? 'Submission') }}</span>
                                    @if($log->resource_id)
                                        <span class="text-[10px] font-mono text-slate-400">ID: {{ $log->resource_id }}</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 max-w-xs text-[11px] text-slate-600 font-mono">
                                    @if($log->payload_after)
                                        <span class="truncate block" title="{{ json_encode($log->payload_after) }}">
                                            {{ is_array($log->payload_after) ? json_encode($log->payload_after) : $log->payload_after }}
                                        </span>
                                    @else
                                        <span class="text-slate-400 italic font-sans">-</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="p-3 border-t border-slate-100">
                {{ $logs->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
