<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class TestUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = Carbon::now();
        
        // Buat User Dosen
        $dosenUserId = (string) Str::uuid();
        $dosenId = (string) Str::uuid();
        
        DB::table('d_user')->insert([
            'user_id' => $dosenUserId,
            'email' => 'dosen@example.com',
            'password' => Hash::make('password123'),
            'role' => 'Dosen',
            'created_at' => $now,
            'updated_at' => $now
        ]);
        
        DB::table('d_dosen')->insert([
            'dosen_id' => $dosenId,
            'user_id' => $dosenUserId,
            'nama' => 'Dosen Test',
            'email' => 'dosen@example.com',
            'password' => Hash::make('password123'),
            'jenis_kelamin' => 'Laki-Laki',
            'tanggal_lahir' => '1980-01-01',
            'telepon' => '081234567890',
            'profile_img' => null,
            'nidn' => '1234567890',
            'created_at' => $now,
            'created_by' => 0
        ]);
        
        // Buat User Mahasiswa
        $mahasiswaUserId = (string) Str::uuid();
        $mahasiswaId = (string) Str::uuid();
        $bidangKeahlianId = (string) Str::uuid();
        
        // Bidang Keahlian 
        DB::table('m_bidang_keahlian')->insert([
            'bidang_keahlian_id' => $bidangKeahlianId,
            'nama_bidang' => 'UI/UX Design',
            'deskripsi' => 'Bidang keahlian dalam desain antarmuka pengguna dan pengalaman pengguna.',
            'created_at' => $now,
            'created_by' => 1
        ]);
        
        DB::table('d_user')->insert([
            'user_id' => $mahasiswaUserId,
            'email' => 'mahasiswa@example.com',
            'password' => Hash::make('password123'),
            'role' => 'Mahasiswa',
            'created_at' => $now,
            'updated_at' => $now
        ]);
        
        DB::table('d_mahasiswa')->insert([
            'mahasiswa_id' => $mahasiswaId,
            'user_id' => $mahasiswaUserId,
            'bidang_keahlian_id' => $bidangKeahlianId,
            'nama' => 'Mahasiswa Test',
            'email' => 'mahasiswa@example.com',
            'password' => Hash::make('password123'),
            'jenis_kelamin' => 'Laki-Laki',
            'tanggal_lahir' => '1998-01-01',
            'telepon' => '082345678901',
            'profile_img' => null,
            'nim' => '192011212',
            'linkedin' => 'linkedin.com/in/mahasiswa-test',
            'github' => 'github.com/mahasiswa-test',
            'doc_cv' => null,
            'doc_ktm' => null,
            'doc_ktp' => null,
            'created_at' => $now,
            'created_by' => 1
        ]);
        
        // User Koordinator
        $koordinatorUserId = (string) Str::uuid();
        $koordinatorId = (string) Str::uuid();
        
        DB::table('d_user')->insert([
            'user_id' => $koordinatorUserId,
            'email' => 'koordinator@example.com',
            'password' => Hash::make('password123'),
            'role' => 'Koordinator',
            'created_at' => $now,
            'updated_at' => $now
        ]);
        
        DB::table('d_koordinator')->insert([
            'koordinator_id' => $koordinatorId,
            'user_id' => $koordinatorUserId,
            'nama' => 'Koordinator Test',
            'email' => 'koordinator@example.com',
            'password' => Hash::make('password123'),
            'jenis_kelamin' => 'Laki-Laki',
            'tanggal_lahir' => '1975-01-01',
            'telepon' => '083456789012',
            'profile_img' => null,
            'nidn' => '0987654321',
            'created_at' => $now,
            'created_by' => 1
        ]);
    }
}