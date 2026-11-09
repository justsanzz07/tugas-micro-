@extends('layouts.app')

@section('title', 'Persetujuan Final Dekan — ' . $submission->code)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Breadcrumb & Header -->
    <div class="flex items-center space-x-2 text-xs text-slate-500">
        <a href="{{ route('dekan.dashboard') }}" class="hover:text-purple-700">Dashboard Dekan</a>
        <span>/</span>
        <span class="text-slate-800 font-semibold">Validasi Final Fakultas [{{ $submission->code }}]</span>
    </div>

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight">{{ $submission->competitionDetail?->competition_name ?? $submission->title }}</h1>
            <p class="text-xs text-slate-500">
                Ketua Pengusul: <strong>{{ $submission->submitter_name ?? $submission->submitter_nim }}</strong> &bull;
                Fakultas: {{ $submission->fakultas_id }} &bull;
                Dosen Pendamping: <strong>{{ $submission->supervisor_name ?? 'Belum Ditunjuk' }}</strong>
            </p>
        </div>
        <div>
            <span class="px-3.5 py-1.5 rounded-full text-xs font-bold bg-purple-100 text-purple-800 border border-purple-200">
                Status: {{ $submission->current_status?->label() }}
            </span>
        </div>
    </div>

    <!-- CATATAN REKOMENDASI DARI KAPRODI & DOSEN PENDAMPING -->
    @php
        $kaprodiStep = $flow?->steps()->where('step_order', 1)->first();
        $kaprodiNotes = $kaprodiStep?->notes;
    @endphp
    @if($kaprodiNotes || $submission->supervisor_name)
        <div class="bg-teal-50 border border-teal-200 p-4 rounded-2xl flex items-start space-x-3 text-xs text-teal-950">
            <i class="fa-solid fa-user-tie text-teal-700 mt-0.5 text-base"></i>
            <div>
                <span class="font-bold block text-teal-900">Validasi Kaprodi & Penetapan Dosen Pendamping:</span>
                <p class="mt-0.5 text-teal-800">
                    Dosen Pendamping: <strong>{{ $submission->supervisor_name }}</strong> (NIDN: {{ $submission->supervisor_nidn ?? '-' }})
                </p>
                @if($kaprodiNotes)
                    <p class="mt-1 text-teal-800 italic bg-white/70 p-2 rounded-lg border border-teal-200">
                        "{{ $kaprodiNotes }}"
                    </p>
                @endif
            </div>
        </div>
    @endif

    <!-- DETAIL LOMBA & PROPOSAL -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 space-y-4">
        <h3 class="text-sm font-bold text-slate-800 border-b border-slate-100 pb-3 flex items-center">
            <i class="fa-solid fa-file-signature text-purple-600 mr-2"></i>
            Detail Delegasi & Dokumen Proposal Mahasiswa
        </h3>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
            <div>
                <span class="text-slate-400 block text-[10px] uppercase font-semibold">Jadwal Perlombaan</span>
                <span class="font-bold text-slate-800">{{ $submission->competitionDetail?->start_date?->format('d M Y') ?? '-' }} s/d {{ $submission->competitionDetail?->end_date?->format('d M Y') ?? '-' }}</span>
            </div>
            <div>
                <span class="text-slate-400 block text-[10px] uppercase font-semibold">Tingkat & Kategori</span>
                <span class="font-bold text-slate-800">{{ $submission->competitionDetail?->level?->label() ?? 'REGIONAL' }} ({{ $submission->competitionDetail?->category ?? '-' }})</span>
            </div>
            <div>
                <span class="text-slate-400 block text-[10px] uppercase font-semibold">Lokasi Penyelenggaraan</span>
                <span class="font-bold text-slate-800">{{ $submission->competitionDetail?->location ?? 'Online / Kampus' }}</span>
            </div>
            <div>
                <span class="text-slate-400 block text-[10px] uppercase font-semibold">Daftar Anggota Delegasi</span>
                <span class="font-bold text-slate-800">{{ $submission->members->pluck('nim')->join(', ') }}</span>
            </div>
            <div class="sm:col-span-2">
                <span class="text-slate-400 block text-[10px] uppercase font-semibold mb-1">Dokumen Proposal (PDF)</span>
                <div class="flex items-center justify-between bg-slate-50 p-3.5 rounded-xl border border-slate-200">
                    <div class="flex items-center space-x-3">
                        <i class="fa-solid fa-file-pdf text-rose-600 text-2xl"></i>
                        <div>
                            <div class="font-bold text-slate-800">
                                {{ $submission->proposal_path ? basename($submission->proposal_path) : 'Proposal-' . $submission->code . '.pdf' }}
                            </div>
                            <div class="text-[10px] text-slate-400">PDF Proposal Mahasiswa &bull; Telah Diverifikasi Kaprodi</div>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2">
                        <a href="{{ route('dekan.proposal', $submission->id) }}" target="_blank" class="px-3 py-1.5 bg-blue-100 hover:bg-blue-200 text-blue-900 rounded-lg font-bold text-xs transition flex items-center space-x-1.5">
                            <i class="fa-solid fa-eye"></i>
                            <span>Buka / Lihat Proposal</span>
                        </a>
                        <a href="{{ route('dekan.proposal', ['submission' => $submission->id, 'download' => 1]) }}" class="p-1.5 bg-slate-200 hover:bg-slate-300 text-slate-700 rounded-lg text-xs font-bold transition" title="Unduh PDF">
                            <i class="fa-solid fa-download"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- RINCIAN USULAN ANGGARAN (RAB) -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h3 class="text-sm font-bold text-slate-800 flex items-center">
                <i class="fa-solid fa-money-bill-wave text-blue-600 mr-2"></i>
                Rencana Anggaran Biaya (RAB)
            </h3>
            <span class="text-xs font-bold text-blue-800 bg-blue-50 px-3 py-1 rounded-lg border border-blue-200">
                Total Usulan: Rp {{ number_format($budget?->total_requested ?? $submission->total_budget_requested ?? 0, 0, ',', '.') }}
            </span>
        </div>

        <table class="w-full text-left text-xs">
            <thead>
                <tr class="bg-slate-50 text-slate-500 uppercase text-[10px] font-bold border-b border-slate-200">
                    <th class="py-2.5 px-3">Item Pengeluaran</th>
                    <th class="py-2.5 px-3 text-center">Vol</th>
                    <th class="py-2.5 px-3">Satuan</th>
                    <th class="py-2.5 px-3 text-right">Harga Satuan</th>
                    <th class="py-2.5 px-3 text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($budget?->items ?? [] as $item)
                    <tr>
                        <td class="py-2.5 px-3 font-semibold text-slate-800">{{ $item->item_name }}</td>
                        <td class="py-2.5 px-3 text-center">{{ $item->quantity }}</td>
                        <td class="py-2.5 px-3 text-slate-500">{{ $item->unit }}</td>
                        <td class="py-2.5 px-3 text-right">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                        <td class="py-2.5 px-3 text-right font-bold text-slate-800">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-4 text-center text-slate-400">Tidak ada rincian item anggaran</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- FORM KEPUTUSAN FINAL DEKAN FAKULTAS -->
    @if(auth()->user()->isAdmin())
        <div class="bg-indigo-50/70 border-2 border-indigo-200 rounded-2xl p-6 text-indigo-950 flex items-start space-x-3 shadow-sm">
            <div class="w-8 h-8 rounded-lg bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold text-sm shrink-0">
                <i class="fa-solid fa-eye"></i>
            </div>
            <div>
                <h3 class="text-sm font-bold text-slate-800">Mode Peninjauan Administrator (Read-Only)</h3>
                <p class="text-xs text-indigo-700 mt-1 leading-relaxed">
                    Anda sedang meninjau berkas pengajuan ini sebagai Administrator Sistem. Penetapan plafon anggaran dana dan persetujuan final penerbitan surat tugas resmi hanya dapat dieksekusi oleh Dekan Fakultas terkait.
                </p>
            </div>
        </div>
    @elseif(in_array($submission->current_status?->value, ['REVIEW_DEKAN', 'APPROVED_KAPRODI']))
        <div class="bg-white rounded-2xl border-2 border-purple-300 shadow-md p-6 space-y-5">
            <div class="flex items-center space-x-2.5 border-b border-slate-100 pb-3">
                <div class="w-8 h-8 rounded-lg bg-purple-100 text-purple-700 flex items-center justify-center font-bold text-sm">
                    <i class="fa-solid fa-stamp"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-800">Formulir Keputusan Final Dekan Fakultas</h3>
                    <p class="text-[11px] text-slate-500">Persetujuan final Anda akan menetapkan plafon dana disetujui serta otomatis menerbitkan Surat Dispensasi Kuliah dan Surat Tugas Resmi ber-QR Code.</p>
                </div>
            </div>

            <form action="{{ route('dekan.process', $submission->id) }}" method="POST" class="space-y-4">
                @csrf

                <!-- PENETAPAN PLAFON DANA BANTUAN OLEH DEKAN -->
                <div class="bg-purple-50/60 p-4 rounded-xl border border-purple-200 space-y-2">
                    <label class="block text-xs font-bold text-purple-950">
                        <i class="fa-solid fa-coins text-purple-700 mr-1.5"></i>
                        Plafon Bantuan Dana Disetujui (RAB Final) <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative w-full sm:w-72">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-500 font-bold text-xs">Rp</span>
                        <input type="number" name="approved_amount"
                            value="{{ old('approved_amount', $budget?->total_requested ?? $submission->total_budget_requested ?? 0) }}"
                            min="0" step="50000"
                            class="w-full text-xs pl-9 pr-3 py-2.5 border border-purple-300 rounded-xl bg-white focus:ring-2 focus:ring-purple-500 outline-none font-bold text-slate-800">
                    </div>
                    <p class="text-[11px] text-purple-800 leading-relaxed">
                        Nominal ini akan dicatatkan pada tiket pencairan SIMPAY dan menjadi dasar pertanggungjawaban nota LPJ mahasiswa.
                    </p>
                </div>

                <!-- CATATAN REKOMENDASI DEKAN -->
                <div class="text-xs">
                    <label class="block font-semibold text-slate-700 mb-1">Catatan Keputusan Dekan</label>
                    <textarea name="notes" rows="3" class="w-full px-3 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-purple-500 outline-none"
                        placeholder="Berikan arahan, pesan penyemangat, atau catatan dispensasi untuk tim mahasiswa..."></textarea>
                </div>

                <div class="flex flex-col sm:flex-row items-center justify-end gap-3 pt-3 border-t border-slate-100">
                    <!-- Reject -->
                    <button type="submit" name="action" value="REJECT" onclick="return confirm('Apakah Anda yakin ingin MENOLAK proposal pengajuan lomba ini?')"
                        class="w-full sm:w-auto px-4 py-2.5 rounded-xl border border-rose-300 text-rose-700 hover:bg-rose-50 font-bold text-xs transition flex items-center justify-center space-x-1.5">
                        <i class="fa-solid fa-xmark"></i>
                        <span>Tolak Proposal</span>
                    </button>

                    <!-- Revise -->
                    <button type="submit" name="action" value="REVISE"
                        class="w-full sm:w-auto px-4 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-bold text-xs transition flex items-center justify-center space-x-1.5 shadow">
                        <i class="fa-solid fa-rotate-left"></i>
                        <span>Minta Revisi Proposal</span>
                    </button>

                    <!-- Approve Final -->
                    <button type="submit" name="action" value="APPROVE"
                        class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs transition flex items-center justify-center space-x-1.5 shadow-md">
                        <i class="fa-solid fa-stamp"></i>
                        <span>Setujui Final & Terbitkan Surat Resmi</span>
                    </button>
                </div>
            </form>
        </div>
    @endif

</div>
@endsection
