<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SIPRES') — Sistem Informasi Pengajuan Lomba & Prestasi UNUJA</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Tailwind CSS (CDN for instant live rendering + Vite support) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
                    colors: {
                        unuja: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            200: '#bfdbfe',
                            300: '#93c5fd',
                            400: '#60a5fa',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                            800: '#1e40af',
                            900: '#1e3a8a',
                            950: '#172554',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
    @stack('styles')
</head>
<body class="h-full flex flex-col text-slate-800 antialiased">

    <!-- Top Navigation Bar -->
    <header class="bg-gradient-to-r from-blue-900 via-blue-800 to-indigo-900 text-white shadow-lg sticky top-0 z-50 border-b border-blue-700/50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Brand / Logo -->
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-xl bg-blue-600/40 flex items-center justify-center font-extrabold text-xl tracking-wider text-blue-200 border border-blue-400/30 shadow-inner">
                        <i class="fa-solid fa-trophy text-amber-400"></i>
                    </div>
                    <div>
                        <a href="{{ route('home') }}" class="font-black text-lg tracking-tight block">SIPRES UNUJA</a>
                        <span class="text-xs text-blue-200 block -mt-1 font-medium">Sistem Pengajuan Lomba & Prestasi Mahasiswa</span>
                    </div>
                </div>

                <!-- Center Active Role Badge -->
                @auth
                    @php
                        $user = auth()->user();
                        $roleBadgeColor = match($user->role?->value ?? '') {
                            'mahasiswa' => 'bg-blue-500/20 text-blue-100 border-blue-400/40',
                            'kaprodi' => 'bg-sky-500/20 text-sky-100 border-sky-400/40',
                            'dosen' => 'bg-sky-500/20 text-sky-100 border-sky-400/40',
                            'dekan' => 'bg-indigo-500/20 text-indigo-100 border-indigo-400/40',
                            'admin' => 'bg-amber-500/20 text-amber-100 border-amber-400/40',
                            default => 'bg-slate-500/20 text-slate-200 border-slate-400/30',
                        };
                    @endphp
                    <div class="hidden md:flex items-center space-x-2">
                        <span class="px-3 py-1 rounded-full text-xs font-semibold uppercase tracking-wider border {{ $roleBadgeColor }}">
                            <i class="fa-solid fa-user-shield mr-1"></i> Role: {{ $user->role?->label() ?? 'User' }}
                        </span>
                        @if($user->fakultas_id)
                            <span class="px-2.5 py-1 rounded-md bg-white/10 text-xs font-medium text-blue-100">
                                <i class="fa-solid fa-building-columns mr-1"></i> {{ $user->fakultas_id }}
                            </span>
                        @endif
                    </div>
                @endauth

                <!-- User Profile & Quick Demo Switcher -->
                <div class="flex items-center space-x-4">
                    @auth
                        <!-- Demo Role Switcher Quick Pill Dropdown -->
                        <div class="relative group hidden sm:block">
                            <button class="bg-blue-950/60 hover:bg-blue-950 text-blue-100 hover:text-white px-3 py-1.5 rounded-lg text-xs font-medium border border-blue-700/50 flex items-center space-x-1.5 transition">
                                <i class="fa-solid fa-arrows-rotate text-blue-300"></i>
                                <span>Ganti Role (Demo)</span>
                                <i class="fa-solid fa-chevron-down text-[10px]"></i>
                            </button>
                            <div class="absolute right-0 mt-1 w-56 bg-white rounded-xl shadow-2xl border border-slate-200 py-1.5 z-50 hidden group-hover:block text-slate-800">
                                <div class="px-3.5 py-2 text-[10px] font-bold text-slate-400 uppercase tracking-wider border-b border-slate-100">
                                    Simulasi Role Sistem:
                                </div>
                                <a href="{{ route('demo.login', 'mahasiswa') }}" class="flex items-center px-3.5 py-2 text-xs hover:bg-blue-50 text-slate-700 hover:text-blue-800 {{ auth()->user()->isMahasiswa() ? 'bg-blue-50 font-bold text-blue-700' : '' }}">
                                    <i class="fa-solid fa-user-graduate w-5 text-blue-600"></i> Mahasiswa
                                </a>
                                <a href="{{ route('demo.login', 'kaprodi') }}" class="flex items-center px-3.5 py-2 text-xs hover:bg-sky-50 text-slate-700 hover:text-sky-800 {{ auth()->user()->isKaprodi() ? 'bg-sky-50 font-bold text-sky-700' : '' }}">
                                    <i class="fa-solid fa-user-tie w-5 text-sky-600"></i> Kaprodi (Ketua Prodi)
                                </a>
                                <a href="{{ route('demo.login', 'dekan') }}" class="flex items-center px-3.5 py-2 text-xs hover:bg-indigo-50 text-slate-700 hover:text-indigo-800 {{ auth()->user()->isDekan() ? 'bg-indigo-50 font-bold text-indigo-700' : '' }}">
                                    <i class="fa-solid fa-award w-5 text-indigo-600"></i> Dekan Fakultas
                                </a>
                                <a href="{{ route('demo.login', 'admin') }}" class="flex items-center px-3.5 py-2 text-xs hover:bg-blue-50 text-slate-700 hover:text-blue-800 {{ auth()->user()->isAdmin() ? 'bg-blue-50 font-bold text-blue-700' : '' }}">
                                    <i class="fa-solid fa-shield-halved w-5 text-blue-600"></i> Administrator (Observer)
                                </a>
                            </div>
                        </div>

                        <!-- User Info -->
                        <div class="flex items-center space-x-2">
                            <div class="w-8 h-8 rounded-full bg-blue-600 border border-blue-400 flex items-center justify-center font-bold text-xs text-white shadow-inner">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </div>
                            <span class="text-xs font-semibold text-blue-100 hidden lg:inline">{{ auth()->user()->name }}</span>
                        </div>

                        <!-- Logout Form -->
                        <form action="{{ route('logout') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="text-blue-200 hover:text-white p-2 rounded-lg hover:bg-blue-700/50 text-xs transition" title="Keluar">
                                <i class="fa-solid fa-right-from-bracket"></i>
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="bg-white text-blue-800 hover:bg-blue-50 px-4 py-2 rounded-lg text-xs font-bold transition shadow">
                            <i class="fa-solid fa-right-to-bracket mr-1"></i> Masuk SSO
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    <!-- Sub-Navbar Navigation (Dikhususkan hanya untuk Administrator sebagai navigasi lintas portal) -->
    @auth
        @if(auth()->user()->isAdmin())
            <nav class="bg-white border-b border-slate-200 shadow-sm">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex space-x-1 sm:space-x-4 py-2.5 overflow-x-auto">
                        <a href="{{ route('admin.dashboard') }}" class="px-3 py-1.5 rounded-lg text-xs font-bold flex items-center space-x-2 {{ request()->routeIs('admin.dashboard*') ? 'bg-blue-100 text-blue-900 ring-1 ring-blue-400' : 'text-slate-700 hover:text-blue-900 hover:bg-slate-100' }}">
                            <i class="fa-solid fa-gauge-high text-blue-600"></i>
                            <span>Dashboard Admin</span>
                        </a>
                        <span class="text-slate-300 self-center hidden sm:inline">|</span>
                        <a href="{{ route('mahasiswa.dashboard') }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold flex items-center space-x-1.5 {{ request()->routeIs('mahasiswa.*') ? 'bg-blue-100 text-blue-900 font-bold' : 'text-slate-600 hover:text-blue-900 hover:bg-blue-50' }}">
                            <i class="fa-solid fa-user-graduate text-blue-600"></i>
                            <span>Portal Mahasiswa</span>
                        </a>
                        <a href="{{ route('kaprodi.dashboard') }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold flex items-center space-x-1.5 {{ request()->routeIs('kaprodi.*', 'dosen.*') ? 'bg-sky-100 text-sky-900 font-bold' : 'text-slate-600 hover:text-sky-900 hover:bg-sky-50' }}">
                            <i class="fa-solid fa-user-tie text-sky-600"></i>
                            <span>Portal Kaprodi</span>
                        </a>
                        <a href="{{ route('dekan.dashboard') }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold flex items-center space-x-1.5 {{ request()->routeIs('dekan.dashboard*') ? 'bg-indigo-100 text-indigo-900 font-bold' : 'text-slate-600 hover:text-indigo-900 hover:bg-indigo-50' }}">
                            <i class="fa-solid fa-building-columns text-indigo-600"></i>
                            <span>Portal Dekan</span>
                        </a>
                        <a href="{{ route('dekan.reports') }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold flex items-center space-x-1.5 {{ request()->routeIs('dekan.reports') ? 'bg-blue-100 text-blue-900 font-bold' : 'text-slate-600 hover:text-blue-900 hover:bg-blue-50' }}">
                            <i class="fa-solid fa-chart-column text-blue-600"></i>
                            <span>Laporan Prestasi</span>
                        </a>
                    </div>
                </div>
            </nav>
        @endif
    @endauth

    <!-- Flash Alerts Container -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4 w-full">
        @if(auth()->check() && auth()->user()->isAdmin() && !request()->routeIs('admin.*'))
            <div class="bg-blue-50 border border-blue-200 text-blue-950 px-4 py-2.5 rounded-xl shadow-sm mb-4 flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-xs">
                <div class="flex items-center space-x-2">
                    <span class="inline-flex items-center justify-center w-5 h-5 rounded-md bg-blue-600 text-white text-[10px] shrink-0">
                        <i class="fa-solid fa-eye"></i>
                    </span>
                    <span>
                        <strong>Mode Peninjauan Administrator (Read-Only):</strong> Anda sedang menjelajahi portal ini dengan hak akses peninjau menyeluruh. Aksi formulir perubahan data dibatasi.
                    </span>
                </div>
                <a href="{{ route('admin.dashboard') }}" class="font-bold text-blue-700 hover:text-blue-900 hover:underline sm:text-right shrink-0">
                    &larr; Kembali ke Dashboard Admin
                </a>
            </div>
        @endif

        @if(session('success'))
            <div class="bg-blue-50 border-l-4 border-blue-600 p-4 rounded-r-xl shadow-sm mb-4 flex items-start space-x-3">
                <i class="fa-solid fa-circle-check text-blue-600 mt-0.5 text-lg"></i>
                <div class="flex-1">
                    <p class="text-sm font-semibold text-blue-900">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-rose-50 border-l-4 border-rose-500 p-4 rounded-r-xl shadow-sm mb-4 flex items-start space-x-3">
                <i class="fa-solid fa-triangle-exclamation text-rose-600 mt-0.5 text-lg"></i>
                <div class="flex-1">
                    <p class="text-sm font-semibold text-rose-800">{{ session('error') }}</p>
                </div>
            </div>
        @endif

        @if($errors->any())
            <div class="bg-rose-50 border-l-4 border-rose-500 p-4 rounded-r-xl shadow-sm mb-4">
                <div class="flex items-center space-x-2 text-rose-800 font-bold text-sm mb-1">
                    <i class="fa-solid fa-circle-xmark"></i>
                    <span>Terdapat kesalahan pengisian data:</span>
                </div>
                <ul class="list-disc list-inside text-xs text-rose-700 space-y-0.5 ml-2">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>

    <!-- Main Content Area -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-6">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200 py-4 text-center text-xs text-slate-500 mt-auto">
        <div class="max-w-7xl mx-auto px-4">
            SIPRES &copy; {{ date('Y') }} — Pusat Data & Sistem Informasi (PDSI) Universitas Nurul Jadid (UNUJA).
        </div>
    </footer>

    @stack('scripts')
</body>
</html>

