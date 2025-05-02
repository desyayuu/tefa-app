<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $now = Carbon::now();
        $koorUserId = (string) Str::uuid();
        $koorId = (string) Str::uuid();

        $dosenUserId = (string) Str::uuid();
        $dosenId = (string) Str::uuid();

        $mahasiswaUserId = (string) Str::uuid();
        $mahasiswaId = (string) Str::uuid();

        $profesionalUserId = (string) Str::uuid();
        $profesionalId = (string) Str::uuid();
        
        DB::table('d_user')->insert([
            'user_id' => $koorUserId,
            'email'=> 'koordinator@example.com',
            'password' => Hash::make('password123'),
            'role' => 'Koordinator',
            'status' => 'Active',
            'created_at' => now(),
            'created_by' => 0,
        ]);
        
        DB::table('d_koordinator')->insert([
            'koordinator_id' => $koorId,
            'user_id' => $koorUserId,
            'nama_koordinator' => 'Koordinator Test',
            'jenis_kelamin_koordinator' => 'Laki-Laki',
            'tanggal_lahir_koordinator' => '1980-01-01',
            'telepon_koordinator' => '081234567890',
            'profile_img_koordinator' => null,
            'nidn_koordinator' => '1234567890',
            'created_at' => $now,
            'created_by' => 0
        ]);

        DB::table('d_user')->insert([
            'user_id' => $dosenUserId,
            'email'=> 'dosen_a@example.com',
            'password' => Hash::make('password123'),
            'role' => 'Dosen',
            'status' => 'Active',
            'created_at' => now(),
            'created_by' => 0,
        ]);

        DB::table('d_dosen')->insert([
            'dosen_id' => $dosenId,
            'user_id' => $dosenUserId,
            'nama_dosen' => 'Dosen Test A',
            'jenis_kelamin_dosen' => 'Laki-Laki',
            'tanggal_lahir_dosen' => '1980-01-01',
            'telepon_dosen' => '081234567890',
            'profile_img_dosen' => null,
            'nidn_dosen' => '1234567890',
            'created_at' => $now,
            'created_by' => 0
        ]);

        DB::table('d_user')->insert([
            'user_id' => $mahasiswaUserId,
            'email'=> 'mahasiswa_a@example.com', 
            'password' => Hash::make('password123'),
            'status' => 'Active',
            'role' => 'Mahasiswa',
            'created_at' => now(),
            'created_by' => 0,
        ]);

        DB::table('d_mahasiswa')->insert([
            'mahasiswa_id' => $mahasiswaId,
            'user_id' => $mahasiswaUserId,
            'nama_mahasiswa' => 'Mahasiswa Test A',
            'jenis_kelamin_mahasiswa' => 'Laki-Laki',
            'tanggal_lahir_mahasiswa' => '2000-01-01',
            'telepon_mahasiswa' => '081234567890',
            'profile_img_mahasiswa' => null,
            'nim_mahasiswa' => '1234567890',
            'created_at' => $now,
            'created_by' => 0
        ]);

        DB::table('d_user')->insert([
            'user_id' => $profesionalUserId,
            'email'=> 'profesional_a@example.com', 
            'password' => Hash::make('password123'),
            'status' => 'Active',
            'role' => 'Profesional',
            'created_at' => now(),
            'created_by' => 0,
        ]);

        DB::table('d_profesional')->insert([
            'profesional_id' => $profesionalId,
            'user_id' => $profesionalUserId,
            'nama_profesional' => 'Profesional Test A',
            'jenis_kelamin_profesional' => 'Laki-Laki',
            'tanggal_lahir_profesional' => '1995-01-01',
            'telepon_profesional' => '081234567891',
            'profile_img_profesional' => null,
            'created_at' => $now,
            'created_by' => 0
        ]);
    }
}
