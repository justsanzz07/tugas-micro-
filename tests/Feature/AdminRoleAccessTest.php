<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Submission\Competition;
use App\Models\Submission\Submission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminRoleAccessTest extends TestCase
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

    public function test_demo_login_admin_redirects_to_admin_dashboard(): void
    {
        $response = $this->get(route('demo.login', 'admin'));
        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($this->admin);
    }

    public function test_admin_can_access_admin_dashboard(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Pusat Kendali & Peninjauan Administrator', false);
        $response->assertSee('Super Observer', false);
    }

    public function test_admin_has_access_to_enter_every_role_dashboard(): void
    {
        // 1. Admin can enter Mahasiswa Dashboard
        $responseMahasiswa = $this->actingAs($this->admin)->get(route('mahasiswa.dashboard'));
        $responseMahasiswa->assertStatus(200);
        $responseMahasiswa->assertSee('Mode Peninjauan Administrator');

        // 2. Admin can enter Kaprodi Dashboard
        $responseKaprodi = $this->actingAs($this->admin)->get(route('kaprodi.dashboard'));
        $responseKaprodi->assertStatus(200);
        $responseKaprodi->assertSee('Mode Peninjauan Administrator');

        // 3. Admin can enter Dekan Dashboard
        $responseDekan = $this->actingAs($this->admin)->get(route('dekan.dashboard'));
        $responseDekan->assertStatus(200);
        $responseDekan->assertSee('Mode Peninjauan Administrator');

        // 4. Admin can enter Dekan Reports
        $responseReports = $this->actingAs($this->admin)->get(route('dekan.reports'));
        $responseReports->assertStatus(200);
    }

    public function test_non_admin_cannot_access_admin_dashboard(): void
    {
        $response = $this->actingAs($this->mahasiswa)->get(route('admin.dashboard'));
        $response->assertStatus(403);
    }

    public function test_admin_is_read_only_and_cannot_mutate_approvals(): void
    {
        $comp = Competition::create([
            'name' => 'Gemastik 2026',
            'organizer' => 'Puspresnas',
            'level' => 'national',
            'category' => 'Software Development',
            'start_date' => now()->addDays(5),
            'end_date' => now()->addDays(10),
            'location_type' => 'offline',
            'venue' => 'Jakarta',
        ]);

        // Create sample submission in kaprodi review
        $submission = Submission::create([
            'competition_id' => $comp->id,
            'title' => 'Lomba Karya Tulis Nasional',
            'submitter_id' => $this->mahasiswa->id,
            'submitter_name' => $this->mahasiswa->name,
            'submitter_nim' => $this->mahasiswa->identifier,
            'study_program_id' => 'TI',
            'study_program_name' => 'Teknologi Informasi',
            'status' => 'submitted',
            'delegation_type' => 'individual',
            'total_budget_requested' => 500000,
        ]);

        // Admin attempts to POST approval to kaprodi
        $responseKaprodi = $this->actingAs($this->admin)
            ->from(route('kaprodi.show', $submission->id))
            ->post(route('kaprodi.process', $submission->id), [
                'action' => 'APPROVE',
                'supervisor_nidn' => '0715088901',
                'supervisor_name' => 'Dr. Muhammad Ilham, M.Kom',
            ]);

        $responseKaprodi->assertRedirect(route('kaprodi.show', $submission->id));
        $responseKaprodi->assertSessionHas('error', 'Administrator hanya memiliki hak akses peninjauan (read-only).');

        // Verify status remains submitted (not modified by admin)
        $this->assertEquals('submitted', $submission->fresh()->status);
    }
}
