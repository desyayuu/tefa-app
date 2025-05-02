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
        $mahasiswaId = (string) Str::uuid();
        $now = Carbon::now();
        
        DB::table('d_mahasiswa')->insert(
            [
                'mahasiswa_id' => $mahasiswaId,
                'user_id' => '1bf25f68-a9c3-4c73-8009-097190fd03fa',
                'nama' => 'Mahasiswa Test 1',
                'jenis_kelamin' => 'Laki-Laki',
                'tanggal_lahir' => '1998-01-01',
                'telepon' => '082345678901',
                'profile_img' => null,
                'nim_mahasiswa' => '2141720119',
                'linkedin' => 'linkedin.com/in/mahasiswa-test1',
                'github' => 'github.com/mahasiswa-test1',
                'doc_cv' => null,
                'doc_ktm' => null,
                'doc_ktp' => null,
                'created_at' => $now,
                'created_by' => 0
            ], 
            [
                'mahasiswa_id' => $mahasiswaId,
                'user_id' => '6a0fdb31-5eb8-4f73-8cdb-35af78565701',
                'nama' => 'Mahasiswa Test 2',
                'jenis_kelamin' => 'Perempuan',
                'tanggal_lahir' => '1998-01-01',
                'telepon' => '082345678901',
                'profile_img' => null,
                'nim_mahasiswa' => '2141720120',
                'linkedin' => 'linkedin.com/in/mahasiswa-test2',
                'github' => 'github.com/mahasiswa-test2',
                'doc_cv' => null,
                'doc_ktm' => null,
                'doc_ktp' => null,
                'created_at' => $now,
                'created_by' => 0
            ], 
            [
                'mahasiswa_id' => $mahasiswaId,
                'user_id' => 'fd2f0655-d556-4698-bcaf-d6117701f348',
                'nama' => 'Mahasiswa Test 3',
                'jenis_kelamin' => 'Laki-Laki',
                'tanggal_lahir' => '1998-01-01',
                'telepon' => '082345678901',
                'profile_img' => null,
                'nim_mahasiswa' => '2141720121',
                'linkedin' => 'linkedin.com/in/mahasiswa-test3',
                'github' => 'github.com/mahasiswa-test3',
                'doc_cv' => null,
                'doc_ktm' => null,
                'doc_ktp' => null,
                'created_at' => $now,
                'created_by' => 0
            ]
        );
    }
}
