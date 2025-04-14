<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Ramsey\Uuid\Uuid;

class TestUserSeeder extends Seeder
{
    public function run()
    {
        // Dosen dengan multi role (Project Leader dan Project Member)
        $dosenMultiRoleId = Uuid::uuid4()->toString();
        DB::table('d_user')->insert([
            'user_id' => $dosenMultiRoleId,
            'nama' => 'Dosen Multi Role',
            'email' => 'dosen@tefa.polinema.ac.id',
            'password' => Hash::make('password123'),
            'jenis_kelamin' => 'Laki-Laki',
            'telepon' => '081234567890',
            'nip' => '12345678',
            'created_at' => now(),
        ]);

        DB::table('d_user_role')->insert([
            'user_id' => $dosenMultiRoleId,
            'role_id' => 2,
        ]);

        DB::table('d_user_role')->insert([
            'user_id' => $dosenMultiRoleId,
            'role_id' => 3,
        ]);
        
        // Mahasiswa
        $mahasiswaId = Uuid::uuid4()->toString();
        DB::table('d_user')->insert([
            'user_id' => $mahasiswaId,
            'nama' => 'Mahasiswa Test',
            'email' => 'mahasiswa@tefa.polinema.ac.id',
            'password' => Hash::make('password123'),
            'jenis_kelamin' => 'Laki-Laki',
            'telepon' => '081234567891',
            'nim' => '123456789',
            'created_at' => now(),
        ]);

        DB::table('d_user_role')->insert([
            'user_id' => $mahasiswaId,
            'role_id' => 4,
        ]);
    }
}