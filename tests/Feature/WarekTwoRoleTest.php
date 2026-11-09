<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WarekTwoRoleTest extends TestCase
{
    use RefreshDatabase;

    protected User $warek2;

    protected User $mahasiswa;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->warek2 = User::create([
            'name' => 'Dr. H. M. Zainuri, M.Kom',
            'email' => 'warek2@unuja.ac.id',
            'password' => bcrypt('password'),
            'role' => UserRole::WAREK_II,
            'identifier' => '0701017503',
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

    public function test_demo_login_warek2_redirects_to_warek2_dashboard(): void
    {
        $response = $this->get(route('demo.login', 'warek2'));
        $response->assertRedirect(route('warek2.dashboard'));
        $this->assertAuthenticatedAs($this->warek2);
    }

    public function test_warek2_can_access_dashboard_and_monitoring_pages(): void
    {
        $response = $this->actingAs($this->warek2)->get(route('warek2.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Wakil Rektor II', false);
        $response->assertSee('Antrean Persetujuan Final Universitas', false);
        $response->assertSee('Menunggu Persetujuan', false);
        $response->assertSee('Plafon Disetujui', false);

        $responseBudget = $this->actingAs($this->warek2)->get(route('warek2.monitoring.budget'));
        $responseBudget->assertStatus(200);
        $responseBudget->assertSee('Monitoring Alokasi', false);

        $responseAchievements = $this->actingAs($this->warek2)->get(route('warek2.monitoring.achievements'));
        $responseAchievements->assertStatus(200);
        $responseAchievements->assertSee('Rekapitulasi Capaian Prestasi Mahasiswa Universitas', false);
    }

    public function test_unauthorized_user_cannot_access_warek2_dashboard(): void
    {
        $response = $this->actingAs($this->mahasiswa)->get(route('warek2.dashboard'));
        $response->assertStatus(403);
    }

    public function test_admin_can_access_warek2_dashboard(): void
    {
        $response = $this->actingAs($this->admin)->get(route('warek2.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Wakil Rektor II', false);
    }

    public function test_legacy_warek_url_redirects_to_warek2(): void
    {
        $response = $this->actingAs($this->warek2)->get('/warek');
        $response->assertRedirect(route('warek2.dashboard'));
    }
}
