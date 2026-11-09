<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DekanDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected User $dekan;

    protected function setUp(): void
    {
        parent::setUp();

        $this->dekan = User::create([
            'name' => 'Dr. Achmad Faisol, M.T',
            'email' => 'dekan@unuja.ac.id',
            'password' => bcrypt('password'),
            'role' => UserRole::DEKAN,
            'identifier' => '0715017803',
            'fakultas_id' => 'FTI',
            'prodi_id' => 'TI',
            'is_active' => true,
        ]);
    }

    public function test_dekan_can_view_redesigned_dashboard(): void
    {
        $response = $this->actingAs($this->dekan)->get(route('dekan.dashboard'));

        $response->assertStatus(200);

        // Header Profil Dekan
        $response->assertSee('Dr. Achmad Faisol, M.T', false);
        $response->assertSee('Dekan Fakultas (FTI)', false);
        $response->assertSee('0715017803', false);
        $response->assertSee('Lihat Laporan Prestasi Fakultas', false);

        // 4 Kartu Statistik Dashboard
        $response->assertSee('Antrean Proposal', false);
        $response->assertSee('Antrean LPJ & SKPI', false);
        $response->assertSee('Lomba Aktif', false);
        $response->assertSee('Prestasi Selesai', false);

        // Card 1: Antrean Persetujuan Final
        $response->assertSee('Antrean Persetujuan Final Fakultas & Penetapan Dana', false);
        $response->assertSee('Pengajuan Perlu Validasi', false);

        // Card 2: Partisipasi & Capaian Prestasi
        $response->assertSee('Partisipasi & Capaian Prestasi Mahasiswa Fakultas', false);
        $response->assertSee('Total:', false);
        $response->assertSee('Semua (', false);
        $response->assertSee('Proses (', false);
        $response->assertSee('Disetujui (', false);
        $response->assertSee('Selesai (', false);
        $response->assertSee('Semua Status', false);
    }

    public function test_dekan_dashboard_filter_parameters_are_accepted(): void
    {
        $response = $this->actingAs($this->dekan)->get(route('dekan.dashboard', [
            'tab' => 'proses',
            'search' => 'GEMASTIK',
            'status' => 'review_dekan',
        ]));

        $response->assertStatus(200);
        $response->assertSee('GEMASTIK', false);
    }

    public function test_dekan_can_view_redesigned_reports_page(): void
    {
        $response = $this->actingAs($this->dekan)->get(route('dekan.reports'));

        $response->assertStatus(200);
        $response->assertSee('Rekapitulasi Prestasi Mahasiswa', false);
        $response->assertSee('Total Delegasi', false);
        $response->assertSee('Prestasi Juara', false);
        $response->assertSee('Akumulasi Poin SKPI', false);
        $response->assertSee('Dokumen Resmi Terbit', false);
        $response->assertSee('Cetak / Ekspor Laporan', false);
        $response->assertSee('Semua Tingkat', false);
        $response->assertSee('Kembali ke Dashboard', false);
    }
}
