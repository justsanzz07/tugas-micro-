<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class UserManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query()->latest('id');

        if (Schema::hasTable('academic_profiles')) {
            $query->with('profile');
        }

        // Filter Pencarian
        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");

                if (Schema::hasColumn('users', 'identifier')) {
                    $q->orWhere('identifier', 'like', "%{$search}%");
                }
            });
        }

        // Filter Peran / Role
        if ($role = $request->query('role')) {
            $query->where('role', $role);
        }

        // Filter Status Aktif
        if ($request->has('status') && $request->query('status') !== '') {
            $query->where('is_active', (bool) $request->query('status'));
        }

        $users = $query->paginate(10)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
            'role' => ['required', 'string'],
            'identifier' => ['nullable', 'string', 'max:50'],
            'fakultas_id' => ['nullable', 'string', 'max:20'],
            'prodi_id' => ['nullable', 'string', 'max:20'],
        ]);

        $roleEnum = UserRole::tryFrom($validated['role']) ?? UserRole::MAHASISWA;

        $userPayload = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $roleEnum,
            'is_active' => true,
        ];

        if (Schema::hasColumn('users', 'identifier')) {
            $userPayload['identifier'] = $validated['identifier'] ?? null;
            $userPayload['fakultas_id'] = $validated['fakultas_id'] ?? null;
            $userPayload['prodi_id'] = $validated['prodi_id'] ?? null;
        }

        $user = User::create($userPayload);

        if (Schema::hasTable('academic_profiles')) {
            DB::table('academic_profiles')->updateOrInsert(
                ['user_id' => $user->id],
                [
                    'identifier_number' => $validated['identifier'] ?? null,
                    'faculty_id' => $validated['fakultas_id'] ?? null,
                    'study_program_id' => $validated['prodi_id'] ?? null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        // Audit Trail
        $this->recordAudit('USER_CREATED', (string) $user->id, ['name' => $user->name, 'email' => $user->email, 'role' => $user->role?->value]);

        return redirect()->route('admin.users.index')->with('success', "Pengguna [{$user->name}] berhasil ditambahkan ke sistem.");
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'role' => ['required', 'string'],
            'identifier' => ['nullable', 'string', 'max:50'],
            'fakultas_id' => ['nullable', 'string', 'max:20'],
            'prodi_id' => ['nullable', 'string', 'max:20'],
            'is_active' => ['required', 'boolean'],
        ]);

        $roleEnum = UserRole::tryFrom($validated['role']) ?? $user->role;

        $userPayload = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $roleEnum,
            'is_active' => (bool) $validated['is_active'],
        ];

        if (Schema::hasColumn('users', 'identifier')) {
            $userPayload['identifier'] = $validated['identifier'] ?? null;
            $userPayload['fakultas_id'] = $validated['fakultas_id'] ?? null;
            $userPayload['prodi_id'] = $validated['prodi_id'] ?? null;
        }

        $user->update($userPayload);

        if (Schema::hasTable('academic_profiles')) {
            DB::table('academic_profiles')->updateOrInsert(
                ['user_id' => $user->id],
                [
                    'identifier_number' => $validated['identifier'] ?? null,
                    'faculty_id' => $validated['fakultas_id'] ?? null,
                    'study_program_id' => $validated['prodi_id'] ?? null,
                    'updated_at' => now(),
                ]
            );
        }

        // Audit Trail
        $this->recordAudit('USER_UPDATED', (string) $user->id, ['name' => $user->name, 'email' => $user->email, 'role' => $user->role?->value, 'is_active' => $user->is_active]);

        return redirect()->route('admin.users.index')->with('success', "Data pengguna [{$user->name}] berhasil diperbarui.");
    }

    public function toggleStatus(Request $request, User $user)
    {
        $user->is_active = ! $user->is_active;
        $user->save();

        $statusStr = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';

        // Audit Trail
        $this->recordAudit('USER_STATUS_TOGGLED', (string) $user->id, ['is_active' => $user->is_active]);

        return redirect()->route('admin.users.index')->with('success', "Akun [{$user->name}] telah {$statusStr}.");
    }

    public function resetPassword(Request $request, User $user)
    {
        $newPass = $request->input('password', 'password');

        $user->update([
            'password' => Hash::make($newPass),
        ]);

        // Audit Trail
        $this->recordAudit('USER_PASSWORD_RESET', (string) $user->id, ['reset_by' => auth()->user()?->name]);

        return redirect()->route('admin.users.index')->with('success', "Kata sandi [{$user->name}] berhasil direset.");
    }

    protected function recordAudit(string $action, string $resourceId, array $payloadAfter = []): void
    {
        try {
            if (Schema::hasTable('audit_logs')) {
                AuditLog::create([
                    'correlation_id' => (string) Str::uuid(),
                    'service_name' => 'sipres_admin',
                    'user_id' => auth()->id(),
                    'user_name' => auth()->user()?->name,
                    'user_role' => 'admin',
                    'action' => $action,
                    'resource_type' => 'user',
                    'resource_id' => $resourceId,
                    'payload_after' => $payloadAfter,
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ]);
            }
        } catch (\Throwable) {
            // Silently continue if audit log table is unavailable
        }
    }
}
