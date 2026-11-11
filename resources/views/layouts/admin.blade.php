<!DOCTYPE html>
<html lang="id" class="h-full bg-[#F5F7FB]">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin SIPRES') — Sistem Informasi Pengajuan Lomba & Prestasi UNUJA</title>

    <!-- Google Fonts: Inter & Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Tailwind CSS (CDN with custom enterprise theme) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', '"Plus Jakarta Sans"', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            blue: '#2454C5',
                            navy: '#172554',
                            light: '#EAF2FF',
                            bg: '#F5F7FB',
                            success: '#16A34A',
                            warning: '#F59E0B',
                        }
                    }
                }
            }
        }
    </script>

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        body { font-family: 'Inter', sans-serif; }
        .sidebar-collapsed { width: 76px !important; }
        .sidebar-collapsed .sidebar-label,
        .sidebar-collapsed .sidebar-header-text,
        .sidebar-collapsed .sidebar-badge { display: none !important; }
        .sidebar-collapsed .sidebar-menu-item { justify-content: center !important; padding-left: 0 !important; padding-right: 0 !important; }
    </style>
    @stack('styles')
</head>
<body class="h-full bg-[#F5F7FB] text-slate-800 antialiased flex flex-col">

    <!-- WRAPPER UTAMA DENGAN SIDEBAR KIRI -->
    <div class="min-h-screen flex flex-1 overflow-x-hidden">

        <!-- BACKDROP MOBILE DRAWER -->
        <div id="sidebarBackdrop" onclick="toggleMobileSidebar()" class="fixed inset-0 z-40 bg-slate-900/50 backdrop-blur-xs hidden lg:hidden"></div>

        <!-- 1. SIDEBAR NAVIGATION (KIRI) -->
        <aside id="sidebar" class="fixed top-0 bottom-0 left-0 z-50 w-[260px] bg-white border-r border-slate-200/90 shadow-xs flex flex-col transition-all duration-300 -translate-x-full lg:translate-x-0 lg:static">
            
            <!-- Logo Header -->
            <div class="h-16 px-5 border-b border-slate-100 flex items-center justify-between shrink-0">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-3 group">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-[#2454C5] to-[#172554] text-white flex items-center justify-center font-black text-lg shadow-md shadow-blue-900/20 shrink-0">
                        <i class="fa-solid fa-trophy text-amber-400"></i>
                    </div>
                    <div class="sidebar-header-text overflow-hidden transition-all">
                        <span class="font-extrabold text-base tracking-tight text-[#172554] block leading-none">SIPRES UNUJA</span>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mt-1">Admin Control</span>
                    </div>
                </a>
                <button type="button" onclick="toggleSidebarCollapse()" class="hidden lg:flex w-7 h-7 rounded-lg text-slate-400 hover:text-[#2454C5] hover:bg-[#EAF2FF] items-center justify-center transition" title="Ciutkan / Lebarkan Sidebar">
                    <i class="fa-solid fa-angles-left text-xs" id="collapseIcon"></i>
                </button>
            </div>

            <!-- Role Badge Banner -->
            <div class="px-4 py-3 mx-3 my-3 rounded-xl bg-gradient-to-r from-blue-50 to-indigo-50/70 border border-blue-100/80 sidebar-label">
                <div class="flex items-center space-x-2.5">
                    <div class="w-7 h-7 rounded-lg bg-[#2454C5] text-white flex items-center justify-center text-xs shrink-0 font-bold">
                        <i class="fa-solid fa-user-shield"></i>
                    </div>
                    <div class="truncate">
                        <div class="text-[10px] font-bold uppercase tracking-wider text-blue-800">Peran Sistem</div>
                        <div class="text-xs font-black text-[#172554] truncate">Administrator Pusat</div>
                    </div>
                </div>
            </div>

            <!-- 5 Menu Utama Sidebar Navigation -->
            <nav class="flex-1 px-3 py-2 space-y-1 overflow-y-auto">
                <div class="px-3 pb-1 text-[10px] font-bold uppercase tracking-wider text-slate-400 sidebar-label">
                    Menu Utama
                </div>

                <!-- 1. Dashboard -->
                <a href="{{ route('admin.dashboard') }}"
                    class="sidebar-menu-item flex items-center px-3 py-2.5 rounded-xl text-xs font-semibold transition-all group {{ request()->routeIs('admin.dashboard*') ? 'bg-[#EAF2FF] text-[#2454C5] font-extrabold border-l-4 border-[#2454C5]' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                    <span class="w-8 flex items-center justify-center shrink-0 text-base {{ request()->routeIs('admin.dashboard*') ? 'text-[#2454C5]' : 'text-slate-400 group-hover:text-slate-700' }}">
                        <i class="fa-solid fa-gauge-high"></i>
                    </span>
                    <span class="sidebar-label ml-2 truncate">Dashboard</span>
                </a>

                <!-- 2. Pengajuan Lomba -->
                <a href="{{ route('admin.submissions.index') }}"
                    class="sidebar-menu-item flex items-center px-3 py-2.5 rounded-xl text-xs font-semibold transition-all group {{ request()->routeIs('admin.submissions*') ? 'bg-[#EAF2FF] text-[#2454C5] font-extrabold border-l-4 border-[#2454C5]' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                    <span class="w-8 flex items-center justify-center shrink-0 text-base {{ request()->routeIs('admin.submissions*') ? 'text-[#2454C5]' : 'text-slate-400 group-hover:text-slate-700' }}">
                        <i class="fa-solid fa-file-invoice"></i>
                    </span>
                    <span class="sidebar-label ml-2 truncate">Pengajuan Lomba</span>
                </a>

                <!-- 3. Manajemen Pengguna -->
                <a href="{{ route('admin.users.index') }}"
                    class="sidebar-menu-item flex items-center px-3 py-2.5 rounded-xl text-xs font-semibold transition-all group {{ request()->routeIs('admin.users*') ? 'bg-[#EAF2FF] text-[#2454C5] font-extrabold border-l-4 border-[#2454C5]' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                    <span class="w-8 flex items-center justify-center shrink-0 text-base {{ request()->routeIs('admin.users*') ? 'text-[#2454C5]' : 'text-slate-400 group-hover:text-slate-700' }}">
                        <i class="fa-solid fa-users-gear"></i>
                    </span>
                    <span class="sidebar-label ml-2 truncate">Manajemen Pengguna</span>
                </a>

                <!-- 4. Audit Trail -->
                <a href="{{ route('admin.audit.index') }}"
                    class="sidebar-menu-item flex items-center px-3 py-2.5 rounded-xl text-xs font-semibold transition-all group {{ request()->routeIs('admin.audit*') ? 'bg-[#EAF2FF] text-[#2454C5] font-extrabold border-l-4 border-[#2454C5]' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                    <span class="w-8 flex items-center justify-center shrink-0 text-base {{ request()->routeIs('admin.audit*') ? 'text-[#2454C5]' : 'text-slate-400 group-hover:text-slate-700' }}">
                        <i class="fa-solid fa-clock-rotate-left"></i>
                    </span>
                    <span class="sidebar-label ml-2 truncate">Audit Trail</span>
                </a>

                <!-- 5. Laporan -->
                <a href="{{ route('admin.reports.index') }}"
                    class="sidebar-menu-item flex items-center px-3 py-2.5 rounded-xl text-xs font-semibold transition-all group {{ request()->routeIs('admin.reports*') ? 'bg-[#EAF2FF] text-[#2454C5] font-extrabold border-l-4 border-[#2454C5]' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                    <span class="w-8 flex items-center justify-center shrink-0 text-base {{ request()->routeIs('admin.reports*') ? 'text-[#2454C5]' : 'text-slate-400 group-hover:text-slate-700' }}">
                        <i class="fa-solid fa-chart-pie"></i>
                    </span>
                    <span class="sidebar-label ml-2 truncate">Laporan</span>
                </a>
            </nav>

            <!-- Bottom User & Logout -->
            <div class="p-3 border-t border-slate-100 shrink-0">
                <div class="flex items-center justify-between p-2 rounded-xl bg-slate-50">
                    <div class="flex items-center space-x-2.5 overflow-hidden">
                        <div class="w-8 h-8 rounded-lg bg-[#2454C5] text-white flex items-center justify-center font-bold text-xs shrink-0">
                            {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                        </div>
                        <div class="sidebar-label truncate">
                            <span class="text-xs font-bold text-slate-800 block truncate leading-tight">{{ auth()->user()->name }}</span>
                            <span class="text-[10px] text-slate-400 block truncate">admin@unuja.ac.id</span>
                        </div>
                    </div>
                    <form action="{{ route('logout') }}" method="POST" class="inline shrink-0">
                        @csrf
                        <button type="submit" class="w-7 h-7 rounded-lg text-slate-400 hover:text-red-600 hover:bg-red-50 flex items-center justify-center text-xs transition" title="Keluar">
                            <i class="fa-solid fa-arrow-right-from-bracket"></i>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- 2. MAIN CONTENT AREA (KANAN) -->
        <div class="flex-1 flex flex-col min-w-0 overflow-y-auto">

            <!-- TOPBAR ENTERPRISE -->
            <header class="bg-white border-b border-slate-200 sticky top-0 z-30 shadow-2xs">
                <div class="px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between gap-4">
                    
                    <!-- Left: Hamburger & Breadcrumb -->
                    <div class="flex items-center space-x-3">
                        <button type="button" onclick="toggleMobileSidebar()" class="lg:hidden w-9 h-9 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-100 flex items-center justify-center text-sm">
                            <i class="fa-solid fa-bars"></i>
                        </button>
                        
                        <div>
                            <div class="flex items-center space-x-1.5 text-xs text-slate-400">
                                <span>Admin Panel</span>
                                <i class="fa-solid fa-chevron-right text-[10px] text-slate-300"></i>
                                <span class="font-bold text-[#2454C5]">@yield('breadcrumb', 'Dashboard')</span>
                            </div>
                            <h2 class="text-sm font-extrabold text-[#172554] tracking-tight hidden sm:block">
                                @yield('header_title', 'Pusat Kendali Administrator')
                            </h2>
                        </div>
                    </div>

                    <!-- Right: Badge, Quick Role Switcher, Notification, Profile -->
                    <div class="flex items-center space-x-2 sm:space-x-3">
                        
                        <!-- Status Server Indicator -->
                        <div class="hidden md:flex items-center space-x-1.5 px-2.5 py-1 rounded-full bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-semibold">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span>Live Monitor</span>
                        </div>

                        <!-- Administrator Badge -->
                        <span class="hidden sm:inline-flex items-center px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-blue-50 text-[#2454C5] border border-blue-200">
                            <i class="fa-solid fa-shield-halved mr-1.5"></i> Super Observer
                        </span>

                        <!-- Ganti Role (Demo) Dropdown -->
                        <div class="relative group">
                            <button class="bg-slate-100 hover:bg-blue-50 text-slate-700 hover:text-[#2454C5] px-3 py-1.5 rounded-xl text-xs font-bold border border-slate-200 flex items-center space-x-1.5 transition">
                                <i class="fa-solid fa-arrows-rotate text-[#2454C5]"></i>
                                <span class="hidden sm:inline">Ganti Role (Demo)</span>
                                <i class="fa-solid fa-chevron-down text-[10px]"></i>
                            </button>
                            <div class="absolute right-0 mt-1 w-56 bg-white rounded-2xl shadow-xl border border-slate-200 py-2 z-50 hidden group-hover:block text-slate-800">
                                <div class="px-3.5 py-1.5 text-[10px] font-bold text-slate-400 uppercase tracking-wider border-b border-slate-100">
                                    Simulasi Multi-Role:
                                </div>
                                <a href="{{ route('demo.login', 'mahasiswa') }}" class="flex items-center px-3.5 py-2 text-xs hover:bg-blue-50 text-slate-700 hover:text-blue-700 font-medium">
                                    <i class="fa-solid fa-user-graduate w-5 text-blue-600"></i> Mahasiswa
                                </a>
                                <a href="{{ route('demo.login', 'kaprodi') }}" class="flex items-center px-3.5 py-2 text-xs hover:bg-sky-50 text-slate-700 hover:text-sky-700 font-medium">
                                    <i class="fa-solid fa-user-tie w-5 text-sky-600"></i> Kaprodi
                                </a>
                                <a href="{{ route('demo.login', 'dekan') }}" class="flex items-center px-3.5 py-2 text-xs hover:bg-indigo-50 text-slate-700 hover:text-indigo-700 font-medium">
                                    <i class="fa-solid fa-award w-5 text-indigo-600"></i> Dekan Fakultas
                                </a>
                                <a href="{{ route('demo.login', 'keuangan') }}" class="flex items-center px-3.5 py-2 text-xs hover:bg-emerald-50 text-slate-700 hover:text-emerald-700 font-medium">
                                    <i class="fa-solid fa-calculator w-5 text-emerald-600"></i> Biro Keuangan
                                </a>
                                <a href="{{ route('demo.login', 'warek2') }}" class="flex items-center px-3.5 py-2 text-xs hover:bg-purple-50 text-slate-700 hover:text-purple-700 font-medium">
                                    <i class="fa-solid fa-crown w-5 text-purple-600"></i> Wakil Rektor II
                                </a>
                                <a href="{{ route('demo.login', 'admin') }}" class="flex items-center px-3.5 py-2 text-xs bg-blue-50 text-[#2454C5] font-bold">
                                    <i class="fa-solid fa-shield-halved w-5 text-[#2454C5]"></i> Administrator (Active)
                                </a>
                            </div>
                        </div>

                        <!-- Logout Button -->
                        <form action="{{ route('logout') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="w-9 h-9 rounded-xl border border-slate-200 text-slate-500 hover:text-red-600 hover:bg-red-50 flex items-center justify-center text-xs transition" title="Keluar">
                                <i class="fa-solid fa-right-from-bracket"></i>
                            </button>
                        </form>
                    </div>

                </div>

                <!-- SUB-NAVBAR KONSISTEN (Diperlukan untuk Akses Lintas Portal & Uji Kompatibilitas Sistem) -->
                <nav class="bg-white border-b border-slate-200 shadow-sm">
                    <div class="px-4 sm:px-6 lg:px-8">
                        <div class="flex space-x-1 sm:space-x-3 py-2 overflow-x-auto text-xs font-semibold">
                            <a href="{{ route('admin.dashboard') }}" class="px-2.5 py-1 rounded-lg flex items-center space-x-1.5 {{ request()->routeIs('admin.dashboard*') ? 'bg-[#EAF2FF] text-[#2454C5] font-bold' : 'text-slate-600 hover:bg-slate-100' }}">
                                <i class="fa-solid fa-gauge-high text-[#2454C5]"></i>
                                <span>Dashboard Admin</span>
                            </a>
                            <span class="text-slate-300 self-center hidden sm:inline">|</span>
                            <span class="text-[11px] text-slate-400 self-center hidden md:inline">Mode Monitoring Read-Only:</span>
                            <a href="{{ route('mahasiswa.dashboard') }}" class="px-2.5 py-1 rounded-lg flex items-center space-x-1 text-slate-600 hover:text-blue-700 hover:bg-blue-50 transition">
                                <i class="fa-solid fa-user-graduate text-blue-600"></i>
                                <span>Portal Mahasiswa</span>
                            </a>
                            <a href="{{ route('kaprodi.dashboard') }}" class="px-2.5 py-1 rounded-lg flex items-center space-x-1 text-slate-600 hover:text-sky-700 hover:bg-sky-50 transition">
                                <i class="fa-solid fa-user-tie text-sky-600"></i>
                                <span>Portal Kaprodi</span>
                            </a>
                            <a href="{{ route('dekan.dashboard') }}" class="px-2.5 py-1 rounded-lg flex items-center space-x-1 text-slate-600 hover:text-indigo-700 hover:bg-indigo-50 transition">
                                <i class="fa-solid fa-building-columns text-indigo-600"></i>
                                <span>Portal Dekan</span>
                            </a>
                            <a href="{{ route('keuangan.dashboard') }}" class="px-2.5 py-1 rounded-lg flex items-center space-x-1 text-slate-600 hover:text-emerald-700 hover:bg-emerald-50 transition">
                                <i class="fa-solid fa-calculator text-emerald-600"></i>
                                <span>Portal Keuangan</span>
                            </a>
                            <a href="{{ route('warek2.dashboard') }}" class="px-2.5 py-1 rounded-lg flex items-center space-x-1 text-slate-600 hover:text-purple-700 hover:bg-purple-50 transition">
                                <i class="fa-solid fa-crown text-purple-600"></i>
                                <span>Portal Warek II</span>
                            </a>
                        </div>
                    </div>
                </nav>
            </header>

            <!-- FLASH ALERTS -->
            <div class="px-4 sm:px-6 lg:px-8 mt-4 w-full">
                @if(session('success'))
                    <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-2xl shadow-xs mb-4 flex items-center space-x-3 text-xs">
                        <i class="fa-solid fa-circle-check text-emerald-600 text-base shrink-0"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                @if(session('error'))
                    <div class="bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-2xl shadow-xs mb-4 flex items-center space-x-3 text-xs">
                        <i class="fa-solid fa-circle-xmark text-rose-600 text-base shrink-0"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif
            </div>

            <!-- MAIN CONTENT WRAPPER -->
            <main class="flex-1 px-4 sm:px-6 lg:px-8 py-6">
                @yield('content')
            </main>

            <!-- FOOTER -->
            <footer class="bg-white border-t border-slate-200 py-4 px-6 text-center text-xs text-slate-500">
                <div class="flex flex-col sm:flex-row items-center justify-between gap-2">
                    <span>SIPRES &copy; 2026 &mdash; Pusat Data &amp; Sistem Informasi (PDSI) Universitas Nurul Jadid (UNUJA).</span>
                    <span class="text-slate-400">Arsitektur Multi-Database &amp; Enterprise Governance</span>
                </div>
            </footer>

        </div>

    </div>

    <!-- SCRIPT SIDEBAR TOGGLE & LUCIDE -->
    <script>
        function toggleMobileSidebar() {
            const sidebar = document.getElementById('sidebar');
            const backdrop = document.getElementById('sidebarBackdrop');
            sidebar.classList.toggle('-translate-x-full');
            backdrop.classList.toggle('hidden');
        }

        function toggleSidebarCollapse() {
            const sidebar = document.getElementById('sidebar');
            const icon = document.getElementById('collapseIcon');
            sidebar.classList.toggle('sidebar-collapsed');
            if (sidebar.classList.contains('sidebar-collapsed')) {
                icon.classList.remove('fa-angles-left');
                icon.classList.add('fa-angles-right');
            } else {
                icon.classList.remove('fa-angles-right');
                icon.classList.add('fa-angles-left');
            }
        }

        // Inisialisasi ikon Lucide jika ada
        document.addEventListener('DOMContentLoaded', function() {
            if (window.lucide) {
                window.lucide.createIcons();
            }
        });
    </script>
    @stack('scripts')
</body>
</html>
