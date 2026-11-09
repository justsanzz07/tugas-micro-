<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KeuanganRoleTest extends TestCase
{
    use RefreshDatabase;

    protected User $keuangan;

    protected User $mahasiswa;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->keuangan = User::create([
            'name' => 'Biro Keuangan UNUJA',
            'email' => 'keuangan@unuja.ac.id',
            'password' => bcrypt('password'),
            'role' => UserRole::KEUANGAN,
            'identifier' => 'KEU-01',
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

        $this->admin = User::create([
            'name' => 'Administrator SIPRES',
            'email' => 'admin.sipres@unuja.ac.id',
            'password' => bcrypt('password'),
            'role' => UserRole::ADMIN,
            'is_active' => true,
        ]);
    }

    public function test_demo_login_keuangan_redirects_to_keuangan_dashboard(): void
    {
        $response = $this->get(route('demo.login', 'keuangan'));
        $response->assertRedirect(route('keuangan.dashboard'));
        $this->assertAuthenticatedAs($this->keuangan);
    }

    public function test_keuangan_can_access_dashboard_and_reports(): void
    {
        $response = $this->actingAs($this->keuangan)->get(route('keuangan.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Biro Keuangan', false);
        $response->assertSee('Antrean Verifikasi Anggaran', false);
        $response->assertSee('Menunggu Verifikasi', false);
        $response->assertSee('Total Dicairkan', false);

        $responseReports = $this->actingAs($this->keuangan)->get(route('keuangan.reports'));
        $responseReports->assertStatus(200);
        $responseReports->assertSee('Laporan Realisasi Anggaran Prestasi Mahasiswa', false);
    }

    public function test_unauthorized_user_cannot_access_keuangan_dashboard(): void
    {
        $response = $this->actingAs($this->mahasiswa)->get(route('keuangan.dashboard'));
        $response->assertStatus(403);
    }

    public function test_admin_can_access_keuangan_dashboard(): void
    {
        $response = $this->actingAs($this->admin)->get(route('keuangan.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Biro Keuangan', false);
    }
}
