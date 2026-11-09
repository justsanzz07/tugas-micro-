<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proposal — {{ $submission->code }} — {{ $submission->competitionDetail?->competition_name ?? $submission->title }}</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        @media print {
            .no-print { display: none !important; }
            body { background: #fff !important; padding: 0 !important; }
            .print-shadow-none { box-shadow: none !important; border: 1px solid #e2e8f0 !important; }
        }
    </style>
</head>
<body class="bg-slate-100 text-slate-800 p-4 sm:p-8 min-h-screen">

    <!-- Top Action Bar (No Print) -->
    <div class="max-w-4xl mx-auto mb-4 flex items-center justify-between no-print">
        <div class="flex items-center space-x-2 text-xs text-slate-600">
            <span class="px-2.5 py-1 bg-blue-100 text-blue-900 font-bold rounded-lg">SIPRES UNUJA</span>
            <span>&bull;</span>
            <span class="font-mono font-bold">{{ $submission->code }}</span>
        </div>
        <div class="flex items-center space-x-2">
            <button onclick="window.print()" class="px-3.5 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold transition shadow flex items-center space-x-1.5">
                <i class="fa-solid fa-print"></i>
                <span>Cetak / Simpan PDF</span>
            </button>
            <button onclick="window.close()" class="px-3.5 py-1.5 bg-slate-200 hover:bg-slate-300 text-slate-700 rounded-xl text-xs font-bold transition">
                Tutup
            </button>
        </div>
    </div>

    <!-- Official Proposal Document Sheet -->
    <div class="max-w-4xl mx-auto bg-white rounded-2xl border border-slate-200 shadow-xl p-6 sm:p-10 print-shadow-none space-y-6">

        <!-- KOP DOKUMEN UNIVERSITAS NURUL JADID -->
        <div class="flex items-center justify-between pb-6 border-b-2 border-slate-900 gap-4">
            <div class="flex items-center space-x-4">
                <div class="w-14 h-14 rounded-2xl bg-blue-900 text-white flex items-center justify-center font-black text-2xl shadow-md">
                    <i class="fa-solid fa-graduation-cap text-amber-400"></i>
                </div>
                <div>
                    <h2 class="text-base sm:text-lg font-black tracking-tight text-blue-950 uppercase leading-snug">
                        Universitas Nurul Jadid (UNUJA)
                    </h2>
                    <h3 class="text-xs font-extrabold text-blue-800 uppercase tracking-wide">
                        Pusat Data & Sistem Informasi (PDSI) &bull; Bagian Kemahasiswaan
                    </h3>
                    <p class="text-[11px] text-slate-500 mt-0.5">
                        Sistem Informasi Pengajuan Lomba & Prestasi Mahasiswa (SIPRES)
                    </p>
                </div>
            </div>
            <div class="text-right shrink-0">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Dokumen Resmi</span>
                <span class="text-xs font-black font-mono text-blue-900 px-2.5 py-1 rounded-md bg-blue-50 border border-blue-200 inline-block mt-0.5">
                    {{ $submission->code }}
                </span>
            </div>
        </div>

        <!-- JUDUL DOKUMEN PROPOSAL -->
        <div class="text-center py-2">
            <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-blue-100 text-blue-900 border border-blue-200 inline-block mb-2">
                Usulan Proposal Delegasi Lomba Mahasiswa
            </span>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight leading-tight">
                {{ $submission->competitionDetail?->competition_name ?? $submission->title }}
            </h1>
            <p class="text-xs text-slate-500 mt-1">
                Diajukan pada tanggal: <strong>{{ $submission->created_at?->format('d F Y, H:i') ?? date('d F Y') }} WIB</strong>
            </p>
        </div>

        <!-- 1. DETAIL KOMPETISI -->
        <div class="space-y-3">
            <h4 class="text-xs font-bold text-blue-900 uppercase tracking-wider flex items-center space-x-2 border-b border-slate-100 pb-2">
                <i class="fa-solid fa-trophy text-blue-600"></i>
                <span>1. Informasi & Profil Perlombaan</span>
            </h4>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs bg-slate-50 p-4 rounded-xl border border-slate-200">
                <div>
                    <span class="text-slate-400 block text-[10px] uppercase font-bold">Nama Perlombaan / Acara</span>
                    <span class="font-bold text-slate-900 text-sm">{{ $submission->competitionDetail?->competition_name ?? $submission->title }}</span>
                </div>
                <div>
                    <span class="text-slate-400 block text-[10px] uppercase font-bold">Penyelenggara Kegiatan</span>
                    <span class="font-bold text-slate-800">{{ $submission->competitionDetail?->organizer ?? 'Panitia Resmi Lomba' }}</span>
                </div>
                <div>
                    <span class="text-slate-400 block text-[10px] uppercase font-bold">Tingkat & Kategori Perlombaan</span>
                    <span class="font-bold text-slate-800 uppercase">{{ $submission->competitionDetail?->level?->label() ?? 'Nasional' }} &bull; {{ $submission->competitionDetail?->category ?? 'Karya Ilmiah / Teknologi' }}</span>
                </div>
                <div>
                    <span class="text-slate-400 block text-[10px] uppercase font-bold">Jadwal Pelaksanaan</span>
                    <span class="font-bold text-slate-800">
                        {{ $submission->competitionDetail?->start_date?->format('d M Y') ?? 'TBA' }} s/d {{ $submission->competitionDetail?->end_date?->format('d M Y') ?? 'TBA' }}
                    </span>
                </div>
                <div>
                    <span class="text-slate-400 block text-[10px] uppercase font-bold">Lokasi / Tempat Acara</span>
                    <span class="font-bold text-slate-800">{{ $submission->competitionDetail?->location ?? 'Kampus Penyelenggara' }}</span>
                </div>
                <div>
                    <span class="text-slate-400 block text-[10px] uppercase font-bold">Tautan Informasi / Website</span>
                    @if($submission->competitionDetail?->competition_url)
                        <a href="{{ $submission->competitionDetail->competition_url }}" target="_blank" class="text-blue-700 font-bold hover:underline truncate block">
                            {{ $submission->competitionDetail->competition_url }}
                        </a>
                    @else
                        <span class="text-slate-400">-</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- 2. SUSUNAN TIM DELEGASI -->
        <div class="space-y-3">
            <h4 class="text-xs font-bold text-blue-900 uppercase tracking-wider flex items-center space-x-2 border-b border-slate-100 pb-2">
                <i class="fa-solid fa-users text-blue-600"></i>
                <span>2. Susunan Delegasi Mahasiswa</span>
            </h4>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs border border-slate-200 rounded-xl overflow-hidden">
                    <thead class="bg-blue-50/60 text-blue-950 text-[10px] uppercase font-bold border-b border-slate-200">
                        <tr>
                            <th class="py-2.5 px-3 w-10">No</th>
                            <th class="py-2.5 px-3">Nama Mahasiswa</th>
                            <th class="py-2.5 px-3">NIM</th>
                            <th class="py-2.5 px-3">Program Studi</th>
                            <th class="py-2.5 px-3">Peran dalam Tim</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($submission->members as $idx => $m)
                            <tr>
                                <td class="py-2 px-3 font-bold">{{ $idx + 1 }}</td>
                                <td class="py-2 px-3 font-bold text-slate-900">{{ $m->name ?? $m->nim }}</td>
                                <td class="py-2 px-3 font-mono text-blue-800 font-bold">{{ $m->nim }}</td>
                                <td class="py-2 px-3 text-slate-600">{{ $m->study_program_name ?? 'Fakultas Teknologi Informasi' }}</td>
                                <td class="py-2 px-3">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $m->role_in_team === 'Ketua Tim' ? 'bg-blue-100 text-blue-900' : 'bg-slate-100 text-slate-700' }}">
                                        {{ $m->role_in_team }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="py-2 px-3 font-bold">1</td>
                                <td class="py-2 px-3 font-bold text-slate-900">{{ $submission->submitter_name ?? 'Mahasiswa Pengusul' }}</td>
                                <td class="py-2 px-3 font-mono text-blue-800 font-bold">{{ $submission->submitter_nim ?? $submission->mahasiswa_nim }}</td>
                                <td class="py-2 px-3 text-slate-600">{{ $submission->study_program_name ?? 'Program Studi Pengusul' }}</td>
                                <td class="py-2 px-3"><span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-100 text-blue-900">Ketua Tim</span></td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 3. DOSEN PENDAMPING -->
        <div class="space-y-3">
            <h4 class="text-xs font-bold text-blue-900 uppercase tracking-wider flex items-center space-x-2 border-b border-slate-100 pb-2">
                <i class="fa-solid fa-chalkboard-user text-blue-600"></i>
                <span>3. Dosen Pendamping yang Ditugaskan</span>
            </h4>
            <div class="bg-blue-50/50 p-4 rounded-xl border border-blue-200 text-xs flex items-center justify-between">
                <div>
                    <span class="text-[10px] font-bold text-blue-700 uppercase block">Nama Dosen Pendamping</span>
                    <div class="font-extrabold text-sm text-slate-900 mt-0.5">
                        {{ $submission->supervisor_name ?? 'Belum Ditugaskan oleh Kaprodi' }}
                    </div>
                    @if($submission->supervisor_nidn)
                        <span class="text-[11px] font-mono text-blue-800 font-semibold">NIDN: {{ $submission->supervisor_nidn }}</span>
                    @endif
                </div>
                <div>
                    @if($submission->supervisor_name)
                        <span class="px-3 py-1 rounded-full text-[11px] font-bold bg-blue-600 text-white shadow-sm">
                            <i class="fa-solid fa-circle-check mr-1"></i> Dosen Resmi Ditugaskan
                        </span>
                    @else
                        <span class="px-3 py-1 rounded-full text-[11px] font-bold bg-amber-100 text-amber-900 border border-amber-300">
                            <i class="fa-solid fa-clock mr-1"></i> Menunggu Penunjukan Kaprodi
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <!-- 4. RENCANA ANGGARAN BIAYA (RAB) -->
        <div class="space-y-3">
            <h4 class="text-xs font-bold text-blue-900 uppercase tracking-wider flex items-center space-x-2 border-b border-slate-100 pb-2">
                <i class="fa-solid fa-file-invoice-dollar text-blue-600"></i>
                <span>4. Usulan Rencana Anggaran Biaya (RAB)</span>
            </h4>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs border border-slate-200 rounded-xl overflow-hidden">
                    <thead class="bg-slate-50 text-slate-600 text-[10px] uppercase font-bold border-b border-slate-200">
                        <tr>
                            <th class="py-2.5 px-3">Nama Kebutuhan / Pos Anggaran</th>
                            <th class="py-2.5 px-3 text-center">Vol</th>
                            <th class="py-2.5 px-3">Satuan</th>
                            <th class="py-2.5 px-3 text-right">Harga Satuan (Rp)</th>
                            <th class="py-2.5 px-3 text-right">Subtotal (Rp)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($budget?->items ?? [] as $item)
                            <tr>
                                <td class="py-2 px-3 font-semibold text-slate-800">{{ $item->item_name }}</td>
                                <td class="py-2 px-3 text-center">{{ $item->quantity }}</td>
                                <td class="py-2 px-3 text-slate-500">{{ $item->unit }}</td>
                                <td class="py-2 px-3 text-right font-mono">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                                <td class="py-2 px-3 text-right font-bold text-slate-900 font-mono">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td class="py-2 px-3 font-semibold text-slate-800">Biaya Pendaftaran & Operasional Delegasi</td>
                                <td class="py-2 px-3 text-center">1</td>
                                <td class="py-2 px-3 text-slate-500">Paket</td>
                                <td class="py-2 px-3 text-right font-mono">Rp {{ number_format($submission->total_budget_requested ?? 0, 0, ',', '.') }}</td>
                                <td class="py-2 px-3 text-right font-bold text-slate-900 font-mono">Rp {{ number_format($submission->total_budget_requested ?? 0, 0, ',', '.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="bg-blue-50 font-bold border-t border-slate-200 text-xs">
                        <tr>
                            <td colspan="4" class="py-3 px-3 text-right text-blue-950 font-extrabold uppercase">Total Anggaran yang Diajukan:</td>
                            <td class="py-3 px-3 text-right text-blue-900 text-sm font-black font-mono">
                                Rp {{ number_format($budget?->total_requested ?: $submission->total_budget_requested, 0, ',', '.') }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- TANDA TANGAN & PENGESAHAN -->
        <div class="pt-6 border-t-2 border-slate-200 grid grid-cols-2 gap-6 text-xs text-center">
            <div class="space-y-12">
                <div>
                    <span class="text-slate-400 block text-[10px] uppercase font-bold">Diajukan Oleh:</span>
                    <span class="font-bold text-slate-800 block mt-0.5">Ketua Tim Pengusul</span>
                </div>
                <div>
                    <div class="font-bold text-slate-900 border-b border-slate-300 pb-1 inline-block min-w-[160px]">
                        {{ $submission->submitter_name ?? $submission->mahasiswa_nim }}
                    </div>
                    <div class="text-[10px] text-slate-500 mt-0.5 font-mono">
                        NIM: {{ $submission->submitter_nim ?? $submission->mahasiswa_nim }}
                    </div>
                </div>
            </div>

            <div class="space-y-12">
                <div>
                    <span class="text-slate-400 block text-[10px] uppercase font-bold">Mengetahui & Memverifikasi:</span>
                    <span class="font-bold text-slate-800 block mt-0.5">Ketua Program Studi (Kaprodi)</span>
                </div>
                <div>
                    <div class="font-bold text-slate-900 border-b border-slate-300 pb-1 inline-block min-w-[160px]">
                        Moh. Iqbal, M.Kom
                    </div>
                    <div class="text-[10px] text-slate-500 mt-0.5 font-mono">
                        NIDN: 0720048502
                    </div>
                </div>
            </div>
        </div>

    </div>

</body>
</html>

