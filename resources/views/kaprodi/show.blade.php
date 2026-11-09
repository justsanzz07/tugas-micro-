@extends('layouts.app')

@section('title', 'Verifikasi Proposal & Dosen Pendamping — ' . $submission->code)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Breadcrumb & Header -->
    <div class="flex items-center space-x-2 text-xs text-slate-500">
        <a href="{{ route('kaprodi.dashboard') }}" class="hover:text-blue-700 font-medium">Dashboard Kaprodi</a>
        <span>/</span>
        <span class="text-slate-800 font-semibold">Verifikasi Proposal & Dosen Pendamping [{{ $submission->code }}]</span>
    </div>

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">{{ $submission->competitionDetail?->competition_name ?? $submission->title }}</h1>
            <p class="text-xs text-slate-500 mt-0.5">Ketua Pengusul: <strong class="text-slate-700">{{ $submission->submitter_name ?? $submission->submitter_nim }}</strong> &bull; Penyelenggara: {{ $submission->competitionDetail?->organizer ?? '-' }}</p>
        </div>
        <div>
            <span class="px-3.5 py-1.5 rounded-full text-xs font-bold bg-blue-100 text-blue-900 border border-blue-200 shadow-sm inline-flex items-center space-x-1.5">
                <i class="fa-solid fa-circle-notch text-[10px] text-blue-600"></i>
                <span>Status: {{ $submission->current_status?->label() }}</span>
            </span>
        </div>
    </div>

    <!-- DETAIL LOMBA & PROPOSAL MAHASISWA -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 space-y-5">
        <h3 class="text-sm font-bold text-slate-900 border-b border-slate-100 pb-3 flex items-center">
            <i class="fa-solid fa-file-pdf text-rose-500 mr-2 text-base"></i>
            Proposal & Detail Delegasi Mahasiswa
        </h3>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
            <div class="p-3 bg-slate-50/70 rounded-xl border border-slate-100">
                <span class="text-slate-400 block text-[10px] uppercase font-bold tracking-wider">Tingkat & Kategori Lomba</span>
                <span class="font-bold text-slate-800 mt-0.5 block">{{ $submission->competitionDetail?->level?->label() ?? 'REGIONAL' }} &bull; {{ $submission->competitionDetail?->category ?? '-' }}</span>
            </div>
            <div class="p-3 bg-slate-50/70 rounded-xl border border-slate-100">
                <span class="text-slate-400 block text-[10px] uppercase font-bold tracking-wider">Jadwal Pelaksanaan</span>
                <span class="font-bold text-slate-800 mt-0.5 block">
                    {{ $submission->competitionDetail?->start_date?->format('d M Y') ?? '-' }} s/d {{ $submission->competitionDetail?->end_date?->format('d M Y') ?? '-' }}
                </span>
            </div>
            <div class="p-3 bg-slate-50/70 rounded-xl border border-slate-100">
                <span class="text-slate-400 block text-[10px] uppercase font-bold tracking-wider">Lokasi Penyelenggaraan</span>
                <span class="font-bold text-slate-800 mt-0.5 block">{{ $submission->competitionDetail?->location ?? 'Online / Kampus Penyelenggara' }}</span>
            </div>
            <div class="p-3 bg-slate-50/70 rounded-xl border border-slate-100">
                <span class="text-slate-400 block text-[10px] uppercase font-bold tracking-wider">Susunan Anggota Tim</span>
                <div class="mt-1 space-y-0.5 font-semibold text-slate-700">
                    <div>1. {{ $submission->submitter_name ?? $submission->submitter_nim }} (Ketua)</div>
                    @foreach($submission->members as $member)
                        @if($member->nim !== $submission->submitter_nim)
                            <div>• {{ $member->name ?? $member->nim }} ({{ $member->nim }})</div>
                        @endif
                    @endforeach
                </div>
            </div>

            <!-- DOSEN PENDAMPING DENGAN FITUR EDIT / GANTI DOSEN -->
            <div class="sm:col-span-2 bg-gradient-to-br from-blue-50/60 to-slate-50 p-4 rounded-2xl border border-blue-200/80 shadow-sm space-y-3">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        <div class="w-7 h-7 rounded-lg bg-blue-600 text-white flex items-center justify-center font-bold text-xs shadow-sm">
                            <i class="fa-solid fa-chalkboard-user"></i>
                        </div>
                        <div>
                            <span class="text-xs font-bold text-slate-900 block">Dosen Pendamping Mahasiswa</span>
                            <span class="text-[10px] text-slate-500">Dosen pembina yang resmi ditunjuk oleh Kaprodi</span>
                        </div>
                    </div>

                    @if(!auth()->user()->isAdmin())
                        <button type="button" onclick="toggleEditDosen()" id="btnToggleEditDosen"
                            class="px-3.5 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl transition shadow-sm flex items-center space-x-1.5">
                            <i class="fa-solid fa-user-pen"></i>
                            <span>{{ $submission->supervisor_name ? 'Edit Dosen' : 'Tetapkan Dosen' }}</span>
                        </button>
                    @endif
                </div>

                <!-- Tampilan Normal Dosen Saat Ini -->
                <div id="dosenCurrentView" class="bg-white p-3.5 rounded-xl border border-blue-100 shadow-sm">
                    @if($submission->supervisor_name)
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3 text-slate-800">
                                <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center text-lg font-bold">
                                    <i class="fa-solid fa-user-tie"></i>
                                </div>
                                <div>
                                    <span class="font-extrabold text-sm block text-slate-900">{{ $submission->supervisor_name }}</span>
                                    <span class="text-xs text-blue-800 font-mono font-semibold">NIDN: {{ $submission->supervisor_nidn }}</span>
                                </div>
                            </div>
                            <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                <i class="fa-solid fa-circle-check mr-1"></i> Ditugaskan
                            </span>
                        </div>
                    @else
                        <div class="flex items-center space-x-2.5 text-amber-900 bg-amber-50/70 p-3 rounded-lg border border-amber-200 text-xs">
                            <i class="fa-solid fa-triangle-exclamation text-amber-600 text-base shrink-0"></i>
                            <div>
                                <span class="font-bold block">Belum Ada Dosen Pendamping yang Ditugaskan</span>
                                <span class="text-[11px] text-amber-700">Klik tombol <strong>"Tetapkan Dosen"</strong> di atas atau tentukan pada formulir keputusan di bawah.</span>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- FORM EDIT DOSEN PENDAMPING (POP-DOWN) -->
                @if(!auth()->user()->isAdmin())
                    <form id="dosenEditForm" action="{{ route('kaprodi.updateSupervisor', $submission->id) }}" method="POST"
                        class="hidden bg-white p-4 rounded-xl border-2 border-blue-400 shadow-md space-y-3 transition-all">
                        @csrf
                        <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                            <h4 class="text-xs font-bold text-blue-900 flex items-center space-x-1.5">
                                <i class="fa-solid fa-user-pen text-blue-600"></i>
                                <span>Formulir Edit & Penetapan Dosen Pendamping</span>
                            </h4>
                            <button type="button" onclick="toggleEditDosen()" class="text-slate-400 hover:text-slate-600 text-xs font-medium">
                                <i class="fa-solid fa-xmark"></i> Tutup
                            </button>
                        </div>

                        <div>
                            <label class="block text-[11px] font-semibold text-slate-700 mb-1">Pilih Dosen dari Program Studi</label>
                            <select id="quickDosenSelect" class="w-full text-xs px-3 py-2 border border-slate-300 rounded-xl bg-white focus:ring-2 focus:ring-blue-500 outline-none">
                                <option value="">-- Pilih Dosen Pendamping Program Studi --</option>
                                @foreach($dosenList as $dosen)
                                    <option value="{{ $dosen['nidn'] }}" data-name="{{ $dosen['nama'] }}"
                                        {{ ($submission->supervisor_nidn == $dosen['nidn']) ? 'selected' : '' }}>
                                        {{ $dosen['nama'] }} (NIDN: {{ $dosen['nidn'] }})
                                    </option>
                                @endforeach
                                <option value="custom">+ Input Dosen Manual Lainnya</option>
                            </select>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-[11px] font-semibold text-slate-700 mb-1">Nama Lengkap Dosen <span class="text-rose-500">*</span></label>
                                <input type="text" name="supervisor_name" id="quickSupervisorName" value="{{ old('supervisor_name', $submission->supervisor_name) }}" required
                                    class="w-full text-xs px-3 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none font-semibold text-slate-800"
                                    placeholder="Contoh: Dr. Muhammad Ilham, M.Kom">
                            </div>
                            <div>
                                <label class="block text-[11px] font-semibold text-slate-700 mb-1">NIDN Dosen <span class="text-rose-500">*</span></label>
                                <input type="text" name="supervisor_nidn" id="quickSupervisorNidn" value="{{ old('supervisor_nidn', $submission->supervisor_nidn) }}" required
                                    class="w-full text-xs px-3 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none font-semibold text-slate-800"
                                    placeholder="Contoh: 0715088901">
                            </div>
                        </div>

                        <div>
                            <label class="block text-[11px] font-semibold text-slate-700 mb-1">Catatan Pengalihan / Penunjukan (Opsional)</label>
                            <input type="text" name="notes" class="w-full text-xs px-3 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none"
                                placeholder="Contoh: Disesuaikan dengan bidang kepakaran karya tulis mahasiswa...">
                        </div>

                        <div class="flex items-center justify-end space-x-2 pt-2 border-t border-slate-100">
                            <button type="button" onclick="toggleEditDosen()" class="px-3.5 py-1.5 rounded-xl border border-slate-200 text-slate-600 text-xs font-semibold hover:bg-slate-50 transition">
                                Batal
                            </button>
                            <button type="submit" class="px-5 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold transition shadow-md flex items-center space-x-1.5">
                                <i class="fa-solid fa-check"></i>
                                <span>Simpan Perubahan Dosen</span>
                            </button>
                        </div>
                    </form>
                @endif
            </div>

            <!-- Berkas Dokumen Proposal Mahasiswa -->
            <div class="sm:col-span-2 bg-gradient-to-br from-slate-50 to-blue-50/40 p-4 rounded-2xl border border-blue-200/80 shadow-sm space-y-3">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                    <div class="flex items-center space-x-2">
                        <div class="w-7 h-7 rounded-lg bg-rose-600 text-white flex items-center justify-center font-bold text-xs shadow-sm">
                            <i class="fa-solid fa-file-pdf"></i>
                        </div>
                        <div>
                            <span class="text-xs font-bold text-slate-900 block">Dokumen Proposal Kegiatan Mahasiswa</span>
                            <span class="text-[10px] text-slate-500">Berkas usulan resmi yang dikirimkan oleh tim mahasiswa untuk diverifikasi Kaprodi</span>
                        </div>
                    </div>

                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-blue-100 text-blue-800 border border-blue-200 w-fit">
                        <i class="fa-solid fa-circle-check text-blue-600 mr-1.5"></i> Berkas Tersedia & Siap Ditinjau
                    </span>
                </div>

                <div class="bg-white p-3.5 rounded-xl border border-blue-100 shadow-sm flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                    <div class="flex items-center space-x-3 text-slate-800 min-w-0">
                        <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center text-xl shrink-0 border border-rose-200">
                            <i class="fa-solid fa-file-pdf"></i>
                        </div>
                        <div class="truncate">
                            <div class="font-extrabold text-sm text-slate-900 truncate">
                                {{ $submission->proposal_path ? basename($submission->proposal_path) : 'Proposal-' . $submission->code . '-' . \Illuminate\Support\Str::slug($submission->competitionDetail?->competition_name ?? $submission->title) . '.pdf' }}
                            </div>
                            <div class="text-[11px] text-slate-400 mt-0.5">
                                Format: PDF Dokumen Resmi &bull; Pengusul: <strong class="text-slate-700">{{ $submission->submitter_name ?? $submission->mahasiswa_nim }}</strong>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons: Preview Modal, Buka Tab Baru, Unduh -->
                    <div class="flex items-center space-x-2 shrink-0">
                        <button type="button" onclick="openProposalModal()"
                            class="px-3.5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold transition shadow-sm flex items-center space-x-1.5">
                            <i class="fa-solid fa-eye"></i>
                            <span>Buka & Baca Proposal</span>
                        </button>
                        <a href="{{ route('kaprodi.proposal', $submission->id) }}" target="_blank"
                            class="p-2 bg-slate-100 hover:bg-blue-50 text-slate-700 hover:text-blue-800 rounded-xl text-xs font-bold border border-slate-200 transition"
                            title="Buka di Tab Baru">
                            <i class="fa-solid fa-arrow-up-right-from-square"></i>
                        </a>
                        <a href="{{ route('kaprodi.proposal', ['submission' => $submission->id, 'download' => 1]) }}"
                            class="p-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold border border-slate-200 transition"
                            title="Unduh Berkas PDF">
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
            <h3 class="text-sm font-bold text-slate-900 flex items-center">
                <i class="fa-solid fa-money-bill-wave text-blue-600 mr-2"></i>
                Rincian Usulan Anggaran (RAB) Mahasiswa
            </h3>
            <span class="text-xs font-bold text-blue-900 bg-blue-50 px-3 py-1 rounded-lg border border-blue-200">
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

    <!-- FORM KEPUTUSAN KAPRODI & PENUNJUKAN DOSEN PENDAMPING -->
    @if(auth()->user()->isAdmin())
        <div class="bg-blue-50/70 border-2 border-blue-200 rounded-2xl p-6 text-blue-950 flex items-start space-x-3 shadow-sm">
            <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-sm shrink-0">
                <i class="fa-solid fa-eye"></i>
            </div>
            <div>
                <h3 class="text-sm font-bold text-slate-800">Mode Peninjauan Administrator (Read-Only)</h3>
                <p class="text-xs text-blue-800 mt-1 leading-relaxed">
                    Anda sedang meninjau berkas pengajuan ini sebagai Administrator Sistem. Formulir penunjukan dosen pendamping dan pengambilan keputusan persetujuan hanya dapat dieksekusi oleh Ketua Program Studi terkait.
                </p>
            </div>
        </div>
    @elseif(in_array($submission->current_status?->value, ['REVIEW_KAPRODI', 'REVIEW_DOSEN', 'SUBMITTED']))
        <div class="bg-white rounded-2xl border-2 border-blue-300 shadow-md p-6 space-y-5">
            <div class="flex items-center space-x-2.5 border-b border-slate-100 pb-3">
                <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-sm">
                    <i class="fa-solid fa-gavel"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Formulir Keputusan Kaprodi & Penunjukan Dosen Pendamping</h3>
                    <p class="text-[11px] text-slate-500">Pilih dosen pendamping yang kompeten dan tetapkan keputusan persetujuan proposal.</p>
                </div>
            </div>

            <form action="{{ route('kaprodi.process', $submission->id) }}" method="POST" id="formKaprodi" class="space-y-4">
                @csrf

                <!-- SELEKSI DOSEN PENDAMPING OLEH KAPRODI -->
                <div class="bg-blue-50/60 p-4 rounded-xl border border-blue-200 space-y-3">
                    <div class="flex items-center justify-between">
                        <label class="block text-xs font-bold text-blue-950">
                            <i class="fa-solid fa-chalkboard-user text-blue-700 mr-1.5"></i>
                            Pilih Dosen Pendamping Mahasiswa <span class="text-rose-500">*</span>
                        </label>
                        <span class="text-[10px] text-blue-700 font-semibold bg-blue-100 px-2 py-0.5 rounded">Wajib Dipilih Kaprodi</span>
                    </div>
                    
                    <p class="text-[11px] text-blue-900 leading-relaxed">
                        Sebagai Kaprodi, tentukan Dosen yang bertugas mendampingi tim mahasiswa ini. Nama dan NIDN dosen yang dipilih akan otomatis tercantum pada Surat Tugas dan berkas persetujuan Dekan.
                    </p>

                    <div>
                        <select id="dosenSelect" class="w-full text-xs px-3 py-2.5 border border-blue-300 rounded-xl bg-white focus:ring-2 focus:ring-blue-500 outline-none">
                            <option value="">-- Pilih Dosen Pendamping Program Studi --</option>
                            @foreach($dosenList as $dosen)
                                <option value="{{ $dosen['nidn'] }}" data-name="{{ $dosen['nama'] }}"
                                    {{ (old('supervisor_nidn', $submission->supervisor_nidn) == $dosen['nidn']) ? 'selected' : '' }}>
                                    {{ $dosen['nama'] }} (NIDN: {{ $dosen['nidn'] }})
                                </option>
                            @endforeach
                            <option value="custom" {{ old('supervisor_nidn') && !collect($dosenList)->pluck('nidn')->contains(old('supervisor_nidn')) ? 'selected' : '' }}>
                                + Input Dosen Pendamping Lainnya (Manual)
                            </option>
                        </select>
                    </div>

                    <!-- Hidden / Input fields for Supervisor Name & NIDN -->
                    <div id="customDosenWrapper" class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-700 mb-1">Nama Lengkap Dosen Pendamping <span class="text-rose-500">*</span></label>
                            <input type="text" name="supervisor_name" id="supervisorName" value="{{ old('supervisor_name', $submission->supervisor_name) }}"
                                class="w-full text-xs px-3 py-2 border border-slate-300 rounded-xl bg-white focus:ring-2 focus:ring-blue-500 outline-none font-semibold text-slate-800"
                                placeholder="Contoh: Dr. Muhammad Ilham, M.Kom">
                        </div>
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-700 mb-1">NIDN Dosen Pendamping <span class="text-rose-500">*</span></label>
                            <input type="text" name="supervisor_nidn" id="supervisorNidn" value="{{ old('supervisor_nidn', $submission->supervisor_nidn) }}"
                                class="w-full text-xs px-3 py-2 border border-slate-300 rounded-xl bg-white focus:ring-2 focus:ring-blue-500 outline-none font-semibold text-slate-800"
                                placeholder="Contoh: 0715088901">
                        </div>
                    </div>
                </div>

                <!-- CATATAN EVALUASI KAPRODI -->
                <div class="text-xs">
                    <label class="block font-semibold text-slate-700 mb-1">Catatan Evaluasi / Rekomendasi Kaprodi</label>
                    <textarea name="notes" rows="3" class="w-full px-3 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none"
                        placeholder="Berikan masukan proposal, catatan teknis pembimbingan, atau alasan keputusan..."></textarea>
                </div>

                <!-- TOMBOL KEPUTUSAN 3 PILIHAN (REJECT, REVISE, APPROVE) -->
                <div class="flex flex-col sm:flex-row items-center justify-end gap-3 pt-3 border-t border-slate-100">
                    <!-- Reject -->
                    <button type="submit" name="action" value="REJECT" onclick="return confirm('Apakah Anda yakin ingin MENOLAK proposal lomba mahasiswa ini?')"
                        class="w-full sm:w-auto px-4 py-2.5 rounded-xl border border-rose-300 text-rose-700 hover:bg-rose-50 font-bold text-xs transition flex items-center justify-center space-x-1.5">
                        <i class="fa-solid fa-xmark"></i>
                        <span>Tolak Proposal</span>
                    </button>

                    <!-- Revise -->
                    <button type="submit" name="action" value="REVISE"
                        class="w-full sm:w-auto px-4 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-bold text-xs transition flex items-center justify-center space-x-1.5 shadow">
                        <i class="fa-solid fa-rotate-left"></i>
                        <span>Minta Mahasiswa Revisi</span>
                    </button>

                    <!-- Approve -->
                    <button type="submit" name="action" value="APPROVE" id="btnApprove"
                        class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs transition flex items-center justify-center space-x-1.5 shadow-md">
                        <i class="fa-solid fa-check-circle"></i>
                        <span>Setujui & Tetapkan Dosen Pendamping</span>
                    </button>
                </div>
            </form>
        </div>
    @endif

    <!-- MODAL POPUP PREVIEW PROPOSAL INTERAKTIF -->
    <div id="proposalModal" class="fixed inset-0 z-50 hidden bg-slate-950/70 backdrop-blur-sm flex items-center justify-center p-3 sm:p-6 transition-all duration-300">
        <div class="bg-white rounded-2xl w-full max-w-5xl h-[90vh] flex flex-col shadow-2xl overflow-hidden border border-slate-300">
            <!-- Modal Header -->
            <div class="px-5 py-3.5 bg-gradient-to-r from-blue-900 via-blue-800 to-indigo-900 text-white flex items-center justify-between shadow">
                <div class="flex items-center space-x-2.5">
                    <div class="w-8 h-8 rounded-lg bg-blue-700/60 flex items-center justify-center text-rose-300 text-sm">
                        <i class="fa-solid fa-file-pdf"></i>
                    </div>
                    <div>
                        <h3 class="text-xs font-black tracking-tight uppercase">Peninjauan Dokumen Proposal Mahasiswa</h3>
                        <p class="text-[11px] text-blue-200 truncate max-w-md">
                            [{{ $submission->code }}] {{ $submission->competitionDetail?->competition_name ?? $submission->title }}
                        </p>
                    </div>
                </div>
                <div class="flex items-center space-x-2">
                    <a href="{{ route('kaprodi.proposal', $submission->id) }}" target="_blank" class="px-3 py-1.5 rounded-lg bg-white/10 hover:bg-white/20 text-white text-xs font-semibold transition flex items-center space-x-1" title="Buka di Tab Penuh">
                        <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
                        <span class="hidden sm:inline">Tab Penuh</span>
                    </a>
                    <a href="{{ route('kaprodi.proposal', ['submission' => $submission->id, 'download' => 1]) }}" class="px-3 py-1.5 rounded-lg bg-white/10 hover:bg-white/20 text-white text-xs font-semibold transition flex items-center space-x-1" title="Unduh File PDF">
                        <i class="fa-solid fa-download text-[10px]"></i>
                        <span class="hidden sm:inline">Unduh</span>
                    </a>
                    <button type="button" onclick="closeProposalModal()" class="w-8 h-8 rounded-lg bg-white/10 hover:bg-rose-600 text-white flex items-center justify-center transition" title="Tutup Preview">
                        <i class="fa-solid fa-xmark text-sm"></i>
                    </button>
                </div>
            </div>

            <!-- Modal Body (Iframe) -->
            <div class="flex-1 bg-slate-100 relative">
                <div id="proposalLoading" class="absolute inset-0 flex flex-col items-center justify-center text-slate-500 text-xs bg-slate-50 z-10">
                    <i class="fa-solid fa-circle-notch fa-spin text-3xl text-blue-600 mb-2"></i>
                    <span>Memuat dokumen proposal mahasiswa...</span>
                </div>
                <iframe id="proposalIframe" data-src="{{ route('kaprodi.proposal', $submission->id) }}" class="w-full h-full border-0"></iframe>
            </div>

            <!-- Modal Footer -->
            <div class="px-5 py-2.5 bg-white border-t border-slate-200 flex items-center justify-between text-xs text-slate-500">
                <div class="flex items-center space-x-1.5">
                    <i class="fa-solid fa-shield-halved text-blue-600"></i>
                    <span>Verifikasi Akademik Program Studi &bull; Portal Kaprodi</span>
                </div>
                <button type="button" onclick="closeProposalModal()" class="px-4 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold transition">
                    Selesai Membaca & Tutup
                </button>
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script>
    function toggleEditDosen() {
        const form = document.getElementById('dosenEditForm');
        const view = document.getElementById('dosenCurrentView');
        if (form) {
            form.classList.toggle('hidden');
        }
    }

    function openProposalModal() {
        const modal = document.getElementById('proposalModal');
        const iframe = document.getElementById('proposalIframe');
        const loader = document.getElementById('proposalLoading');
        if (!iframe.src || iframe.src === 'about:blank' || iframe.src === window.location.href) {
            loader.style.display = 'flex';
            iframe.src = iframe.getAttribute('data-src');
            iframe.onload = function() {
                loader.style.display = 'none';
            };
        }
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeProposalModal() {
        const modal = document.getElementById('proposalModal');
        modal.classList.add('hidden');
        document.body.style.overflow = '';
    }

    // Keyboard ESC to close modal
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeProposalModal();
        }
    });

    document.addEventListener('DOMContentLoaded', function() {
        // Quick edit select
        const quickDosenSelect = document.getElementById('quickDosenSelect');
        const quickSupervisorName = document.getElementById('quickSupervisorName');
        const quickSupervisorNidn = document.getElementById('quickSupervisorNidn');

        if (quickDosenSelect) {
            quickDosenSelect.addEventListener('change', function() {
                const selectedOpt = this.options[this.selectedIndex];
                const nidn = this.value;

                if (nidn && nidn !== 'custom') {
                    quickSupervisorNidn.value = nidn;
                    quickSupervisorName.value = selectedOpt.getAttribute('data-name') || '';
                } else if (nidn === 'custom') {
                    quickSupervisorNidn.value = '';
                    quickSupervisorName.value = '';
                    quickSupervisorName.focus();
                }
            });
        }

        // Bottom approval form select
        const dosenSelect = document.getElementById('dosenSelect');
        const supervisorName = document.getElementById('supervisorName');
        const supervisorNidn = document.getElementById('supervisorNidn');

        if (dosenSelect) {
            dosenSelect.addEventListener('change', function() {
                const selectedOpt = this.options[this.selectedIndex];
                const nidn = this.value;

                if (nidn && nidn !== 'custom') {
                    supervisorNidn.value = nidn;
                    supervisorName.value = selectedOpt.getAttribute('data-name') || '';
                } else if (nidn === 'custom') {
                    supervisorNidn.value = '';
                    supervisorName.value = '';
                    supervisorName.focus();
                }
            });
        }

        const form = document.getElementById('formKaprodi');
        if (form) {
            form.addEventListener('submit', function(e) {
                const action = document.activeElement ? document.activeElement.value : '';
                if (action === 'APPROVE') {
                    if (!supervisorName.value.trim() || !supervisorNidn.value.trim()) {
                        e.preventDefault();
                        alert('Sebagai Kaprodi, Anda wajib memilih atau menginput Dosen Pendamping sebelum menyetujui pengajuan ini!');
                        dosenSelect.focus();
                    }
                }
            });
        }
    });
</script>
@endpush
@endsection
