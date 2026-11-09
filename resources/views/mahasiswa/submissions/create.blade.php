@extends('layouts.app')

@section('title', 'Formulir Pengajuan Delegasi Lomba — SIPRES UNUJA')

@section('content')
<div class="space-y-5">

    <!-- Breadcrumb & Judul Halaman -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <div class="flex items-center space-x-2 text-xs text-[#8492A6] mb-1.5">
                <a href="{{ route('mahasiswa.dashboard') }}" class="hover:text-[#2454C5] font-medium transition">Dashboard</a>
                <span class="text-slate-300">/</span>
                <span class="text-[#172B4D] font-semibold">Pengajuan Delegasi Lomba Baru</span>
            </div>
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-xl bg-[#EAF2FF] border border-blue-200/60 flex items-center justify-center text-[#2454C5] shadow-xs shrink-0">
                    <i class="fa-solid fa-trophy text-lg"></i>
                </div>
                <div>
                    <h1 class="text-xl sm:text-2xl font-black text-[#172B4D] tracking-tight">Formulir Pengajuan Delegasi Lomba</h1>
                    <p class="text-xs text-[#8492A6] mt-0.5">Lengkapi data kompetisi, anggota tim, berkas proposal kegiatan, dan rincian RAB yang diajukan.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert Mode Administrator (Read-Only) jika login sebagai Admin -->
    @if(auth()->user()->isAdmin())
        <div class="bg-[#EAF2FF] border border-blue-200 text-[#173B8F] px-4 py-3 rounded-2xl flex items-center space-x-3 text-xs shadow-xs">
            <i class="fa-solid fa-circle-info text-blue-600 text-base shrink-0"></i>
            <div>
                <strong class="font-bold">Mode Peninjauan Administrator (Read-Only):</strong>
                <span>Anda sedang meninjau format formulir pengajuan delegasi lomba. Tombol pengiriman formulir dinonaktifkan khusus untuk akun Administrator.</span>
            </div>
        </div>
    @endif

    <!-- Form Pengajuan dengan Layout 2 Kolom -->
    <form action="{{ route('mahasiswa.submissions.store') }}" method="POST" enctype="multipart/form-data" id="submissionForm">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

            <!-- ================= KOLOM KIRI (70%): FORMULIR PENGAJUAN ================= -->
            <div class="lg:col-span-8 space-y-5">

                <!-- BAGIAN 1: INFORMASI PERLOMBAAN -->
                <div class="bg-white rounded-2xl border border-[#E3EAF3] shadow-xs p-5 sm:p-6 space-y-4">
                    <div class="flex items-center space-x-2.5 pb-1">
                        <div class="w-6 h-6 rounded-full bg-[#2454C5] text-white flex items-center justify-center font-bold text-xs shadow-2xs shrink-0">1</div>
                        <h3 class="text-sm font-bold text-[#172B4D]">Informasi Perlombaan</h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3.5 text-xs">
                        <!-- Nama Perlombaan (Full width) -->
                        <div class="md:col-span-2">
                            <label class="block font-semibold text-[#172B4D] mb-1">
                                Nama Perlombaan / Kompetisi <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" name="competition_name" id="competition_name" value="{{ old('competition_name') }}" required
                                class="w-full h-10 px-3.5 py-2 border border-[#E3EAF3] rounded-xl focus:ring-2 focus:ring-[#2454C5]/20 focus:border-[#2454C5] outline-none text-[#172B4D] placeholder:text-slate-400 text-xs transition"
                                placeholder="Contoh: Pagelaran Mahasiswa Nasional (GEMASTIK)">
                        </div>

                        <!-- Penyelenggara Kegiatan -->
                        <div>
                            <label class="block font-semibold text-[#172B4D] mb-1">
                                Penyelenggara Kegiatan <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" name="organizer" id="organizer" value="{{ old('organizer') }}" required
                                class="w-full h-10 px-3.5 py-2 border border-[#E3EAF3] rounded-xl focus:ring-2 focus:ring-[#2454C5]/20 focus:border-[#2454C5] outline-none text-[#172B4D] placeholder:text-slate-400 text-xs transition"
                                placeholder="Contoh: BPTI / Puspresnas">
                        </div>

                        <!-- Kategori / Cabang Lomba -->
                        <div>
                            <label class="block font-semibold text-[#172B4D] mb-1">
                                Kategori / Cabang Lomba <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" name="category" id="category" value="{{ old('category') }}" required
                                class="w-full h-10 px-3.5 py-2 border border-[#E3EAF3] rounded-xl focus:ring-2 focus:ring-[#2454C5]/20 focus:border-[#2454C5] outline-none text-[#172B4D] placeholder:text-slate-400 text-xs transition"
                                placeholder="Contoh: Software Development / KTI">
                        </div>

                        <!-- Tingkat Perlombaan -->
                        <div>
                            <label class="block font-semibold text-[#172B4D] mb-1">
                                Tingkat Perlombaan <span class="text-rose-500">*</span>
                            </label>
                            <select name="level" id="level" required
                                class="w-full h-10 px-3.5 py-2 border border-[#E3EAF3] rounded-xl focus:ring-2 focus:ring-[#2454C5]/20 focus:border-[#2454C5] outline-none text-[#172B4D] bg-white text-xs transition cursor-pointer">
                                <option value="">-- Pilih Tingkat Perlombaan --</option>
                                <option value="REGIONAL" {{ old('level') == 'REGIONAL' ? 'selected' : '' }}>Regional (Jawa Timur / Wilayah)</option>
                                <option value="NASIONAL" {{ old('level', 'NASIONAL') == 'NASIONAL' ? 'selected' : '' }}>Nasional (Lintas Provinsi / Dikti)</option>
                                <option value="INTERNASIONAL" {{ old('level') == 'INTERNASIONAL' ? 'selected' : '' }}>Internasional (Luar Negeri)</option>
                            </select>
                        </div>

                        <!-- Tautan / Website Resmi -->
                        <div>
                            <label class="block font-semibold text-[#172B4D] mb-1">
                                Tautan / Website Resmi <span class="text-rose-500">*</span>
                            </label>
                            <input type="url" name="competition_url" id="competition_url" value="{{ old('competition_url') }}"
                                class="w-full h-10 px-3.5 py-2 border border-[#E3EAF3] rounded-xl focus:ring-2 focus:ring-[#2454C5]/20 focus:border-[#2454C5] outline-none text-[#172B4D] placeholder:text-slate-400 text-xs transition"
                                placeholder="https://puspresnas.kemdikbud.go.id">
                        </div>

                        <!-- Tanggal Mulai Pelaksanaan -->
                        <div>
                            <label class="block font-semibold text-[#172B4D] mb-1">
                                Tanggal Mulai <span class="text-rose-500">*</span>
                            </label>
                            <input type="date" name="start_date" id="start_date" value="{{ old('start_date') }}" required
                                class="w-full h-10 px-3.5 py-2 border border-[#E3EAF3] rounded-xl focus:ring-2 focus:ring-[#2454C5]/20 focus:border-[#2454C5] outline-none text-[#172B4D] text-xs transition cursor-pointer">
                        </div>

                        <!-- Tanggal Selesai Pelaksanaan -->
                        <div>
                            <label class="block font-semibold text-[#172B4D] mb-1">
                                Tanggal Selesai <span class="text-rose-500">*</span>
                            </label>
                            <input type="date" name="end_date" id="end_date" value="{{ old('end_date') }}" required
                                class="w-full h-10 px-3.5 py-2 border border-[#E3EAF3] rounded-xl focus:ring-2 focus:ring-[#2454C5]/20 focus:border-[#2454C5] outline-none text-[#172B4D] text-xs transition cursor-pointer">
                        </div>

                        <!-- Lokasi Penyelenggaraan (Full width) -->
                        <div class="md:col-span-2">
                            <label class="block font-semibold text-[#172B4D] mb-1">
                                Lokasi Penyelenggaraan <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" name="location" id="location" value="{{ old('location') }}" required
                                class="w-full h-10 px-3.5 py-2 border border-[#E3EAF3] rounded-xl focus:ring-2 focus:ring-[#2454C5]/20 focus:border-[#2454C5] outline-none text-[#172B4D] placeholder:text-slate-400 text-xs transition"
                                placeholder="Contoh: Universitas Indonesia, Depok (Online)">
                        </div>

                        <!-- Deskripsi Tambahan / Catatan Lomba (Full width) -->
                        <div class="md:col-span-2">
                            <label class="block font-semibold text-[#172B4D] mb-1">Deskripsi Tambahan</label>
                            <textarea name="description" id="description" rows="2"
                                class="w-full px-3.5 py-2 border border-[#E3EAF3] rounded-xl focus:ring-2 focus:ring-[#2454C5]/20 focus:border-[#2454C5] outline-none text-[#172B4D] placeholder:text-slate-400 text-xs transition resize-none"
                                placeholder="Keterangan singkat mengenai tahapan seleksi lomba...">{{ old('description') }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- BAGIAN 2 & 3: DOSEN PENDAMPING, SUSUNAN TIM & UNGGAH PROPOSAL (DIGABUNG DALAM 1 CARD) -->
                <div class="bg-white rounded-2xl border border-[#E3EAF3] shadow-xs p-5 sm:p-6 space-y-5">

                    <!-- Sub-Bagian 2: Dosen Pendamping & Susunan Tim -->
                    <div class="space-y-3.5">
                        <div class="flex items-center space-x-2.5">
                            <div class="w-6 h-6 rounded-full bg-[#2454C5] text-white flex items-center justify-center font-bold text-xs shadow-2xs shrink-0">2</div>
                            <h3 class="text-sm font-bold text-[#172B4D]">Dosen Pendamping & Susunan Tim</h3>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs items-start">
                            <!-- Dosen Pendamping (Mekanisme Kaprodi) -->
                            <div>
                                <div class="flex items-center space-x-1.5 mb-1.5">
                                    <label class="font-semibold text-[#172B4D]">Dosen Pendamping (Kaprodi)</label>
                                    <span class="text-[#2454C5] cursor-help" title="Sesuai kebijakan akademik, Dosen Pendamping akan ditetapkan langsung secara resmi oleh Kaprodi saat verifikasi proposal. Mahasiswa tidak perlu memilih dosen sendiri.">
                                        <i class="fa-solid fa-circle-info text-[11px]"></i>
                                    </span>
                                </div>
                                <div class="relative">
                                    <div class="w-full h-10 px-3.5 py-2 bg-[#F5F7FB] border border-[#E3EAF3] rounded-xl text-slate-500 text-xs flex items-center justify-between shadow-2xs">
                                        <span class="truncate font-medium text-slate-600">Ditentukan Resmi oleh Kaprodi</span>
                                        <i class="fa-solid fa-lock text-slate-400 text-[11px]"></i>
                                    </div>
                                    <input type="hidden" name="dosen_nidn" value="">
                                </div>
                                <p class="text-[10px] text-[#8492A6] mt-1.5">Penugasan dosen pendamping dilakukan otomatis oleh Kaprodi.</p>
                            </div>

                            <!-- Anggota Delegasi -->
                            <div>
                                <div class="flex items-center justify-between mb-1.5">
                                    <label class="font-semibold text-[#172B4D]">Anggota Delegasi (NIM Mahasiswa)</label>
                                    <button type="button" id="btnAddMember" class="text-[#2454C5] hover:text-[#173B8F] font-bold text-xs flex items-center space-x-1 cursor-pointer transition">
                                        <i class="fa-solid fa-user-plus text-[11px]"></i>
                                        <span>Tambah Anggota</span>
                                    </button>
                                </div>

                                <!-- Ketua Tim (Fixed Default) -->
                                <div class="h-10 px-3 py-1.5 bg-[#F5F7FB] border border-[#E3EAF3] rounded-xl flex items-center space-x-2 text-xs shadow-2xs">
                                    <span class="px-2 py-0.5 bg-[#EAF2FF] text-[#2454C5] rounded font-bold text-[10px] tracking-wide uppercase shrink-0">Ketua Tim</span>
                                    <span class="font-semibold text-[#172B4D] truncate">{{ auth()->user()->identifier ?? '242140001' }} ({{ auth()->user()->name }})</span>
                                </div>

                                <!-- Dynamic Anggota Inputs -->
                                <div id="membersContainer" class="space-y-2 mt-2">
                                    <!-- Anggota tambahan dirender via JS -->
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pembatas Visual Jelas antar Bagian 2 dan 3 -->
                    <div class="border-t border-[#E3EAF3]"></div>

                    <!-- Sub-Bagian 3: Unggah Dokumen Proposal -->
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-2.5">
                                <div class="w-6 h-6 rounded-full bg-[#2454C5] text-white flex items-center justify-center font-bold text-xs shadow-2xs shrink-0">3</div>
                                <h3 class="text-sm font-bold text-[#172B4D]">Unggah Dokumen Proposal</h3>
                            </div>
                            <span class="text-[11px] text-[#8492A6] font-medium">Maks. 10 MB (PDF)</span>
                        </div>

                        <div class="text-xs">
                            <div class="flex items-center justify-between mb-1.5">
                                <label class="font-semibold text-[#172B4D]">
                                    Dokumen Proposal (PDF) <span class="text-rose-500">*</span>
                                </label>
                            </div>

                            <!-- Area Upload Kustom Compact -->
                            <div class="flex items-center h-11 border border-[#E3EAF3] rounded-xl px-2.5 bg-white transition hover:border-[#2454C5]/60">
                                <label for="proposal_file" class="cursor-pointer bg-[#EAF2FF] text-[#2454C5] hover:bg-blue-100 font-bold text-xs px-3.5 py-1.5 rounded-lg flex items-center space-x-1.5 transition shrink-0 shadow-2xs">
                                    <i class="fa-solid fa-paperclip text-xs"></i>
                                    <span>Pilih File</span>
                                </label>
                                <span id="fileNameLabel" class="ml-3 text-xs text-slate-400 truncate flex-1 font-medium">Tidak ada file yang dipilih</span>
                                <button type="button" id="btnResetFile" class="hidden text-slate-400 hover:text-rose-500 p-1.5 text-xs transition cursor-pointer" title="Hapus file terpilih">
                                    <i class="fa-solid fa-xmark"></i>
                                </button>
                                <input type="file" id="proposal_file" name="proposal_file" accept=".pdf" required class="hidden">
                            </div>
                            <span class="text-[10px] text-[#8492A6] mt-1.5 block">Proposal harus mencakup latar belakang, deskripsi karya/ide lomba, jadwal, dan susunan panitia jika ada.</span>
                        </div>
                    </div>

                </div>

                <!-- BAGIAN 4: RENCANA ANGGARAN BIAYA (RAB) -->
                <div class="bg-white rounded-2xl border border-[#E3EAF3] shadow-xs p-5 sm:p-6 space-y-4">
                    <div class="flex items-center justify-between pb-1">
                        <div class="flex items-center space-x-2.5">
                            <div class="w-6 h-6 rounded-full bg-[#2454C5] text-white flex items-center justify-center font-bold text-xs shadow-2xs shrink-0">4</div>
                            <h3 class="text-sm font-bold text-[#172B4D]">Rencana Anggaran Biaya (RAB)</h3>
                        </div>
                        <button type="button" id="btnAddBudgetItem" class="bg-[#EAF2FF] hover:bg-blue-100 text-[#2454C5] font-bold px-3 py-1.5 rounded-xl text-xs flex items-center space-x-1.5 transition border border-blue-200/50 cursor-pointer shadow-2xs">
                            <i class="fa-solid fa-plus text-[10px]"></i>
                            <span>Tambah Item RAB</span>
                        </button>
                    </div>

                    <!-- Tabel Compact RAB -->
                    <div class="overflow-x-auto -mx-1 sm:mx-0">
                        <table class="w-full text-left text-xs min-w-[550px]" id="rabTable">
                            <thead>
                                <tr class="text-[#8492A6] text-[11px] font-semibold border-b border-[#E3EAF3] pb-2">
                                    <th class="py-2 px-2 font-semibold">Nama Kebutuhan / Item</th>
                                    <th class="py-2 px-2 w-20 font-semibold">Volume</th>
                                    <th class="py-2 px-2 w-24 font-semibold">Satuan</th>
                                    <th class="py-2 px-2 w-32 font-semibold">Harga Satuan (Rp)</th>
                                    <th class="py-2 px-2 w-32 font-semibold text-right">Subtotal (Rp)</th>
                                    <th class="py-2 px-1 w-10 text-center font-semibold">Hapus</th>
                                </tr>
                            </thead>
                            <tbody id="rabTableBody" class="divide-y divide-[#E3EAF3]/60">
                                <!-- Baris Default 1 -->
                                <tr class="rab-row">
                                    <td class="py-2 px-2">
                                        <input type="text" name="budget_items[0][item_name]" value="Biaya Registrasi / Pendaftaran Lomba" required
                                            class="w-full h-9 px-3 py-1.5 border border-[#E3EAF3] rounded-lg outline-none focus:ring-1 focus:ring-[#2454C5] text-[#172B4D] text-xs">
                                    </td>
                                    <td class="py-2 px-2">
                                        <input type="number" name="budget_items[0][quantity]" value="1" min="1" required
                                            class="w-full h-9 px-2.5 py-1.5 border border-[#E3EAF3] rounded-lg outline-none focus:ring-1 focus:ring-[#2454C5] text-[#172B4D] text-xs qty-input text-center">
                                    </td>
                                    <td class="py-2 px-2">
                                        <select name="budget_items[0][unit]" required
                                            class="w-full h-9 px-2 py-1.5 border border-[#E3EAF3] rounded-lg outline-none focus:ring-1 focus:ring-[#2454C5] text-[#172B4D] text-xs bg-white cursor-pointer">
                                            <option value="Tim" selected>Tim</option>
                                            <option value="Orang">Orang</option>
                                            <option value="Paket">Paket</option>
                                            <option value="Unit">Unit</option>
                                            <option value="Lembar">Lembar</option>
                                            <option value="Kegiatan">Kegiatan</option>
                                        </select>
                                    </td>
                                    <td class="py-2 px-2">
                                        <input type="number" name="budget_items[0][unit_price]" value="350000" min="0" step="1000" required
                                            class="w-full h-9 px-2.5 py-1.5 border border-[#E3EAF3] rounded-lg outline-none focus:ring-1 focus:ring-[#2454C5] text-[#172B4D] text-xs price-input text-right">
                                    </td>
                                    <td class="py-2 px-2 font-bold text-[#172B4D] subtotal-text text-right">
                                        Rp 350.000
                                    </td>
                                    <td class="py-2 px-1 text-center">
                                        <button type="button" class="text-slate-300 hover:text-rose-500 btn-remove-row p-1 transition" disabled>
                                            <i class="fa-solid fa-trash-can text-xs"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Total Row Compact -->
                    <div class="bg-[#EAF2FF]/50 border border-blue-200/50 rounded-xl px-4 py-2.5 flex items-center justify-end space-x-6 text-xs">
                        <span class="font-bold text-[#172B4D]">Total Anggaran Diajukan:</span>
                        <span class="text-base font-black text-[#2454C5]" id="totalRabText">Rp 350.000</span>
                    </div>
                </div>

                <!-- TOMBOL AKSI FORMULIR -->
                <div class="flex items-center justify-end space-x-3 pt-2">
                    <a href="{{ route('mahasiswa.dashboard') }}" class="px-5 py-2.5 rounded-xl border border-[#E3EAF3] bg-white text-[#172B4D] font-bold text-xs hover:bg-slate-50 transition shadow-2xs">
                        Batal
                    </a>

                    @if(auth()->user()->isAdmin())
                        <button type="button" disabled class="px-6 py-2.5 rounded-xl bg-slate-300 text-slate-600 font-bold text-xs cursor-not-allowed flex items-center space-x-2" title="Mode Administrator (Read-Only)">
                            <i class="fa-solid fa-lock text-xs"></i>
                            <span>Kirim Pengajuan (Nonaktif untuk Admin)</span>
                        </button>
                    @else
                        <button type="submit" id="btnSubmitForm" class="px-6 py-2.5 rounded-xl bg-[#2454C5] hover:bg-[#173B8F] text-white font-bold text-xs shadow-sm hover:shadow transition flex items-center space-x-2 cursor-pointer">
                            <i class="fa-solid fa-paper-plane text-xs"></i>
                            <span>Kirim Pengajuan ke Kaprodi</span>
                        </button>
                    @endif
                </div>

            </div>

            <!-- ================= KOLOM KANAN (30%): PROGRESS & KETENTUAN ================= -->
            <div class="lg:col-span-4 space-y-5 lg:sticky lg:top-6">

                <!-- CARD PROGRESS PENGAJUAN (REAKTIF NYATA) -->
                <div class="bg-white rounded-2xl border border-[#E3EAF3] shadow-xs p-5 space-y-4">
                    <div class="flex items-center justify-between">
                        <h4 class="font-bold text-[#172B4D] text-xs sm:text-sm">Progress Pengajuan</h4>
                        <span id="progressPercent" class="font-black text-[#2454C5] text-xs sm:text-sm">25%</span>
                    </div>

                    <!-- Progress Bar -->
                    <div class="w-full bg-[#F5F7FB] border border-[#E3EAF3]/80 rounded-full h-2.5 overflow-hidden">
                        <div id="progressBar" class="bg-[#2454C5] h-2.5 rounded-full transition-all duration-300 ease-out" style="width: 25%"></div>
                    </div>

                    <!-- Step-by-Step Indikator Pengisian -->
                    <div class="space-y-3 pt-1">
                        <!-- Step 1 -->
                        <div id="step-item-1" class="flex items-center space-x-3 transition">
                            <div class="step-badge w-6 h-6 rounded-full bg-[#EAF2FF] text-[#2454C5] border border-blue-200 flex items-center justify-center font-bold text-xs shrink-0 transition">
                                1
                            </div>
                            <span class="step-label text-xs font-semibold text-[#8492A6] transition">Isi Informasi Perlombaan</span>
                        </div>

                        <!-- Step 2 -->
                        <div id="step-item-2" class="flex items-center space-x-3 transition">
                            <div class="step-badge w-6 h-6 rounded-full bg-[#2454C5] text-white flex items-center justify-center font-bold text-xs shrink-0 shadow-2xs transition">
                                <i class="fa-solid fa-check text-[10px]"></i>
                            </div>
                            <span class="step-label text-xs font-bold text-[#172B4D] transition">Pilih Dosen & Susun Tim</span>
                        </div>

                        <!-- Step 3 -->
                        <div id="step-item-3" class="flex items-center space-x-3 transition">
                            <div class="step-badge w-6 h-6 rounded-full bg-[#EAF2FF] text-[#2454C5] border border-blue-200 flex items-center justify-center font-bold text-xs shrink-0 transition">
                                3
                            </div>
                            <span class="step-label text-xs font-semibold text-[#8492A6] transition">Unggah Proposal</span>
                        </div>

                        <!-- Step 4 -->
                        <div id="step-item-4" class="flex items-center space-x-3 transition">
                            <div class="step-badge w-6 h-6 rounded-full bg-[#EAF2FF] text-[#2454C5] border border-blue-200 flex items-center justify-center font-bold text-xs shrink-0 transition">
                                4
                            </div>
                            <span class="step-label text-xs font-semibold text-[#8492A6] transition">Lengkapi RAB</span>
                        </div>
                    </div>
                </div>

                <!-- CARD KETENTUAN SINGKAT -->
                <div class="bg-[#EAF2FF]/60 border border-blue-200/80 rounded-2xl p-5 space-y-3">
                    <div class="flex items-center space-x-2 text-[#2454C5] font-bold text-xs sm:text-sm">
                        <i class="fa-solid fa-circle-info"></i>
                        <span>Ketentuan Singkat</span>
                    </div>

                    <ul class="space-y-2 text-xs text-[#172B4D] leading-relaxed">
                        <li class="flex items-start space-x-2">
                            <span class="text-[#2454C5] font-bold mt-0.5">•</span>
                            <span>Data lomba harus sesuai pamflet/undangan resmi.</span>
                        </li>
                        <li class="flex items-start space-x-2">
                            <span class="text-[#2454C5] font-bold mt-0.5">•</span>
                            <span>Proposal wajib berformat PDF (maks. 10 MB).</span>
                        </li>
                        <li class="flex items-start space-x-2">
                            <span class="text-[#2454C5] font-bold mt-0.5">•</span>
                            <span>Dosen pendamping ditentukan oleh Kaprodi.</span>
                        </li>
                        <li class="flex items-start space-x-2">
                            <span class="text-[#2454C5] font-bold mt-0.5">•</span>
                            <span>RAB disusun sesuai kebutuhan kegiatan.</span>
                        </li>
                    </ul>
                </div>

            </div>

        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // ── 1. DYNAMIC ANGGOTA DELEGASI TIM ──
        const btnAddMember = document.getElementById('btnAddMember');
        const membersContainer = document.getElementById('membersContainer');

        if (btnAddMember && membersContainer) {
            btnAddMember.addEventListener('click', function () {
                const div = document.createElement('div');
                div.className = 'flex items-center space-x-2';
                div.innerHTML = `
                    <input type="text" name="members[]" placeholder="Masukkan NIM Anggota (Contoh: 242140002)" required
                        class="flex-1 h-10 px-3.5 py-2 border border-[#E3EAF3] rounded-xl focus:ring-2 focus:ring-[#2454C5]/20 focus:border-[#2454C5] outline-none text-[#172B4D] text-xs transition">
                    <button type="button" class="w-10 h-10 flex items-center justify-center text-slate-400 hover:text-rose-500 hover:bg-rose-50 rounded-xl transition cursor-pointer" onclick="this.parentElement.remove(); updateProgress();" title="Hapus anggota">
                        <i class="fa-solid fa-trash-can text-xs"></i>
                    </button>
                `;
                membersContainer.appendChild(div);
                updateProgress();
            });
        }

        // ── 2. PROPOSAL FILE UPLOAD HANDLING & VALIDATION ──
        const proposalFileInput = document.getElementById('proposal_file');
        const fileNameLabel = document.getElementById('fileNameLabel');
        const btnResetFile = document.getElementById('btnResetFile');

        if (proposalFileInput && fileNameLabel) {
            proposalFileInput.addEventListener('change', function () {
                if (this.files && this.files.length > 0) {
                    const file = this.files[0];
                    const maxSizeBytes = 10 * 1024 * 1024; // 10MB

                    // Validasi tipe file PDF
                    if (file.type !== 'application/pdf' && !file.name.toLowerCase().endsWith('.pdf')) {
                        alert('Format berkas tidak valid. Berkas proposal wajib berformat PDF (.pdf).');
                        resetFileInput();
                        return;
                    }

                    // Validasi ukuran maksimal 10MB
                    if (file.size > maxSizeBytes) {
                        alert('Ukuran berkas melebihi batas. Ukuran berkas proposal maksimal adalah 10 MB.');
                        resetFileInput();
                        return;
                    }

                    const fileSizeFormatted = (file.size / (1024 * 1024)).toFixed(1);
                    fileNameLabel.innerHTML = `
                        <span class="text-[#2454C5] font-semibold flex items-center">
                            <i class="fa-solid fa-file-pdf text-rose-500 mr-1.5 text-xs"></i>
                            <span class="truncate max-w-[280px]">${file.name}</span>
                            <span class="text-slate-400 font-normal text-[10px] ml-1.5">(${fileSizeFormatted} MB)</span>
                        </span>
                    `;
                    btnResetFile.classList.remove('hidden');
                } else {
                    resetFileInput();
                }
                updateProgress();
            });

            function resetFileInput() {
                proposalFileInput.value = '';
                fileNameLabel.textContent = 'Tidak ada file yang dipilih';
                fileNameLabel.className = 'ml-3 text-xs text-slate-400 truncate flex-1 font-medium';
                btnResetFile.classList.add('hidden');
                updateProgress();
            }

            if (btnResetFile) {
                btnResetFile.addEventListener('click', resetFileInput);
            }
        }

        // ── 3. DYNAMIC TABEL RAB & AUTO-CALCULATE ──
        const btnAddBudgetItem = document.getElementById('btnAddBudgetItem');
        const rabTableBody = document.getElementById('rabTableBody');
        const totalRabText = document.getElementById('totalRabText');
        let rowIndex = 1;

        function formatRupiah(val) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                maximumFractionDigits: 0
            }).format(val);
        }

        function recalculateAll() {
            let total = 0;
            const rows = document.querySelectorAll('.rab-row');

            rows.forEach((row) => {
                const qtyInput = row.querySelector('.qty-input');
                const priceInput = row.querySelector('.price-input');
                const subtotalText = row.querySelector('.subtotal-text');
                const removeBtn = row.querySelector('.btn-remove-row');

                const qty = parseFloat(qtyInput.value) || 0;
                const price = parseFloat(priceInput.value) || 0;
                const subtotal = qty * price;
                total += subtotal;

                if (subtotalText) {
                    subtotalText.textContent = formatRupiah(subtotal);
                }

                if (rows.length > 1) {
                    removeBtn.removeAttribute('disabled');
                    removeBtn.classList.remove('text-slate-300');
                    removeBtn.classList.add('text-rose-500', 'hover:bg-rose-50', 'cursor-pointer');
                } else {
                    removeBtn.setAttribute('disabled', 'true');
                    removeBtn.classList.add('text-slate-300');
                    removeBtn.classList.remove('text-rose-500', 'hover:bg-rose-50', 'cursor-pointer');
                }
            });

            if (totalRabText) {
                totalRabText.textContent = formatRupiah(total);
            }

            updateProgress();
        }

        if (btnAddBudgetItem && rabTableBody) {
            btnAddBudgetItem.addEventListener('click', function () {
                const tr = document.createElement('tr');
                tr.className = 'rab-row';
                tr.innerHTML = `
                    <td class="py-2 px-2">
                        <input type="text" name="budget_items[${rowIndex}][item_name]" placeholder="Nama item biaya" required
                            class="w-full h-9 px-3 py-1.5 border border-[#E3EAF3] rounded-lg outline-none focus:ring-1 focus:ring-[#2454C5] text-[#172B4D] text-xs">
                    </td>
                    <td class="py-2 px-2">
                        <input type="number" name="budget_items[${rowIndex}][quantity]" value="1" min="1" required
                            class="w-full h-9 px-2.5 py-1.5 border border-[#E3EAF3] rounded-lg outline-none focus:ring-1 focus:ring-[#2454C5] text-[#172B4D] text-xs qty-input text-center">
                    </td>
                    <td class="py-2 px-2">
                        <select name="budget_items[${rowIndex}][unit]" required
                            class="w-full h-9 px-2 py-1.5 border border-[#E3EAF3] rounded-lg outline-none focus:ring-1 focus:ring-[#2454C5] text-[#172B4D] text-xs bg-white cursor-pointer">
                            <option value="Tim">Tim</option>
                            <option value="Orang" selected>Orang</option>
                            <option value="Paket">Paket</option>
                            <option value="Unit">Unit</option>
                            <option value="Lembar">Lembar</option>
                            <option value="Kegiatan">Kegiatan</option>
                        </select>
                    </td>
                    <td class="py-2 px-2">
                        <input type="number" name="budget_items[${rowIndex}][unit_price]" value="0" min="0" step="1000" required
                            class="w-full h-9 px-2.5 py-1.5 border border-[#E3EAF3] rounded-lg outline-none focus:ring-1 focus:ring-[#2454C5] text-[#172B4D] text-xs price-input text-right">
                    </td>
                    <td class="py-2 px-2 font-bold text-[#172B4D] subtotal-text text-right">
                        Rp 0
                    </td>
                    <td class="py-2 px-1 text-center">
                        <button type="button" class="text-rose-500 hover:text-rose-700 btn-remove-row p-1 transition cursor-pointer" title="Hapus baris">
                            <i class="fa-solid fa-trash-can text-xs"></i>
                        </button>
                    </td>
                `;

                rabTableBody.appendChild(tr);
                rowIndex++;
                recalculateAll();
            });

            rabTableBody.addEventListener('input', function (e) {
                if (e.target.classList.contains('qty-input') || e.target.classList.contains('price-input')) {
                    recalculateAll();
                }
            });

            rabTableBody.addEventListener('click', function (e) {
                const btn = e.target.closest('.btn-remove-row');
                if (btn && !btn.hasAttribute('disabled')) {
                    btn.closest('tr').remove();
                    recalculateAll();
                }
            });
        }

        // ── 4. REAKTIF PROGRESS PENGAJUAN (SESUAI FORM ASLI) ──
        function updateProgress() {
            // Step 1: Informasi Perlombaan
            const compName = document.getElementById('competition_name')?.value.trim();
            const organizer = document.getElementById('organizer')?.value.trim();
            const category = document.getElementById('category')?.value.trim();
            const level = document.getElementById('level')?.value.trim();
            const startDate = document.getElementById('start_date')?.value.trim();
            const endDate = document.getElementById('end_date')?.value.trim();
            const location = document.getElementById('location')?.value.trim();

            const isStep1Done = Boolean(compName && organizer && category && level && startDate && endDate && location);

            // Step 2: Dosen Pendamping & Susunan Tim (Ketua tim selalu terdaftar & mekanisme dosen Kaprodi sudah siap)
            const isStep2Done = true;

            // Step 3: Unggah Proposal (cek apakah file proposal sudah dipilih)
            const isStep3Done = Boolean(proposalFileInput && proposalFileInput.files && proposalFileInput.files.length > 0);

            // Step 4: RAB (cek apakah minimal 1 item ada namanya dan harganya > 0)
            const rows = document.querySelectorAll('.rab-row');
            let isStep4Done = false;
            if (rows.length > 0) {
                let validCount = 0;
                rows.forEach((r) => {
                    const itemName = r.querySelector('input[type="text"]')?.value.trim();
                    const qty = parseFloat(r.querySelector('.qty-input')?.value) || 0;
                    const price = parseFloat(r.querySelector('.price-input')?.value) || 0;
                    if (itemName && qty > 0 && price > 0) {
                        validCount++;
                    }
                });
                isStep4Done = (validCount > 0);
            }

            // Tentukan status untuk setiap step item di sidebar
            setStepUI(1, isStep1Done);
            setStepUI(2, isStep2Done);
            setStepUI(3, isStep3Done);
            setStepUI(4, isStep4Done);

            // Hitung persentase total (masing-masing bernilai 25%)
            let completed = 0;
            if (isStep1Done) completed++;
            if (isStep2Done) completed++;
            if (isStep3Done) completed++;
            if (isStep4Done) completed++;

            const percentage = completed * 25;

            const progressPercentEl = document.getElementById('progressPercent');
            const progressBarEl = document.getElementById('progressBar');

            if (progressPercentEl) {
                progressPercentEl.textContent = `${percentage}%`;
            }
            if (progressBarEl) {
                progressBarEl.style.width = `${percentage}%`;
                if (percentage === 100) {
                    progressBarEl.className = 'bg-emerald-600 h-2.5 rounded-full transition-all duration-300 ease-out';
                } else {
                    progressBarEl.className = 'bg-[#2454C5] h-2.5 rounded-full transition-all duration-300 ease-out';
                }
            }
        }

        function setStepUI(stepNumber, isDone) {
            const stepItem = document.getElementById(`step-item-${stepNumber}`);
            if (!stepItem) return;

            const badge = stepItem.querySelector('.step-badge');
            const label = stepItem.querySelector('.step-label');

            if (isDone) {
                badge.className = 'step-badge w-6 h-6 rounded-full bg-[#2454C5] text-white flex items-center justify-center font-bold text-xs shrink-0 shadow-2xs transition';
                badge.innerHTML = '<i class="fa-solid fa-check text-[10px]"></i>';
                label.className = 'step-label text-xs font-bold text-[#172B4D] transition';
            } else {
                badge.className = 'step-badge w-6 h-6 rounded-full bg-[#EAF2FF] text-[#2454C5] border border-blue-200 flex items-center justify-center font-bold text-xs shrink-0 transition';
                badge.innerHTML = `${stepNumber}`;
                label.className = 'step-label text-xs font-semibold text-[#8492A6] transition';
            }
        }

        // Listen form input events untuk real-time update progress
        const submissionForm = document.getElementById('submissionForm');
        if (submissionForm) {
            submissionForm.addEventListener('input', updateProgress);
            submissionForm.addEventListener('change', updateProgress);
        }

        // Initial calculation & progress trigger
        recalculateAll();
        updateProgress();
    });
</script>
@endpush
