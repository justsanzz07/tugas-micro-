@extends('layouts.app')

@section('title', 'Dashboard Keuangan — SIPRES UNUJA')

@section('content')
<div class="space-y-6">

    <!-- 1. KARTU PROFIL BIRO KEUANGAN -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-5 sm:p-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4 relative overflow-hidden">
        <div class="absolute -right-16 -top-16 w-56 h-56 rounded-full bg-emerald-50/70 pointer-events-none"></div>

        <div class="flex items-center space-x-4 relative z-10">
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-blue-700 via-blue-800 to-indigo-900 text-white flex items-center justify-center text-xl font-bold shadow-md shadow-blue-900/20 shrink-0">
                <i class="fa-solid fa-calculator text-emerald-400"></i>
            </div>
            <div>
                <div class="flex flex-wrap items-center gap-2">
                    <h1 class="text-xl font-extrabold text-slate-800 tracking-tight">{{ auth()->user()->name }}</h1>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                        Biro Keuangan & Administrasi Anggaran
                    </span>
                </div>
                <div class="flex flex-wrap items-center gap-x-2.5 gap-y-1 text-xs text-slate-500 mt-1">
                    <span class="flex items-center gap-1 font-medium text-slate-700">
                        <i class="fa-solid fa-building-columns text-slate-400"></i>
                        <span>Universitas Nurul Jadid (Pusat)</span>
                    </span>
                    <span class="text-slate-300">•</span>
                    <span class="font-medium text-slate-600">ID Petugas: {{ auth()->user()->identifier ?? 'KEU-01' }}</span>
                </div>
            </div>
        </div>

        <div class="flex items-center space-x-3 relative z-10">
            <a href="{{ route('keuangan.reports') }}"
                class="inline-flex items-center space-x-2 bg-gradient-to-r from-blue-700 to-indigo-800 hover:from-blue-800 hover:to-indigo-900 text-white font-bold px-4 py-2.5 rounded-xl text-xs transition shadow-sm hover:shadow">
                <i class="fa-solid fa-chart-pie text-emerald-300"></i>
                <span>Laporan Realisasi Anggaran</span>
                <i class="fa-solid fa-chevron-right text-[10px]"></i>
            </a>
        </div>
    </div>

    <!-- 2. KARTU STATISTIK (4 KARTU METRIK DATABASE AKTUAL) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Kartu 1: Menunggu Verifikasi Anggaran -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-4 sm:p-5 flex items-center justify-between transition hover:shadow-sm">
            <div>
                <span class="text-xs font-semibold text-slate-500 block">Menunggu Verifikasi</span>
                <span class="text-3xl font-black text-slate-800 mt-1 block leading-tight">{{ $totalPendingVerification }}</span>
                <span class="text-[11px] text-amber-600 font-medium mt-0.5 block flex items-center gap-1">
                    <i class="fa-solid fa-clock"></i> Proposal dari Dekan
                </span>
            </div>
            <div class="w-11 h-11 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-lg shrink-0 border border-amber-100">
                <i class="fa-solid fa-file-invoice-dollar"></i>
            </div>
        </div>

        <!-- Kartu 2: Menunggu Persetujuan Warek II -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-4 sm:p-5 flex items-center justify-between transition hover:shadow-sm">
            <div>
                <span class="text-xs font-semibold text-slate-500 block">Menunggu Warek II</span>
                <span class="text-3xl font-black text-slate-800 mt-1 block leading-tight">{{ $totalPendingWarek }}</span>
                <span class="text-[11px] text-blue-600 font-medium mt-0.5 block flex items-center gap-1">
                    <i class="fa-solid fa-hourglass-half"></i> Tahap penetapan final
                </span>
            </div>
            <div class="w-11 h-11 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-lg shrink-0 border border-blue-100">
                <i class="fa-solid fa-crown"></i>
            </div>
        </div>

        <!-- Kartu 3: Menunggu Pencairan Dana -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-4 sm:p-5 flex items-center justify-between transition hover:shadow-sm">
            <div>
                <span class="text-xs font-semibold text-slate-500 block">Siap Dicairkan</span>
                <span class="text-3xl font-black text-slate-800 mt-1 block leading-tight">{{ $totalPendingDisbursement }}</span>
                <span class="text-[11px] text-sky-600 font-medium mt-0.5 block flex items-center gap-1">
                    <i class="fa-solid fa-money-bill-transfer"></i> Disetujui Warek II
                </span>
            </div>
            <div class="w-11 h-11 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center text-lg shrink-0 border border-sky-100">
                <i class="fa-solid fa-money-check-dollar"></i>
            </div>
        </div>

        <!-- Kartu 4: Total Dana Telah Dicairkan -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-4 sm:p-5 flex items-center justify-between transition hover:shadow-sm">
            <div>
                <span class="text-xs font-semibold text-slate-500 block">Total Dicairkan</span>
                <span class="text-2xl font-black text-emerald-700 mt-1 block leading-tight">Rp {{ number_format($totalDisbursed, 0, ',', '.') }}</span>
                <span class="text-[11px] text-emerald-600 font-medium mt-0.5 block flex items-center gap-1">
                    <i class="fa-solid fa-circle-check"></i> Realisasi Kas
                </span>
            </div>
            <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg shrink-0 border border-emerald-100">
                <i class="fa-solid fa-wallet"></i>
            </div>
        </div>
    </div>

    <!-- 3. ANTREAN VERIFIKASI ANGGARAN (PROPOSAL SETELAH DEKAN) -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="flex items-center space-x-3">
                <div class="w-2.5 h-6 bg-amber-500 rounded-full"></div>
                <div>
                    <h2 class="text-base font-extrabold text-slate-800">Antrean Verifikasi Anggaran</h2>
                    <p class="text-xs text-slate-500">Proposal yang telah disahkan Dekan Fakultas dan memerlukan telaah administrasi anggaran & RAB.</p>
                </div>
            </div>
            <span class="px-3 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200 self-start sm:self-auto">
                <i class="fa-solid fa-circle-exclamation mr-1"></i> {{ $totalPendingVerification }} Pengajuan Menunggu
            </span>
        </div>

        @if($verificationQueue->isEmpty())
            <div class="p-12 text-center">
                <div class="w-14 h-14 mx-auto rounded-2xl bg-amber-50 text-amber-500 flex items-center justify-center text-xl mb-3">
                    <i class="fa-solid fa-clipboard-check"></i>
                </div>
                <h3 class="text-sm font-bold text-slate-700">Tidak Ada Antrean Verifikasi Anggaran</h3>
                <p class="text-xs text-slate-400 mt-1">Seluruh proposal dari fakultas telah ditinjau atau belum ada proposal baru.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-slate-50/70 border-b border-slate-200 text-slate-500 font-bold uppercase tracking-wider text-[11px]">
                            <th class="py-3 px-4">Kode & Tanggal</th>
                            <th class="py-3 px-4">Fakultas / Prodi</th>
                            <th class="py-3 px-4">Ketua & Anggota Tim</th>
                            <th class="py-3 px-4">Nama Perlombaan</th>
                            <th class="py-3 px-4 text-right">Usulan RAB</th>
                            <th class="py-3 px-4 text-right">Plafon Dekan</th>
                            <th class="py-3 px-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($verificationQueue as $item)
                            <tr class="hover:bg-slate-50/60 transition">
                                <td class="py-3.5 px-4">
                                    <span class="font-extrabold text-blue-900 block">{{ $item->code }}</span>
                                    <span class="text-[11px] text-slate-400 block">{{ $item->created_at ? $item->created_at->format('d M Y') : '-' }}</span>
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="font-bold text-slate-700 block">{{ $item->fakultas_id }}</span>
                                    <span class="text-[11px] text-slate-500 block">{{ $item->study_program_name ?? $item->study_program_id }}</span>
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="font-bold text-slate-800 block">{{ $item->submitter_name }}</span>
                                    <span class="text-[11px] text-slate-500 block">NIM: {{ $item->submitter_nim }}</span>
                                    @if($item->members->count() > 0)
                                        <span class="text-[10px] text-blue-600 block mt-0.5">+{{ $item->members->count() }} Anggota</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 max-w-xs">
                                    <span class="font-bold text-slate-800 block truncate">{{ $item->competitionDetail?->name ?? $item->title }}</span>
                                    <span class="inline-block mt-1 px-2 py-0.5 rounded text-[10px] font-semibold bg-blue-50 text-blue-700">
                                        {{ $item->competitionDetail?->level ?? 'Nasional' }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-right font-medium text-slate-600">
                                    Rp {{ number_format($item->total_budget_requested, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-4 text-right">
                                    <span class="font-extrabold text-emerald-700">Rp {{ number_format($item->total_budget_approved ?: $item->total_budget_requested, 0, ',', '.') }}</span>
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    <a href="{{ route('keuangan.verify.show', $item) }}"
                                        class="inline-flex items-center space-x-1.5 px-3 py-1.5 rounded-lg text-xs font-bold bg-blue-700 hover:bg-blue-800 text-white shadow-xs transition">
                                        <i class="fa-solid fa-magnifying-glass-dollar"></i>
                                        <span>Periksa Anggaran</span>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <!-- 4. MENU PENCAIRAN DANA (SETELAH PERSETUJUAN WAREK II) -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="flex items-center space-x-3">
                <div class="w-2.5 h-6 bg-emerald-500 rounded-full"></div>
                <div>
                    <h2 class="text-base font-extrabold text-slate-800">Antrean Pencairan Dana (Approved Warek II)</h2>
                    <p class="text-xs text-slate-500">Proposal yang telah memperoleh persetujuan final Warek II dan siap dicairkan melalui transfer bank.</p>
                </div>
            </div>
            <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 self-start sm:self-auto">
                <i class="fa-solid fa-money-bill-wave mr-1"></i> {{ $totalPendingDisbursement }} Siap Dicairkan
            </span>
        </div>

        @if($disbursementQueue->isEmpty())
            <div class="p-12 text-center">
                <div class="w-14 h-14 mx-auto rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl mb-3">
                    <i class="fa-solid fa-wallet"></i>
                </div>
                <h3 class="text-sm font-bold text-slate-700">Tidak Ada Antrean Pencairan Dana</h3>
                <p class="text-xs text-slate-400 mt-1">Belum ada pengajuan baru yang disahkan oleh Warek II atau seluruhnya telah dicairkan.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-slate-50/70 border-b border-slate-200 text-slate-500 font-bold uppercase tracking-wider text-[11px]">
                            <th class="py-3 px-4">Kode Pengajuan</th>
                            <th class="py-3 px-4">Ketua Delegasi</th>
                            <th class="py-3 px-4">Nama Lomba</th>
                            <th class="py-3 px-4">Fakultas</th>
                            <th class="py-3 px-4 text-right">Dana Disetujui</th>
                            <th class="py-3 px-4 text-center">Aksi Pencairan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($disbursementQueue as $item)
                            <tr class="hover:bg-slate-50/60 transition">
                                <td class="py-3.5 px-4 font-extrabold text-blue-900">{{ $item->code }}</td>
                                <td class="py-3.5 px-4">
                                    <span class="font-bold text-slate-800 block">{{ $item->submitter_name }}</span>
                                    <span class="text-[11px] text-slate-500">NIM: {{ $item->submitter_nim }}</span>
                                </td>
                                <td class="py-3.5 px-4 font-medium text-slate-700">{{ $item->competitionDetail?->name ?? $item->title }}</td>
                                <td class="py-3.5 px-4 font-bold text-slate-600">{{ $item->fakultas_id }}</td>
                                <td class="py-3.5 px-4 text-right font-black text-emerald-700 text-sm">
                                    Rp {{ number_format($item->total_budget_approved, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    <button type="button"
                                        onclick="openDisbursementModal('{{ $item->id }}', '{{ $item->code }}', '{{ $item->submitter_name }}', '{{ $item->total_budget_approved }}')"
                                        class="inline-flex items-center space-x-1.5 px-3 py-1.5 rounded-lg text-xs font-bold bg-emerald-600 hover:bg-emerald-700 text-white shadow-xs transition">
                                        <i class="fa-solid fa-paper-plane"></i>
                                        <span>Proses Pencairan</span>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <!-- 5. ANTREAN VERIFIKASI LPJ & KUITANSI KEUANGAN -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="flex items-center space-x-3">
                <div class="w-2.5 h-6 bg-blue-600 rounded-full"></div>
                <div>
                    <h2 class="text-base font-extrabold text-slate-800">Verifikasi LPJ & Pertanggungjawaban Keuangan</h2>
                    <p class="text-xs text-slate-500">Pemeriksaan nota, kuitansi transaksi, dan kesesuaian realisasi anggaran pasca-kegiatan lomba mahasiswa.</p>
                </div>
            </div>
            <span class="px-3 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200 self-start sm:self-auto">
                <i class="fa-solid fa-file-shield mr-1"></i> {{ $lpjQueue->count() }} Berkas LPJ
            </span>
        </div>

        @if($lpjQueue->isEmpty())
            <div class="p-12 text-center">
                <div class="w-14 h-14 mx-auto rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl mb-3">
                    <i class="fa-solid fa-receipt"></i>
                </div>
                <h3 class="text-sm font-bold text-slate-700">Tidak Ada Berkas LPJ Menunggu Pemeriksaan</h3>
                <p class="text-xs text-slate-400 mt-1">Seluruh laporan pertanggungjawaban telah selesai diaudit atau belum ada pengunggahan baru.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-slate-50/70 border-b border-slate-200 text-slate-500 font-bold uppercase tracking-wider text-[11px]">
                            <th class="py-3 px-4">Kode & Lomba</th>
                            <th class="py-3 px-4">Ketua Delegasi</th>
                            <th class="py-3 px-4 text-right">Dana Disetujui</th>
                            <th class="py-3 px-4">Dokumen LPJ</th>
                            <th class="py-3 px-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($lpjQueue as $item)
                            <tr class="hover:bg-slate-50/60 transition">
                                <td class="py-3.5 px-4">
                                    <span class="font-extrabold text-blue-900 block">{{ $item->code }}</span>
                                    <span class="text-[11px] text-slate-500 block">{{ $item->competitionDetail?->name ?? $item->title }}</span>
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="font-bold text-slate-800 block">{{ $item->submitter_name }}</span>
                                    <span class="text-[11px] text-slate-500">NIM: {{ $item->submitter_nim }}</span>
                                </td>
                                <td class="py-3.5 px-4 text-right font-black text-slate-700">
                                    Rp {{ number_format($item->total_budget_approved, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <i class="fa-solid fa-file-lines mr-1"></i> Terunggah
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    <a href="{{ route('keuangan.lpj.show', $item) }}"
                                        class="inline-flex items-center space-x-1 px-3 py-1.5 rounded-lg text-xs font-bold bg-blue-700 hover:bg-blue-800 text-white shadow-xs transition">
                                        <i class="fa-solid fa-stamp"></i>
                                        <span>Audit LPJ</span>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

</div>

<!-- MODAL PROSES PENCAIRAN DANA (MODERN POPUP) -->
<div id="disbursementModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs hidden p-4">
    <div class="bg-white rounded-2xl shadow-2xl border border-slate-200 max-w-lg w-full overflow-hidden transform transition-all">
        <div class="bg-gradient-to-r from-blue-900 via-blue-800 to-indigo-900 p-5 text-white flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-500/20 text-emerald-300 flex items-center justify-center font-bold text-lg border border-emerald-400/30">
                    <i class="fa-solid fa-money-bill-transfer"></i>
                </div>
                <div>
                    <h3 class="text-base font-extrabold tracking-tight">Proses Pencairan Dana Mahasiswa</h3>
                    <p class="text-xs text-blue-200" id="modalSubCode">SIPRES-XXXX</p>
                </div>
            </div>
            <button type="button" onclick="closeDisbursementModal()" class="text-blue-200 hover:text-white p-1 rounded-lg">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <form id="disbursementForm" method="POST" enctype="multipart/form-data" class="p-5 space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Penerima Dana (Ketua Tim)</label>
                <input type="text" id="modalRecipient" readonly class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 font-semibold text-slate-700 cursor-not-allowed">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Nominal Pencairan (Rp) <span class="text-red-500">*</span></label>
                    <input type="number" name="disbursed_amount" id="modalAmount" required min="1"
                        class="w-full text-xs border border-slate-300 rounded-xl px-3 py-2 font-black text-emerald-700 focus:ring-2 focus:ring-blue-600">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Tanggal Transfer <span class="text-red-500">*</span></label>
                    <input type="date" name="disbursed_at" required value="{{ date('Y-m-d') }}"
                        class="w-full text-xs border border-slate-300 rounded-xl px-3 py-2 focus:ring-2 focus:ring-blue-600">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Nomor Referensi Bank / No. Bukti Transaksi</label>
                <input type="text" name="transaction_reference" placeholder="Contoh: TRF-BSI-98472891"
                    class="w-full text-xs border border-slate-300 rounded-xl px-3 py-2 focus:ring-2 focus:ring-blue-600">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Unggah Bukti Transfer / Resi (PDF / Foto) <span class="text-slate-400 font-normal">(Opsional)</span></label>
                <input type="file" name="proof_file" accept=".pdf,.jpg,.jpeg,.png"
                    class="w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 border border-slate-200 rounded-xl">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Catatan Pencairan</label>
                <textarea name="notes" rows="2" placeholder="Keterangan tambahan untuk mahasiswa atau audit internal..."
                    class="w-full text-xs border border-slate-300 rounded-xl px-3 py-2 focus:ring-2 focus:ring-blue-600"></textarea>
            </div>

            <div class="flex items-center justify-end space-x-2 pt-2 border-t border-slate-100">
                <button type="button" onclick="closeDisbursementModal()" class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-100">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 rounded-xl text-xs font-bold bg-emerald-600 hover:bg-emerald-700 text-white shadow-sm hover:shadow transition flex items-center space-x-1.5">
                    <i class="fa-solid fa-check-circle"></i>
                    <span>Konfirmasi & Simpan Pencairan</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openDisbursementModal(id, code, recipient, amount) {
        const form = document.getElementById('disbursementForm');
        form.action = `/keuangan/pencairan/${id}`;
        document.getElementById('modalSubCode').textContent = code;
        document.getElementById('modalRecipient').value = recipient;
        document.getElementById('modalAmount').value = amount;
        document.getElementById('disbursementModal').classList.remove('hidden');
    }

    function closeDisbursementModal() {
        document.getElementById('disbursementModal').classList.add('hidden');
    }
</script>
@endsection

