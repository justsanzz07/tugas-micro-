@extends('layouts.admin')

@section('title', 'Manajemen Pengguna — Admin SIPRES')
@section('breadcrumb', 'Manajemen Pengguna')
@section('header_title', 'Manajemen Akun & Hak Akses Pengguna')

@section('content')
<div class="space-y-6">

    <!-- Header & Button Tambah -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h1 class="text-xl font-extrabold text-[#172554] tracking-tight">Manajemen Pengguna &amp; Peran Sistem</h1>
            <p class="text-xs text-slate-500">Kelola akun pengguna, penugasan role struktural, aktivasi status, dan reset kata sandi.</p>
        </div>
        <button type="button" onclick="openCreateModal()"
            class="inline-flex items-center space-x-2 px-4 py-2.5 rounded-xl text-xs font-bold bg-[#2454C5] hover:bg-[#1d4ed8] text-white shadow-xs transition self-start sm:self-auto">
            <i class="fa-solid fa-user-plus"></i>
            <span>Tambah Pengguna Baru</span>
        </button>
    </div>

    <!-- Filter & Pencarian -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-4 shadow-2xs">
        <form method="GET" action="{{ route('admin.users.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 text-xs">
            <div class="lg:col-span-2">
                <label class="block font-bold text-slate-700 mb-1">Cari Pengguna</label>
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Cari nama, email, atau NIM/NIDN..."
                        class="w-full pl-9 pr-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-[#2454C5]">
                    <i class="fa-solid fa-magnifying-glass absolute left-3 top-2.5 text-slate-400"></i>
                </div>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">Peran / Role</label>
                <select name="role" class="w-full py-2 px-3 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-[#2454C5]">
                    <option value="">Semua Role</option>
                    <option value="mahasiswa" {{ request('role') === 'mahasiswa' ? 'selected' : '' }}>Mahasiswa</option>
                    <option value="kaprodi" {{ request('role') === 'kaprodi' ? 'selected' : '' }}>Kaprodi</option>
                    <option value="dekan" {{ request('role') === 'dekan' ? 'selected' : '' }}>Dekan</option>
                    <option value="keuangan" {{ request('role') === 'keuangan' ? 'selected' : '' }}>Keuangan</option>
                    <option value="warek_ii" {{ request('role') === 'warek_ii' ? 'selected' : '' }}>Wakil Rektor II</option>
                    <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Administrator</option>
                </select>
            </div>

            <div class="flex items-end space-x-2">
                <div class="flex-1">
                    <label class="block font-bold text-slate-700 mb-1">Status Akun</label>
                    <select name="status" class="w-full py-2 px-3 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-[#2454C5]">
                        <option value="">Semua Status</option>
                        <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Aktif</option>
                        <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                </div>
                <button type="submit" class="px-4 py-2 bg-[#2454C5] hover:bg-[#1d4ed8] text-white font-bold rounded-xl text-xs transition shrink-0">
                    <i class="fa-solid fa-filter mr-1"></i> Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Tabel Pengguna -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-2xs overflow-hidden">
        <div class="p-4 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center space-x-2 font-extrabold text-xs text-slate-800 uppercase tracking-wider">
                <i class="fa-solid fa-users text-[#2454C5]"></i>
                <span>Daftar Pengguna Sistem (Total: {{ $users->total() }})</span>
            </div>
            @if(request()->hasAny(['search', 'role', 'status']))
                <a href="{{ route('admin.users.index') }}" class="text-xs text-rose-600 hover:underline font-semibold flex items-center space-x-1">
                    <i class="fa-solid fa-rotate-left text-[10px]"></i>
                    <span>Reset Filter</span>
                </a>
            @endif
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 font-bold uppercase text-[10px] border-b border-slate-200">
                        <th class="py-3 px-4">Pengguna</th>
                        <th class="py-3 px-4">Peran (Role)</th>
                        <th class="py-3 px-4">NIM / NIDN</th>
                        <th class="py-3 px-4">Fakultas / Prodi</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-center">Aksi Manajemen</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($users as $u)
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="py-3.5 px-4">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 rounded-lg bg-blue-100 text-[#2454C5] font-bold text-xs flex items-center justify-center shrink-0">
                                        {{ strtoupper(substr($u->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <span class="font-extrabold text-slate-800 block">{{ $u->name }}</span>
                                        <span class="text-[11px] text-slate-400 block">{{ $u->email }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3.5 px-4">
                                @php
                                    $roleColor = match($u->role?->value ?? (string)$u->role) {
                                        'mahasiswa' => 'bg-blue-50 text-blue-700 border-blue-200',
                                        'kaprodi' => 'bg-sky-50 text-sky-700 border-sky-200',
                                        'dekan' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                                        'keuangan' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                        'warek', 'warek_ii' => 'bg-purple-50 text-purple-700 border-purple-200',
                                        'admin' => 'bg-amber-50 text-amber-700 border-amber-200',
                                        default => 'bg-slate-100 text-slate-700 border-slate-200',
                                    };
                                @endphp
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold border uppercase {{ $roleColor }}">
                                    {{ $u->role?->label() ?? strtoupper((string)$u->role) }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 font-mono font-semibold text-slate-700">
                                {{ $u->identifier ?? '-' }}
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="font-bold text-slate-700 block">{{ $u->fakultas_id ?? '-' }}</span>
                                <span class="text-[11px] text-slate-400">{{ $u->prodi_id ?? '' }}</span>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                @if($u->is_active)
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        Aktif
                                    </span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                        Nonaktif
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <div class="inline-flex items-center space-x-1">
                                    <!-- Edit Button -->
                                    <button type="button"
                                        onclick="openEditModal({{ json_encode([
                                            'id' => $u->id,
                                            'name' => $u->name,
                                            'email' => $u->email,
                                            'role' => $u->role?->value ?? (string)$u->role,
                                            'identifier' => $u->identifier,
                                            'fakultas_id' => $u->fakultas_id,
                                            'prodi_id' => $u->prodi_id,
                                            'is_active' => $u->is_active ? 1 : 0,
                                        ]) }})"
                                        class="p-1.5 rounded-lg text-slate-500 hover:text-blue-700 hover:bg-blue-50 transition" title="Edit Pengguna">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>

                                    <!-- Reset Password Button -->
                                    <button type="button"
                                        onclick="openResetModal('{{ $u->id }}', '{{ $u->name }}')"
                                        class="p-1.5 rounded-lg text-slate-500 hover:text-amber-700 hover:bg-amber-50 transition" title="Reset Password">
                                        <i class="fa-solid fa-key"></i>
                                    </button>

                                    <!-- Toggle Active Form -->
                                    <form action="{{ route('admin.users.toggle', $u) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin mengubah status aktif pengguna ini?')">
                                        @csrf
                                        <button type="submit" class="p-1.5 rounded-lg text-slate-500 hover:text-rose-700 hover:bg-rose-50 transition" title="{{ $u->is_active ? 'Nonaktifkan Akun' : 'Aktifkan Akun' }}">
                                            <i class="fa-solid {{ $u->is_active ? 'fa-user-slash text-rose-500' : 'fa-user-check text-emerald-500' }}"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-3 border-t border-slate-100">
            {{ $users->links() }}
        </div>
    </div>

</div>

<!-- 1. MODAL TAMBAH PENGGUNA BARU -->
<div id="createModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs hidden p-4">
    <div class="bg-white rounded-2xl shadow-xl border border-slate-200 max-w-lg w-full overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between bg-slate-50">
            <h3 class="text-sm font-extrabold text-slate-800 flex items-center space-x-2">
                <i class="fa-solid fa-user-plus text-[#2454C5]"></i>
                <span>Tambah Pengguna Baru</span>
            </h3>
            <button type="button" onclick="closeCreateModal()" class="text-slate-400 hover:text-slate-600">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form action="{{ route('admin.users.store') }}" method="POST" class="p-5 space-y-3.5 text-xs">
            @csrf
            <div>
                <label class="block font-bold text-slate-700 mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
                <input type="text" name="name" required placeholder="Contoh: Muhammad Ali, M.Kom" class="w-full border border-slate-300 rounded-xl p-2.5 focus:ring-2 focus:ring-[#2454C5]">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Email Kampus <span class="text-red-500">*</span></label>
                    <input type="email" name="email" required placeholder="user@unuja.ac.id" class="w-full border border-slate-300 rounded-xl p-2.5 focus:ring-2 focus:ring-[#2454C5]">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Password Awal <span class="text-red-500">*</span></label>
                    <input type="password" name="password" required value="password" class="w-full border border-slate-300 rounded-xl p-2.5 focus:ring-2 focus:ring-[#2454C5]">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Peran / Role <span class="text-red-500">*</span></label>
                    <select name="role" required class="w-full border border-slate-300 rounded-xl p-2.5 focus:ring-2 focus:ring-[#2454C5]">
                        <option value="mahasiswa">Mahasiswa</option>
                        <option value="kaprodi">Kaprodi (Ketua Prodi)</option>
                        <option value="dekan">Dekan Fakultas</option>
                        <option value="keuangan">Keuangan</option>
                        <option value="warek_ii">Wakil Rektor II</option>
                        <option value="admin">Administrator</option>
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">NIM / NIDN / No. Induk</label>
                    <input type="text" name="identifier" placeholder="Contoh: 242140001" class="w-full border border-slate-300 rounded-xl p-2.5 focus:ring-2 focus:ring-[#2454C5]">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Fakultas</label>
                    <select name="fakultas_id" class="w-full border border-slate-300 rounded-xl p-2.5 focus:ring-2 focus:ring-[#2454C5]">
                        <option value="">Tidak Terikat Fakultas</option>
                        <option value="FTI">Fakultas Teknologi Informasi (FTI)</option>
                        <option value="FAI">Fakultas Agama Islam (FAI)</option>
                        <option value="FEB">Fakultas Ekonomi &amp; Bisnis (FEB)</option>
                        <option value="FKES">Fakultas Kesehatan (FKES)</option>
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Program Studi</label>
                    <input type="text" name="prodi_id" placeholder="Contoh: TI / TE / SI" class="w-full border border-slate-300 rounded-xl p-2.5 focus:ring-2 focus:ring-[#2454C5]">
                </div>
            </div>

            <div class="flex items-center justify-end space-x-2 pt-3 border-t border-slate-100">
                <button type="button" onclick="closeCreateModal()" class="px-4 py-2 rounded-xl text-slate-600 hover:bg-slate-100 font-semibold">Batal</button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-[#2454C5] hover:bg-[#1d4ed8] text-white font-bold transition">Simpan Pengguna</button>
            </div>
        </form>
    </div>
</div>

<!-- 2. MODAL EDIT PENGGUNA -->
<div id="editModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs hidden p-4">
    <div class="bg-white rounded-2xl shadow-xl border border-slate-200 max-w-lg w-full overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between bg-slate-50">
            <h3 class="text-sm font-extrabold text-slate-800 flex items-center space-x-2">
                <i class="fa-solid fa-user-pen text-[#2454C5]"></i>
                <span>Edit Pengguna &amp; Peran</span>
            </h3>
            <button type="button" onclick="closeEditModal()" class="text-slate-400 hover:text-slate-600">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form id="editForm" method="POST" class="p-5 space-y-3.5 text-xs">
            @csrf
            @method('PUT')

            <div>
                <label class="block font-bold text-slate-700 mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
                <input type="text" name="name" id="editName" required class="w-full border border-slate-300 rounded-xl p-2.5 focus:ring-2 focus:ring-[#2454C5]">
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">Email Kampus <span class="text-red-500">*</span></label>
                <input type="email" name="email" id="editEmail" required class="w-full border border-slate-300 rounded-xl p-2.5 focus:ring-2 focus:ring-[#2454C5]">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Peran / Role <span class="text-red-500">*</span></label>
                    <select name="role" id="editRole" required class="w-full border border-slate-300 rounded-xl p-2.5 focus:ring-2 focus:ring-[#2454C5]">
                        <option value="mahasiswa">Mahasiswa</option>
                        <option value="kaprodi">Kaprodi</option>
                        <option value="dekan">Dekan Fakultas</option>
                        <option value="keuangan">Keuangan</option>
                        <option value="warek_ii">Wakil Rektor II</option>
                        <option value="admin">Administrator</option>
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">NIM / NIDN</label>
                    <input type="text" name="identifier" id="editIdentifier" class="w-full border border-slate-300 rounded-xl p-2.5 focus:ring-2 focus:ring-[#2454C5]">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Fakultas</label>
                    <select name="fakultas_id" id="editFakultas" class="w-full border border-slate-300 rounded-xl p-2.5 focus:ring-2 focus:ring-[#2454C5]">
                        <option value="">Tidak Terikat</option>
                        <option value="FTI">Fakultas Teknologi Informasi (FTI)</option>
                        <option value="FAI">Fakultas Agama Islam (FAI)</option>
                        <option value="FEB">Fakultas Ekonomi &amp; Bisnis (FEB)</option>
                        <option value="FKES">Fakultas Kesehatan (FKES)</option>
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Program Studi</label>
                    <input type="text" name="prodi_id" id="editProdi" class="w-full border border-slate-300 rounded-xl p-2.5 focus:ring-2 focus:ring-[#2454C5]">
                </div>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">Status Keaktifan</label>
                <select name="is_active" id="editActive" class="w-full border border-slate-300 rounded-xl p-2.5 focus:ring-2 focus:ring-[#2454C5]">
                    <option value="1">Aktif</option>
                    <option value="0">Nonaktif</option>
                </select>
            </div>

            <div class="flex items-center justify-end space-x-2 pt-3 border-t border-slate-100">
                <button type="button" onclick="closeEditModal()" class="px-4 py-2 rounded-xl text-slate-600 hover:bg-slate-100 font-semibold">Batal</button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-[#2454C5] hover:bg-[#1d4ed8] text-white font-bold transition">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<!-- 3. MODAL RESET PASSWORD -->
<div id="resetModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs hidden p-4">
    <div class="bg-white rounded-2xl shadow-xl border border-slate-200 max-w-sm w-full overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between bg-slate-50">
            <h3 class="text-sm font-extrabold text-slate-800 flex items-center space-x-2">
                <i class="fa-solid fa-key text-amber-500"></i>
                <span>Reset Kata Sandi</span>
            </h3>
            <button type="button" onclick="closeResetModal()" class="text-slate-400 hover:text-slate-600">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form id="resetForm" method="POST" class="p-5 space-y-3.5 text-xs">
            @csrf
            <div>
                <p class="text-slate-600 mb-2">Reset password untuk: <strong id="resetUserName" class="text-slate-800"></strong></p>
                <label class="block font-bold text-slate-700 mb-1">Kata Sandi Baru</label>
                <input type="text" name="password" required value="password" class="w-full border border-slate-300 rounded-xl p-2.5 focus:ring-2 focus:ring-amber-500 font-mono">
                <span class="text-[10px] text-slate-400 mt-1 block">Default: password (dapat diganti)</span>
            </div>

            <div class="flex items-center justify-end space-x-2 pt-2 border-t border-slate-100">
                <button type="button" onclick="closeResetModal()" class="px-4 py-2 rounded-xl text-slate-600 hover:bg-slate-100 font-semibold">Batal</button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-bold transition">Reset Password</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openCreateModal() {
        document.getElementById('createModal').classList.remove('hidden');
    }
    function closeCreateModal() {
        document.getElementById('createModal').classList.add('hidden');
    }

    function openEditModal(data) {
        document.getElementById('editForm').action = `/admin/pengguna/${data.id}`;
        document.getElementById('editName').value = data.name;
        document.getElementById('editEmail').value = data.email;
        document.getElementById('editRole').value = data.role;
        document.getElementById('editIdentifier').value = data.identifier || '';
        document.getElementById('editFakultas').value = data.fakultas_id || '';
        document.getElementById('editProdi').value = data.prodi_id || '';
        document.getElementById('editActive').value = data.is_active ? '1' : '0';
        document.getElementById('editModal').classList.remove('hidden');
    }
    function closeEditModal() {
        document.getElementById('editModal').classList.add('hidden');
    }

    function openResetModal(id, name) {
        document.getElementById('resetForm').action = `/admin/pengguna/${id}/reset-password`;
        document.getElementById('resetUserName').textContent = name;
        document.getElementById('resetModal').classList.remove('hidden');
    }
    function closeResetModal() {
        document.getElementById('resetModal').classList.add('hidden');
    }
</script>
@endsection
