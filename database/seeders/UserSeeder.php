<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

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
                'identifier' => '2022010001',
                'fakultas_id' => 'FTI',
                'prodi_id' => 'TI',
                'is_active' => true,
            ],
            [
                'name' => 'Dr. Muhammad Ilham, M.Kom (Dosen Pembimbing)',
                'email' => 'dosen@unuja.ac.id',
                'password' => Hash::make('password'),
                'role' => UserRole::DOSEN,
                'identifier' => '0715088901',
                'fakultas_id' => 'FTI',
                'prodi_id' => 'TI',
                'is_active' => true,
            ],
            [
                'name' => 'Dr. H. Kamiluddin, M.T. (Dekan FTI)',
                'email' => 'dekan@unuja.ac.id',
                'password' => Hash::make('password'),
                'role' => UserRole::DEKAN,
                'identifier' => '0720048102',
                'fakultas_id' => 'FTI',
                'prodi_id' => null,
                'is_active' => true,
            ],
            [
                'name' => 'Dr. KH. Hasan Baharun, M.Pd. (Warek III)',
                'email' => 'warek@unuja.ac.id',
                'password' => Hash::make('password'),
                'role' => UserRole::WAREK,
                'identifier' => '0701017503',
                'fakultas_id' => null,
                'prodi_id' => null,
                'is_active' => true,
            ],
        ];

        foreach ($users as $userData) {
            User::updateOrCreate(
                ['email' => $userData['email']],
                $userData
            );
        }
    }
}
