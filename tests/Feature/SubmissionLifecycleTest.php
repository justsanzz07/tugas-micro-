<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubmissionLifecycleTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_is_accessible(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('SIPRES UNUJA');
    }

    public function test_demo_login_creates_session_for_roles(): void
    {
        // Seed a mahasiswa
        User::create([
            'name' => 'Ahmad Fauzi',
            'email' => 'mahasiswa@unuja.ac.id',
            'password' => bcrypt('password'),
            'role' => UserRole::MAHASISWA,
            'identifier' => '2022010001',
            'fakultas_id' => 'FTI',
            'prodi_id' => 'TI',
            'is_active' => true,
        ]);

        $response = $this->get('/demo-login/mahasiswa');
        $response->assertRedirect('/mahasiswa/dashboard');
        $this->assertAuthenticated();

        // Seed a kaprodi
        User::create([
            'name' => 'Moh. Iqbal, M.Kom',
            'email' => 'kaprodi@unuja.ac.id',
            'password' => bcrypt('password'),
            'role' => UserRole::KAPRODI,
            'identifier' => '0720048502',
            'fakultas_id' => 'FTI',
            'prodi_id' => 'TI',
            'is_active' => true,
        ]);

        $responseKaprodi = $this->get('/demo-login/kaprodi');
        $responseKaprodi->assertRedirect('/kaprodi/dashboard');
    }

    public function test_role_middleware_prevents_unauthorized_access(): void
    {
        $mahasiswa = User::create([
            'name' => 'Ahmad Fauzi',
            'email' => 'mahasiswa2@unuja.ac.id',
            'password' => bcrypt('password'),
            'role' => UserRole::MAHASISWA,
            'identifier' => '2022010002',
            'is_active' => true,
        ]);

        // Mahasiswa attempts to access Dekan portal -> 403 Forbidden
        $response = $this->actingAs($mahasiswa)->get(route('dekan.dashboard'));
        $response->assertStatus(403);

        // Mahasiswa attempts to access Kaprodi portal -> 403 Forbidden
        $this->actingAs($mahasiswa)->get(route('kaprodi.dashboard'))->assertStatus(403);
    }
}
