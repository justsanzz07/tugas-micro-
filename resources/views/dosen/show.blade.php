@extends('layouts.app')

@section('title', 'Verifikasi Bimbingan — ' . $submission->code)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Breadcrumb & Header -->
    <div class="flex items-center space-x-2 text-xs text-slate-500">
        <a href="{{ route('dosen.dashboard') }}" class="hover:text-blue-700">Dashboard Dosen</a>
        <span>/</span>
        <span class="text-slate-800 font-semibold">Verifikasi Bimbingan [{{ $submission->code }}]</span>
    </div>

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight">{{ $submission->competitionDetail?->competition_name }}</h1>
            <p class="text-xs text-slate-500">Ketua Tim: {{ $submission->mahasiswa_nim }} &bull; Penyelenggara: {{ $submission->competitionDetail?->organizer }}</p>
        </div>
        <div>
            <span class="px-3 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-800">
                Status: {{ $submission->current_status?->label() }}
            </span>
        </div>
    </div>

    <!-- DETAIL LOMBA & PROPOSAL -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 space-y-4">
        <h3 class="text-sm font-bold text-slate-800 border-b border-slate-100 pb-3 flex items-center">
            <i class="fa-solid fa-file-pdf text-rose-500 mr-2"></i>
            Proposal & Informasi Delegasi
        </h3>

        <div class="grid grid-cols-2 gap-4 text-xs">
            <div>
                <span class="text-slate-400 block text-[10px] uppercase font-semibold">Tingkat & Kategori</span>
                <span class="font-bold text-slate-800">{{ $submission->competitionDetail?->level?->label() }} &bull; {{ $submission->competitionDetail?->category }}</span>
            </div>
            <div>
                <span class="text-slate-400 block text-[10px] uppercase font-semibold">Tanggal Pelaksanaan</span>
                <span class="font-bold text-slate-800">{{ $submission->competitionDetail?->start_date?->format('d M Y') }} s/d {{ $submission->competitionDetail?->end_date?->format('d M Y') }}</span>
            </div>
            <div>
                <span class="text-slate-400 block text-[10px] uppercase font-semibold">Lokasi</span>
                <span class="font-bold text-slate-800">{{ $submission->competitionDetail?->location }}</span>
            </div>
            <div>
                <span class="text-slate-400 block text-[10px] uppercase font-semibold">Anggota Tim</span>
                <span class="font-bold text-slate-800">{{ $submission->members->pluck('nim')->join(', ') }}</span>
            </div>
            <div class="col-span-2">
                <span class="text-slate-400 block text-[10px] uppercase font-semibold">Dokumen Proposal Mahasiswa</span>
                @php $doc = $submission->documents->first(); @endphp
                @if($doc)
                    <div class="mt-1 flex items-center justify-between bg-slate-50 p-3 rounded-xl border border-slate-200">
                        <div class="flex items-center space-x-2">
                            <i class="fa-solid fa-file-pdf text-rose-600 text-xl"></i>
                            <div>
                                <div class="font-bold text-slate-800">{{ $doc->file_name }}</div>
                                <div class="text-[10px] text-slate-400">{{ number_format($doc->file_size / 1024, 0) }} KB</div>
                            </div>
                        </div>
                        <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded font-bold text-xs">Siap Diperiksa</span>
                    </div>
                @else
                    <span class="text-slate-400">Tidak ada file proposal</span>
                @endif
            </div>
        </div>
    </div>

    <!-- RINCIAN RAB DIAJUKAN -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h3 class="text-sm font-bold text-slate-800 flex items-center">
                <i class="fa-solid fa-money-bill-wave text-blue-600 mr-2"></i>
                Rincian Usulan RAB Mahasiswa
            </h3>
            <span class="text-xs font-bold text-blue-800">
                Total: Rp {{ number_format($budget?->total_requested ?? 0, 0, ',', '.') }}
            </span>
        </div>

        <table class="w-full text-left text-xs">
            <thead>
                <tr class="bg-slate-50 text-slate-500 uppercase text-[10px] border-b border-slate-200">
                    <th class="py-2 px-3">Item</th>
                    <th class="py-2 px-3 text-center">Vol</th>
                    <th class="py-2 px-3">Satuan</th>
                    <th class="py-2 px-3 text-right">Harga Satuan</th>
                    <th class="py-2 px-3 text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($budget?->items ?? [] as $item)
                    <tr>
                        <td class="py-2 px-3 font-medium text-slate-800">{{ $item->item_name }}</td>
                        <td class="py-2 px-3 text-center">{{ $item->quantity }}</td>
                        <td class="py-2 px-3 text-slate-500">{{ $item->unit }}</td>
                        <td class="py-2 px-3 text-right">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                        <td class="py-2 px-3 text-right font-bold text-slate-800">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-3 text-center text-slate-400">Tidak ada item RAB</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- FORM KEPUTUSAN DOSEN PEMBIMBING -->
    @if($submission->current_status?->value === 'REVIEW_DOSEN')
        <div class="bg-white rounded-2xl border-2 border-blue-200 shadow-sm p-6 space-y-4">
            <div class="flex items-center space-x-2 border-b border-slate-100 pb-3">
                <i class="fa-solid fa-gavel text-blue-600 text-lg"></i>
                <h3 class="text-sm font-bold text-slate-800">Formulir Keputusan Verifikasi Bimbingan</h3>
            </div>

            <form action="{{ route('dosen.process', $submission->id) }}" method="POST" class="space-y-4">
                @csrf
                <div class="text-xs">
                    <label class="block font-semibold text-slate-700 mb-1">Catatan Evaluasi / Rekomendasi Teknis</label>
                    <textarea name="notes" rows="3" class="w-full px-3 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none"
                        placeholder="Berikan masukan teknis proposal, catatan revisi, atau alasan rekomendasi..."></textarea>
                </div>

                <div class="flex flex-col sm:flex-row items-center justify-end gap-3 pt-2">
                    <!-- Reject -->
                    <button type="submit" name="action" value="REJECT" onclick="return confirm('Apakah Anda yakin ingin MENOLAK pengajuan ini?')"
                        class="w-full sm:w-auto px-4 py-2.5 rounded-xl border border-rose-300 text-rose-700 hover:bg-rose-50 font-bold text-xs transition flex items-center justify-center space-x-1.5">
                        <i class="fa-solid fa-xmark"></i>
                        <span>Tolak Pengajuan</span>
                    </button>

                    <!-- Revise -->
                    <button type="submit" name="action" value="REVISE"
                        class="w-full sm:w-auto px-4 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-bold text-xs transition flex items-center justify-center space-x-1.5 shadow">
                        <i class="fa-solid fa-rotate-left"></i>
                        <span>Minta Mahasiswa Revisi</span>
                    </button>

                    <!-- Approve -->
                    <button type="submit" name="action" value="APPROVE"
                        class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs transition flex items-center justify-center space-x-1.5 shadow-md">
                        <i class="fa-solid fa-check"></i>
                        <span>Setujui & Teruskan ke Dekan</span>
                    </button>
                </div>
            </form>
        </div>
    @endif

</div>
@endsection

