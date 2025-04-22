<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class MahasiswaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $mahasiswaUserId = (string) Str::uuid();
        $mahasiswaId = (string) Str::uuid();
        $now = Carbon::now();
        
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
            'nama' => 'Mahasiswa Test',
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
            'created_by' => 0
        ]);
    }
}
