<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Buat akun dosen/editor awal.
 * Akun dosen tidak dibuat melalui register — hanya lewat seeder ini.
 */
class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $dosens = [
            [
                'name' => 'Dr. Budi Santoso',
                'username' => 'dosen.budi',
                'email' => 'dosen@umpku.ac.id',
                'password' => Hash::make('password'),
                'role' => 'dosen',
            ],
            [
                'name' => 'Prof. Sari Dewi',
                'username' => 'dosen.sari',
                'email' => 'dosen@itspku.ac.id',
                'password' => Hash::make('password'),
                'role' => 'dosen',
            ],
        ];

        foreach ($dosens as $dosen) {
            User::firstOrCreate(['email' => $dosen['email']], $dosen);
        }

        $this->command->info('Akun dosen berhasil dibuat:');
        $this->command->table(
            ['Email', 'Password', 'Role'],
            array_map(fn ($d) => [$d['email'], 'password', $d['role']], $dosens),
        );
    }
}
