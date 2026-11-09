@extends('layouts.app')

@section('title', 'Tracking Pengajuan — ' . $submission->code)

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

    <!-- Breadcrumb & Status Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2 text-xs text-slate-500 mb-1">
                <a href="{{ route('mahasiswa.dashboard') }}" class="hover:text-blue-700 font-medium">Dashboard</a>
                <span>/</span>
                <span class="text-slate-800 font-semibold">{{ $submission->code }}</span>
            </div>
            <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight">{{ $submission->competitionDetail?->competition_name }}</h1>
            <p class="text-xs text-slate-500">Penyelenggara: {{ $submission->competitionDetail?->organizer }} &bull; Tanggal Lomba: {{ $submission->competitionDetail?->start_date?->format('d M Y') }} - {{ $submission->competitionDetail?->end_date?->format('d M Y') }}</p>
        </div>

        <div>
            @php
                $statusEnum = $submission->current_status;
                $statusBadgeClass = match($statusEnum?->value) {
                    'DRAFT' => 'bg-slate-100 text-slate-700 border-slate-300',
                    'SUBMITTED', 'REVIEW_DOSEN', 'REVIEW_KAPRODI', 'REVIEW_DEKAN', 'REVIEW_LPJ' => 'bg-blue-50 text-blue-700 border-blue-300',
                    'REVISION_DOSEN', 'REVISION_KAPRODI', 'REVISION_DEKAN' => 'bg-amber-100 text-amber-800 border-amber-300',
                    'APPROVED_DOSEN', 'APPROVED_KAPRODI' => 'bg-sky-50 text-sky-700 border-sky-300',
                    'APPROVED_DEKAN', 'APPROVED_FINAL' => 'bg-blue-100 text-blue-800 border-blue-300 font-bold',
                    'WAITING_LPJ' => 'bg-orange-100 text-orange-800 border-orange-300 animate-pulse',
                    'COMPLETED' => 'bg-blue-200 text-blue-900 border-blue-400 font-extrabold',
                    'REJECTED' => 'bg-rose-100 text-rose-800 border-rose-300',
                    default => 'bg-slate-100 text-slate-700 border-slate-300',
                };
            @endphp
            <span class="inline-flex items-center px-3.5 py-1.5 rounded-full text-xs font-bold border {{ $statusBadgeClass }}">
                <span class="w-2 h-2 rounded-full bg-current mr-2"></span>
                Status: {{ $statusEnum?->label() }}
            </span>
        </div>
    </div>

    <!-- Alert Revisi dari Approver jika ada -->
    @if(in_array($submission->current_status?->value, ['REVISION_KAPRODI', 'REVISION_DOSEN', 'REVISION_DEKAN', 'REVISION_WAREK']))
        @php
            $latestStep = $flow?->currentStep();
            $latestLog = $latestStep?->logs()->latest()->first();
        @endphp
        <div class="bg-amber-50 border-2 border-amber-400 p-5 rounded-2xl shadow-sm space-y-3">
            <div class="flex items-center space-x-2 text-amber-900 font-bold text-sm">
                <i class="fa-solid fa-triangle-exclamation text-amber-600 text-lg"></i>
                <span>Catatan Revisi dari {{ $latestStep?->approver_role?->label() ?? 'Pemeriksa' }}</span>
            </div>
            <p class="text-xs text-amber-900 bg-white/70 p-3 rounded-xl border border-amber-200 leading-relaxed font-mono">
                "{{ $latestStep?->notes ?? $latestLog?->notes ?? 'Silakan lakukan penyesuaian dokumen dan RAB sesuai ketentuan.' }}"
            </p>
            <div class="flex items-center justify-end">
                <a href="{{ route('mahasiswa.submissions.edit', $submission->id) }}" class="inline-flex items-center space-x-1.5 bg-amber-600 hover:bg-amber-700 text-white font-bold px-4 py-2 rounded-xl text-xs transition shadow">
                    <i class="fa-solid fa-pen-to-square"></i>
                    <span>Buka Form Perbaikan & Kirim Ulang</span>
                </a>
            </div>
        </div>
    @endif

    <!-- STEPPER TIMELINE ALUR PERSETUJUAN BERJENJANG -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6">
        <h2 class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-6 flex items-center">
            <i class="fa-solid fa-route text-blue-600 mr-2"></i>
            Pelacakan Status Alur Persetujuan (Workflow State Machine)
        </h2>

        @php
            $statusVal = $submission->current_status?->value;
            $steps = [
                ['key' => 'SUBMITTED', 'label' => 'Mahasiswa', 'desc' => 'Pengajuan Dikirim', 'icon' => 'fa-paper-plane'],
                ['key' => 'KAPRODI', 'label' => 'Kaprodi', 'desc' => 'Penunjukan Dosen & Review', 'icon' => 'fa-user-tie'],
                ['key' => 'DEKAN', 'label' => 'Dekan Fakultas', 'desc' => 'Persetujuan Final & Dana', 'icon' => 'fa-building-columns'],
                ['key' => 'LPJ', 'label' => 'Pelaporan LPJ', 'desc' => 'Nota & Sertifikat', 'icon' => 'fa-file-invoice'],
                ['key' => 'COMPLETED', 'label' => 'Selesai & SKPI', 'desc' => 'Prestasi Disahkan', 'icon' => 'fa-trophy'],
            ];

            // Hitung active step index (0 to 4)
            $activeIndex = match($statusVal) {
                'DRAFT' => 0,
                'SUBMITTED', 'REVIEW_KAPRODI', 'REVIEW_DOSEN', 'REVISION_KAPRODI', 'REVISION_DOSEN' => 1,
                'APPROVED_KAPRODI', 'APPROVED_DOSEN', 'REVIEW_DEKAN', 'REVISION_DEKAN' => 2,
                'APPROVED_DEKAN', 'APPROVED_FINAL', 'WAITING_LPJ', 'REVIEW_LPJ' => 3,
                'COMPLETED' => 4,
                'REJECTED' => 1,
                default => 0,
            };
        @endphp

        <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
            @foreach($steps as $idx => $st)
                @php
                    $isPassed = $idx < $activeIndex;
                    $isCurrent = $idx === $activeIndex;
                    $isCompletedFinal = $statusVal === 'COMPLETED' && $idx === 4;
                @endphp
                <div class="p-3 rounded-xl border text-center transition {{ $isPassed ? 'bg-blue-50 border-blue-200 text-blue-900' : ($isCurrent ? 'bg-blue-600 border-blue-600 text-white shadow-md ring-2 ring-blue-300' : 'bg-slate-50 border-slate-200 text-slate-400') }}">
                    <div class="w-8 h-8 rounded-full mx-auto flex items-center justify-center text-xs mb-2 {{ $isPassed ? 'bg-blue-600 text-white' : ($isCurrent ? 'bg-white text-blue-600 font-extrabold ring-2 ring-blue-200' : 'bg-slate-200 text-slate-500') }}">
                        @if($isPassed)
                            <i class="fa-solid fa-check"></i>
                        @else
                            <i class="fa-solid {{ $st['icon'] }}"></i>
                        @endif
                    </div>
                    <div class="font-bold text-[11px] truncate {{ $isCurrent ? 'text-white' : '' }}">{{ $st['label'] }}</div>
                    <div class="text-[10px] truncate {{ $isCurrent ? 'text-blue-100' : 'text-slate-500' }}">{{ $st['desc'] }}</div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- DOKUMEN RESMI TERBIT: SURAT DISPENSASI & SURAT TUGAS -->
    @if($submission->officialLetters->isNotEmpty())
        <div class="bg-gradient-to-r from-blue-900 via-blue-800 to-indigo-900 rounded-2xl p-6 text-white shadow-md space-y-4">
            <div class="flex items-center space-x-2">
                <i class="fa-solid fa-stamp text-amber-400 text-xl"></i>
                <div>
                    <h3 class="text-sm font-bold">Dokumen Resmi Kampus Telah Diterbitkan</h3>
                    <p class="text-xs text-blue-200">Surat resmi telah ditandatangani secara digital & dilengkapi verifikasi QR Code.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                @foreach($submission->officialLetters as $letter)
                    <div class="bg-white/10 border border-white/20 p-3.5 rounded-xl flex items-center justify-between text-xs">
                        <div>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-white/20 text-white">
                                {{ $letter->letter_type->label() }}
                            </span>
                            <div class="font-bold text-white mt-1 text-sm">{{ $letter->letter_number }}</div>
                            <div class="text-[10px] text-blue-200">Penerbit: {{ $letter->issued_by_role->label() }} &bull; Token: {{ $letter->qr_code_token }}</div>
                        </div>
                        <a href="#" class="px-3 py-1.5 rounded-lg bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs transition flex items-center space-x-1 shadow">
                            <i class="fa-solid fa-download"></i>
                            <span>Unduh PDF</span>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        <!-- LEFT 2 COLS: RINCIAN PENGANJUAN & RAB -->
        <div class="md:col-span-2 space-y-6">

            <!-- Detail Lomba -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 space-y-4">
                <h3 class="text-sm font-bold text-slate-800 border-b border-slate-100 pb-3 flex items-center">
                    <i class="fa-solid fa-circle-info text-blue-600 mr-2"></i>
                    Detail Perlombaan & Proposal
                </h3>

                <div class="grid grid-cols-2 gap-4 text-xs">
                    <div>
                        <span class="text-slate-400 block text-[10px] uppercase font-semibold">Tingkat Lomba</span>
                        <span class="font-bold text-slate-800">{{ $submission->competitionDetail?->level?->label() }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block text-[10px] uppercase font-semibold">Kategori</span>
                        <span class="font-bold text-slate-800">{{ $submission->competitionDetail?->category }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block text-[10px] uppercase font-semibold">Lokasi Penyelenggaraan</span>
                        <span class="font-bold text-slate-800">{{ $submission->competitionDetail?->location }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block text-[10px] uppercase font-semibold">Tautan Website</span>
                        @if($submission->competitionDetail?->competition_url)
                            <a href="{{ $submission->competitionDetail->competition_url }}" target="_blank" class="text-blue-700 hover:underline font-bold truncate block">
                                <i class="fa-solid fa-arrow-up-right-from-square mr-1 text-[10px]"></i> Buka Web Lomba
                            </a>
                        @else
                            <span class="text-slate-400">-</span>
                        @endif
                    </div>
                    <div class="col-span-2">
                        <span class="text-slate-400 block text-[10px] uppercase font-semibold">Dokumen Proposal</span>
                        <div class="mt-1 flex items-center justify-between bg-slate-50 p-2.5 rounded-xl border border-slate-200">
                            <div class="flex items-center space-x-2 truncate">
                                <i class="fa-solid fa-file-pdf text-rose-500 text-lg"></i>
                                <span class="font-medium text-slate-800 truncate">
                                    {{ $submission->proposal_path ? basename($submission->proposal_path) : 'Proposal-' . $submission->code . '.pdf' }}
                                </span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('mahasiswa.submissions.proposal', $submission->id) }}" target="_blank"
                                    class="text-[11px] text-blue-700 hover:text-blue-900 font-bold flex items-center space-x-1">
                                    <i class="fa-solid fa-eye"></i>
                                    <span>Lihat Proposal</span>
                                </a>
                                <a href="{{ route('mahasiswa.submissions.proposal', ['submission' => $submission->id, 'download' => 1]) }}"
                                    class="text-[11px] text-slate-500 hover:text-slate-800 font-bold" title="Unduh PDF">
                                    <i class="fa-solid fa-download"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabel Rincian RAB -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="text-sm font-bold text-slate-800 flex items-center">
                        <i class="fa-solid fa-money-bill-wave text-blue-600 mr-2"></i>
                        Rencana Anggaran Biaya (RAB)
                    </h3>
                    <span class="text-xs font-bold text-blue-800">
                        Total Diajukan: Rp {{ number_format($budget?->total_requested ?? 0, 0, ',', '.') }}
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="bg-slate-50 text-slate-500 uppercase text-[10px] tracking-wider border-b border-slate-200">
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
                                    <td class="py-2.5 px-3 font-medium text-slate-800">{{ $item->item_name }}</td>
                                    <td class="py-2.5 px-3 text-center">{{ $item->quantity }}</td>
                                    <td class="py-2.5 px-3 text-slate-500">{{ $item->unit }}</td>
                                    <td class="py-2.5 px-3 text-right">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                                    <td class="py-2.5 px-3 text-right font-bold text-slate-800">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-4 text-center text-slate-400">Tidak ada rincian RAB.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        <!-- RIGHT 1 COL: DOSEN, TIM, & KEUANGAN FAKULTAS -->
        <div class="space-y-6">

            <!-- Card Dosen Pendamping & Tim -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 space-y-4">
                <h3 class="text-sm font-bold text-slate-800 border-b border-slate-100 pb-3 flex items-center">
                    <i class="fa-solid fa-users text-blue-600 mr-2"></i>
                    Delegasi & Pendamping
                </h3>

                <div class="space-y-3 text-xs">
                    <div>
                        <span class="text-slate-400 block text-[10px] uppercase font-semibold">Dosen Pendamping</span>
                        <div class="font-bold text-slate-800 mt-0.5">
                            {{ $submission->supervisor_name ?? 'Belum Ditugaskan oleh Kaprodi' }}
                        </div>
                        @if($submission->supervisor_nidn)
                            <span class="text-[10px] font-mono text-blue-700">NIDN: {{ $submission->supervisor_nidn }}</span>
                        @endif
                    </div>

                    <div>
                        <span class="text-slate-400 block text-[10px] uppercase font-semibold mb-1">Susunan Tim Delegasi</span>
                        <div class="space-y-1.5">
                            @foreach($submission->members as $mem)
                                <div class="flex items-center justify-between p-2 rounded-lg bg-slate-50 border border-slate-200">
                                    <span class="font-semibold text-slate-800">{{ $mem->nim }}</span>
                                    <span class="text-[10px] px-2 py-0.5 rounded font-bold {{ $mem->role_in_team === 'Ketua Tim' ? 'bg-blue-100 text-blue-800' : 'bg-slate-200 text-slate-700' }}">
                                        {{ $mem->role_in_team }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Keputusan Plafon Dana Fakultas -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 space-y-4">
                <h3 class="text-sm font-bold text-slate-800 border-b border-slate-100 pb-3 flex items-center">
                    <i class="fa-solid fa-wallet text-blue-600 mr-2"></i>
                    Plafon Pendanaan Delegasi
                </h3>

                @if($decision)
                    <div class="space-y-3 text-xs">
                        <div class="bg-blue-50 p-3 rounded-xl border border-blue-200">
                            <span class="text-blue-700 text-[10px] font-bold uppercase block">Plafon Disetujui Dekan</span>
                            <span class="text-xl font-extrabold text-blue-900 block mt-0.5">
                                Rp {{ number_format($decision->approved_amount, 0, ',', '.') }}
                            </span>
                            @if($decision->notes)
                                <p class="text-[11px] text-blue-700 mt-1 italic">"{{ $decision->notes }}"</p>
                            @endif
                        </div>

                        @if($ticket)
                            <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 space-y-1">
                                <div class="flex items-center justify-between">
                                    <span class="text-slate-500 text-[10px] uppercase font-semibold">Tiket Pencairan:</span>
                                    <span class="font-mono font-bold text-slate-800">{{ $ticket->ticket_code }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-slate-500 text-[10px] uppercase font-semibold">Status Pencairan:</span>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800">
                                        {{ $ticket->simpay_status->label() }}
                                    </span>
                                </div>
                            </div>
                        @endif
                    </div>
                @else
                    <div class="text-center py-4 text-slate-400 text-xs">
                        <i class="fa-solid fa-clock-rotate-left text-2xl mb-2 block text-blue-400"></i>
                        Plafon dana belum ditetapkan (menunggu tahap persetujuan Dekan Fakultas).
                    </div>
                @endif
            </div>

            <!-- Capaian Prestasi & Poin SKPI jika sudah selesai -->
            @if($submission->certificates->isNotEmpty() || $incentive)
                <div class="bg-blue-50/60 rounded-2xl border border-blue-200 p-6 space-y-3 text-xs">
                    <h3 class="text-sm font-bold text-blue-950 flex items-center">
                        <i class="fa-solid fa-trophy text-amber-500 mr-2"></i>
                        Capaian Prestasi & SKPI
                    </h3>
                    @foreach($submission->certificates as $cert)
                        <div class="bg-white p-3 rounded-xl border border-blue-200 shadow-sm space-y-1">
                            <div class="font-bold text-blue-950 text-sm">{{ $cert->achievement_rank }}</div>
                            <div class="text-blue-700 font-extrabold">Poin SKPI: +{{ number_format($cert->skpi_points, 2) }}</div>
                            <span class="text-[10px] text-slate-400">Status Validasi: {{ $cert->validation_status->label() }}</span>
                        </div>
                    @endforeach

                    @if($incentive)
                        <div class="bg-blue-100 p-3 rounded-xl border border-blue-300 text-blue-950 font-bold">
                            Insentif Juara Disetujui Dekan: Rp {{ number_format($incentive->incentive_amount, 0, ',', '.') }}
                        </div>
                    @endif
                </div>
            @endif

        </div>

    </div>

</div>
@endsection

