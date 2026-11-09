<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'Ahmad Fauzi (Mahasiswa)',
                'email' => 'mahasiswa@unuja.ac.id',
                'password' => Hash::make('password'),
                'role' => UserRole::MAHASISWA,
                'identifier_number' => '2022010001',
                'faculty_id' => 'FTI',
                'study_program_id' => 'TI',
                'is_active' => true,
            ],
            [
                'name' => 'Dr. Muhammad Ilham, M.Kom (Dosen Pembimbing)',
                'email' => 'dosen@unuja.ac.id',
                'password' => Hash::make('password'),
                'role' => UserRole::DOSEN,
                'identifier_number' => '0715088901',
                'faculty_id' => 'FTI',
                'study_program_id' => 'TI',
                'is_active' => true,
            ],
            [
                'name' => 'Dr. H. Kamiluddin, M.T. (Dekan FTI)',
                'email' => 'dekan@unuja.ac.id',
                'password' => Hash::make('password'),
                'role' => UserRole::DEKAN,
                'identifier_number' => '0720048102',
                'faculty_id' => 'FTI',
                'study_program_id' => null,
                'is_active' => true,
            ],
            [
                'name' => 'Dr. KH. Hasan Baharun, M.Pd. (Warek)',
                'email' => 'warek@unuja.ac.id',
                'password' => Hash::make('password'),
                'role' => UserRole::WAREK,
                'identifier_number' => '0701017503',
                'faculty_id' => null,
                'study_program_id' => null,
                'is_active' => true,
            ],
            [
                'name' => 'Biro Administrasi Keuangan (BAK)',
                'email' => 'keuangan@unuja.ac.id',
                'password' => Hash::make('password'),
                'role' => UserRole::KEUANGAN,
                'identifier_number' => '0700000001',
                'faculty_id' => null,
                'study_program_id' => null,
                'is_active' => true,
            ],
            [
                'name' => 'Dr. H. Ahmad Fathoni, M.Pd. (Warek II)',
                'email' => 'warek2@unuja.ac.id',
                'password' => Hash::make('password'),
                'role' => UserRole::WAREK_II,
                'identifier_number' => '0701017502',
                'faculty_id' => null,
                'study_program_id' => null,
                'is_active' => true,
            ],
            [
                'name' => 'Administrator SIPRES UNUJA',
                'email' => 'admin.sipres@unuja.ac.id',
                'password' => Hash::make('password'),
                'role' => UserRole::ADMIN,
                'identifier_number' => 'ADM001',
                'faculty_id' => null,
                'study_program_id' => null,
                'is_active' => true,
            ],
        ];

        foreach ($users as $userData) {
            $identifier = $userData['identifier_number'] ?? null;
            $facultyId = $userData['faculty_id'] ?? null;
            $studyProgramId = $userData['study_program_id'] ?? null;
            unset($userData['identifier_number'], $userData['faculty_id'], $userData['study_program_id']);

            $existing = User::where('email', $userData['email'])->first();
            if (! $existing && empty($userData['uuid'])) {
                $userData['uuid'] = (string) Str::uuid();
            }

            $user = User::updateOrCreate(
                ['email' => $userData['email']],
                $userData
            );

            if (Schema::hasTable('academic_profiles')) {
                DB::table('academic_profiles')->updateOrInsert(
                    ['user_id' => $user->id],
                    [
                        'identifier_number' => $identifier ?? '000000',
                        'faculty_id' => $facultyId ?? 'FTI',
                        'faculty_name' => $facultyId ? 'Fakultas Teknologi Informasi' : 'Universitas Nurul Jadid',
                        'study_program_id' => $studyProgramId ?? 'TI',
                        'study_program_name' => $studyProgramId ? 'Informatika' : 'Umum',
                        'phone_number' => '081234567890',
                        'semester' => 5,
                        'academic_year' => '2025/2026',
                        'updated_at' => now(),
                    ]
                );
            }
        }
    }
}
