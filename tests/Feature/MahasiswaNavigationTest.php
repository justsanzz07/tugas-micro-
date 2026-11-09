<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MahasiswaNavigationTest extends TestCase
{
    use RefreshDatabase;

    protected User $mahasiswa;

    protected User $kaprodi;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mahasiswa = User::create([
            'name' => 'Ahmad Dani Pratama',
            'email' => 'mahasiswa@unuja.ac.id',
            'password' => bcrypt('password'),
            'role' => UserRole::MAHASISWA,
            'identifier' => '242140001',
            'is_active' => true,
        ]);

        $this->kaprodi = User::create([
            'name' => 'Kaprodi Informatika',
            'email' => 'kaprodi@unuja.ac.id',
            'password' => bcrypt('password'),
            'role' => UserRole::KAPRODI,
            'identifier' => '19880101',
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

    public function test_mahasiswa_does_not_see_subnavbar_under_header(): void
    {
        $response = $this->actingAs($this->mahasiswa)->get(route('mahasiswa.dashboard'));

        $response->assertStatus(200);

        // Sub-navbar link harus ditiadakan untuk mahasiswa
        $response->assertDontSee('<span>Dashboard Mahasiswa</span>', false);
        $response->assertDontSee('<span>Ajukan Delegasi Lomba</span>', false);

        // Tombol hero ajukan di dalam konten dashboard mahasiswa tetap ada
        $response->assertSee('Ajukan Delegasi Lomba Baru', false);
    }

    public function test_kaprodi_and_dekan_do_not_see_subnavbar_under_header(): void
    {
        $dekan = User::create([
            'name' => 'Dekan FTI',
            'email' => 'dekan@unuja.ac.id',
            'password' => bcrypt('password'),
            'role' => UserRole::DEKAN,
            'identifier' => '19750101',
            'is_active' => true,
        ]);

        // Kaprodi tidak memiliki sub-navbar strip
        $responseKaprodi = $this->actingAs($this->kaprodi)->get(route('kaprodi.dashboard'));
        $responseKaprodi->assertStatus(200);
        $responseKaprodi->assertDontSee('<nav class="bg-white border-b border-slate-200 shadow-sm">', false);

        // Dekan tidak memiliki sub-navbar strip
        $responseDekan = $this->actingAs($dekan)->get(route('dekan.dashboard'));
        $responseDekan->assertStatus(200);
        $responseDekan->assertDontSee('<nav class="bg-white border-b border-slate-200 shadow-sm">', false);
    }

    public function test_admin_still_sees_subnavbar(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('<nav class="bg-white border-b border-slate-200 shadow-sm">', false);
        $response->assertSee('Portal Mahasiswa', false);
        $response->assertSee('Portal Kaprodi', false);
        $response->assertSee('Portal Dekan', false);
        $response->assertSee('Dashboard Admin', false);
    }

    public function test_mahasiswa_can_view_redesigned_submission_form(): void
    {
        $response = $this->actingAs($this->mahasiswa)->get(route('mahasiswa.submissions.create'));

        $response->assertStatus(200);
        $response->assertSee('Formulir Pengajuan Delegasi Lomba', false);
        $response->assertSee('Progress Pengajuan', false);
        $response->assertSee('Ketentuan Singkat', false);
        $response->assertSee('Informasi Perlombaan', false);
        $response->assertSee('Dosen Pendamping & Susunan Tim', false);
        $response->assertSee('Unggah Dokumen Proposal', false);
        $response->assertSee('Rencana Anggaran Biaya (RAB)', false);
        $response->assertSee('Kirim Pengajuan ke Kaprodi', false);
        $response->assertSee('Total Anggaran Diajukan', false);
        $response->assertSee('Ditentukan Resmi oleh Kaprodi', false);
        $response->assertSee('Nama Perlombaan / Kompetisi', false);
        $response->assertSee('Penyelenggara Kegiatan', false);
        $response->assertSee('Kategori / Cabang Lomba', false);
        $response->assertSee('Tingkat Perlombaan', false);
        $response->assertSee('Tautan / Website Resmi', false);
        $response->assertSee('Tanggal Mulai', false);
        $response->assertSee('Tanggal Selesai', false);
        $response->assertSee('Lokasi Penyelenggaraan', false);
    }
}
