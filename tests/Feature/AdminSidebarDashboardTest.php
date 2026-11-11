<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminSidebarDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $mahasiswa;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name' => 'Administrator SIPRES',
            'email' => 'admin.sipres@unuja.ac.id',
            'password' => bcrypt('password'),
            'role' => UserRole::ADMIN,
            'is_active' => true,
        ]);

        $this->mahasiswa = User::create([
            'name' => 'Ahmad Dani Pratama',
            'email' => 'mahasiswa@unuja.ac.id',
            'password' => bcrypt('password'),
            'role' => UserRole::MAHASISWA,
            'identifier' => '242140001',
            'is_active' => true,
        ]);
    }

    public function test_admin_can_access_all_five_sidebar_menus(): void
    {
        // 1. Dashboard
        $resDash = $this->actingAs($this->admin)->get(route('admin.dashboard'));
        $resDash->assertStatus(200);
        $resDash->assertSee('Pusat Kendali', false);
        $resDash->assertSee('Total Pengajuan', false);

        // 2. Pengajuan Lomba
        $resSub = $this->actingAs($this->admin)->get(route('admin.submissions.index'));
        $resSub->assertStatus(200);
        $resSub->assertSee('Daftar Seluruh Delegasi', false);

        // 3. Manajemen Pengguna
        $resUsers = $this->actingAs($this->admin)->get(route('admin.users.index'));
        $resUsers->assertStatus(200);
        $resUsers->assertSee('Manajemen Pengguna', false);

        // 4. Audit Trail
        $resAudit = $this->actingAs($this->admin)->get(route('admin.audit.index'));
        $resAudit->assertStatus(200);
        $resAudit->assertSee('Audit Trail', false);

        // 5. Laporan
        $resReports = $this->actingAs($this->admin)->get(route('admin.reports.index'));
        $resReports->assertStatus(200);
        $resReports->assertSee('Laporan Rekapitulasi', false);
    }

    public function test_admin_can_create_user_and_it_records_audit_trail(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.users.store'), [
            'name' => 'Biro Keuangan Baru',
            'email' => 'keuangan.baru@unuja.ac.id',
            'password' => 'password123',
            'role' => 'keuangan',
            'identifier' => 'KEU-99',
            'fakultas_id' => 'FTI',
            'prodi_id' => 'TI',
        ]);

        $response->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseHas('users', ['email' => 'keuangan.baru@unuja.ac.id']);
    }

    public function test_admin_can_toggle_user_status_and_reset_password(): void
    {
        $targetUser = User::create([
            'name' => 'Dosen Uji Coba',
            'email' => 'dosen.uji@unuja.ac.id',
            'password' => bcrypt('oldpassword'),
            'role' => UserRole::KAPRODI,
            'is_active' => true,
        ]);

        // Toggle Status
        $resToggle = $this->actingAs($this->admin)->post(route('admin.users.toggle', $targetUser->id));
        $resToggle->assertRedirect(route('admin.users.index'));
        $this->assertFalse((bool) $targetUser->fresh()->is_active);

        // Reset Password
        $resReset = $this->actingAs($this->admin)->post(route('admin.users.resetPassword', $targetUser->id), [
            'password' => 'newsecretpass',
        ]);
        $resReset->assertRedirect(route('admin.users.index'));
        $this->assertTrue(Hash::check('newsecretpass', $targetUser->fresh()->password));
    }

    public function test_non_admin_cannot_access_new_admin_menus(): void
    {
        $this->actingAs($this->mahasiswa)->get(route('admin.submissions.index'))->assertStatus(403);
        $this->actingAs($this->mahasiswa)->get(route('admin.users.index'))->assertStatus(403);
        $this->actingAs($this->mahasiswa)->get(route('admin.audit.index'))->assertStatus(403);
        $this->actingAs($this->mahasiswa)->get(route('admin.reports.index'))->assertStatus(403);
    }
}
