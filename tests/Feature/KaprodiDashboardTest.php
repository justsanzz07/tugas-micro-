<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KaprodiDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected User $kaprodi;

    protected function setUp(): void
    {
        parent::setUp();

        $this->kaprodi = User::create([
            'name' => 'Moh. Iqbal, M.Kom',
            'email' => 'kaprodi@unuja.ac.id',
            'password' => bcrypt('password'),
            'role' => UserRole::KAPRODI,
            'identifier' => '0720048302',
            'fakultas_id' => 'FTI',
            'prodi_id' => 'TI',
            'is_active' => true,
        ]);
    }

    public function test_kaprodi_can_view_redesigned_dashboard(): void
    {
        $response = $this->actingAs($this->kaprodi)->get(route('kaprodi.dashboard'));

        $response->assertStatus(200);

        // Header & Profil
        $response->assertSee('Verifikasi & Penunjukan Dosen Pendamping', false);
        $response->assertSee('Moh. Iqbal, M.Kom', false);
        $response->assertSee('0720048302', false);
        $response->assertSee('WEWENANG KAPRODI', false);

        // 4 Kartu Statistik
        $response->assertSee('Total Pengajuan', false);
        $response->assertSee('Menunggu Verifikasi', false);
        $response->assertSee('Sudah Ditunjuk Dosen', false);
        $response->assertSee('Selesai Review', false);

        // Tab & Toolbar Filter
        $response->assertSee('Antrean Verifikasi', false);
        $response->assertSee('Riwayat Pengesahan', false);
        $response->assertSee('Filter Cepat:', false);
        $response->assertSee('Semua Tingkat', false);
        $response->assertSee('Semua Status', false);
        $response->assertSee('Reset Filter', false);

        // Bagian Riwayat Pengesahan
        $response->assertSee('Riwayat Pengesahan Kaprodi', false);
    }

    public function test_kaprodi_dashboard_filter_parameters_are_accepted(): void
    {
        $response = $this->actingAs($this->kaprodi)->get(route('kaprodi.dashboard', [
            'search' => 'GEMASTIK',
            'level' => 'NASIONAL',
            'supervisor_status' => 'belum',
            'quick' => 'nasional',
        ]));

        $response->assertStatus(200);
        $response->assertSee('GEMASTIK', false);
    }
}
