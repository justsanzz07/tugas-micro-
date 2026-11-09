@extends('layouts.app')

@section('title', 'Masuk Portal — SIPRES UNUJA')

@section('content')
<div class="min-h-[75vh] flex items-center justify-center py-6">
    <div class="w-full max-w-4xl grid grid-cols-1 md:grid-cols-12 bg-white rounded-3xl shadow-2xl border border-slate-100 overflow-hidden">
        
        <!-- Left Hero Panel -->
        <div class="md:col-span-5 bg-gradient-to-br from-blue-900 via-indigo-950 to-slate-950 p-8 text-white flex flex-col justify-between relative overflow-hidden">
            <div class="relative z-10">
                <div class="inline-flex items-center space-x-2 bg-white/10 px-3 py-1.5 rounded-full border border-white/20 text-xs text-blue-200 mb-6">
                    <i class="fa-solid fa-graduation-cap"></i>
                    <span>PDSI Universitas Nurul Jadid</span>
                </div>
                <h1 class="text-3xl font-black tracking-tight mb-2 text-white">SIPRES UNUJA</h1>
                <p class="text-blue-200 text-xs leading-relaxed">
                    Sistem Informasi Pengajuan Delegasi Lomba, Penetapan Dosen Pendamping oleh Kaprodi, Persetujuan Dekan, dan Pelaporan Prestasi SKPI.
                </p>
            </div>

            <!-- Workflow Highlights -->
            <div class="my-6 space-y-2.5 relative z-10 text-xs">
                <div class="flex items-center space-x-2 text-blue-100">
                    <i class="fa-solid fa-check-circle text-blue-400"></i>
                    <span>Alur Cepat: Mahasiswa &rarr; Kaprodi &rarr; Dekan</span>
                </div>
                <div class="flex items-center space-x-2 text-blue-100">
                    <i class="fa-solid fa-check-circle text-blue-400"></i>
                    <span>Penetapan & Edit Dosen Pendamping oleh Kaprodi</span>
                </div>
                <div class="flex items-center space-x-2 text-blue-100">
                    <i class="fa-solid fa-check-circle text-blue-400"></i>
                    <span>Surat Tugas & Dispensasi ber-QR Code</span>
                </div>
                <div class="flex items-center space-x-2 text-blue-100">
                    <i class="fa-solid fa-check-circle text-blue-400"></i>
                    <span>Pencairan Dana SIMPAY & Validasi Poin SKPI</span>
                </div>
            </div>

            <div class="text-[11px] text-blue-300/80 border-t border-white/10 pt-4 relative z-10">
                Hubungi Helpdesk PDSI UNUJA jika kendala akun SSO.
            </div>
        </div>

        <!-- Right Login Form & Quick Role Switcher -->
        <div class="md:col-span-7 p-8">
            <h2 class="text-xl font-black text-slate-900 mb-1">Masuk ke Portal</h2>
            <p class="text-xs text-slate-500 mb-6">Gunakan akun SSO UNUJA atau tombol simulasi role di bawah ini.</p>

            <!-- Quick Demo 4 Roles Box -->
            <div class="bg-slate-50/80 rounded-2xl p-4 border border-slate-200/80 mb-6">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-bold text-slate-700 uppercase tracking-wider flex items-center">
                        <i class="fa-solid fa-wand-magic-sparkles text-amber-500 mr-1.5"></i>
                        Akses Cepat 1-Klik (Simulasi Role):
                    </span>
                    <span class="text-[10px] text-slate-400">Klik langsung masuk</span>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 text-xs">
                    <a href="{{ route('demo.login', 'mahasiswa') }}" class="flex items-center p-2 rounded-xl bg-white hover:bg-blue-50 border border-slate-200 hover:border-blue-300 transition group shadow-sm">
                        <div class="w-7 h-7 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center font-bold mr-2 text-xs shrink-0">
                            <i class="fa-solid fa-user-graduate"></i>
                        </div>
                        <div class="truncate">
                            <div class="font-bold text-slate-800 group-hover:text-blue-800 text-[11px]">Mahasiswa</div>
                            <div class="text-[10px] text-slate-400 truncate">Ahmad Dani</div>
                        </div>
                    </a>

                    <a href="{{ route('demo.login', 'kaprodi') }}" class="flex items-center p-2 rounded-xl bg-white hover:bg-sky-50 border border-slate-200 hover:border-sky-300 transition group shadow-sm">
                        <div class="w-7 h-7 rounded-lg bg-sky-100 text-sky-800 flex items-center justify-center font-bold mr-2 text-xs shrink-0">
                            <i class="fa-solid fa-user-tie"></i>
                        </div>
                        <div class="truncate">
                            <div class="font-bold text-slate-800 group-hover:text-sky-800 text-[11px]">Kaprodi</div>
                            <div class="text-[10px] text-slate-400 truncate">Moh. Iqbal, M.Kom</div>
                        </div>
                    </a>

                    <a href="{{ route('demo.login', 'dekan') }}" class="flex items-center p-2 rounded-xl bg-white hover:bg-indigo-50 border border-slate-200 hover:border-indigo-300 transition group shadow-sm">
                        <div class="w-7 h-7 rounded-lg bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold mr-2 text-xs shrink-0">
                            <i class="fa-solid fa-award"></i>
                        </div>
                        <div class="truncate">
                            <div class="font-bold text-slate-800 group-hover:text-indigo-800 text-[11px]">Dekan</div>
                            <div class="text-[10px] text-slate-400 truncate">Dr. Achmad Faisol</div>
                        </div>
                    </a>

                    <a href="{{ route('demo.login', 'admin') }}" class="flex items-center p-2 rounded-xl bg-white hover:bg-blue-50 border border-slate-200 hover:border-blue-300 transition group shadow-sm">
                        <div class="w-7 h-7 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center font-bold mr-2 text-xs shrink-0">
                            <i class="fa-solid fa-shield-halved"></i>
                        </div>
                        <div class="truncate">
                            <div class="font-bold text-slate-800 group-hover:text-blue-800 text-[11px]">Admin</div>
                            <div class="text-[10px] text-slate-400 truncate">Super Observer</div>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Standard Login Form -->
            <form action="{{ route('login.post') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label for="email" class="block text-xs font-semibold text-slate-700 mb-1">Email / Akun SSO</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400 text-xs">
                            <i class="fa-solid fa-envelope"></i>
                        </span>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                            class="w-full pl-9 pr-3 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"
                            placeholder="nama@unuja.ac.id">
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-xs font-semibold text-slate-700 mb-1">Password</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400 text-xs">
                            <i class="fa-solid fa-lock"></i>
                        </span>
                        <input type="password" id="password" name="password" required
                            class="w-full pl-9 pr-3 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"
                            placeholder="••••••••">
                    </div>
                </div>

                <div class="flex items-center justify-between text-xs">
                    <label class="flex items-center text-slate-600 cursor-pointer">
                        <input type="checkbox" name="remember" class="rounded text-blue-600 focus:ring-blue-500 mr-2">
                        <span>Ingat Sesi Saya</span>
                    </label>
                    <a href="#" class="text-blue-700 hover:text-blue-900 font-medium">Lupa Password?</a>
                </div>

                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 px-4 rounded-xl text-xs transition shadow-md flex items-center justify-center space-x-2">
                    <i class="fa-solid fa-arrow-right-to-bracket"></i>
                    <span>Masuk ke Akun</span>
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
