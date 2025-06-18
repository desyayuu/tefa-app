<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class DosenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $dosenId = (string) Str::uuid();
        $now = Carbon::now();
        
        DB::table('d_dosen')->insert(
            [
                'dosen_id' => $dosenId,
                'user_id' => '3726d3b6-5834-40d0-a08f-6546763064b4',
                'nama' => 'Dosen Test 1',
                'jenis_kelamin' => 'Laki-Laki',
                'tanggal_lahir' => '1980-01-01',
                'telepon' => '081234567890',
                'profile_img' => null,
                'nidn' => '1234567890',
                'created_at' => $now,
                'created_by' => 0
            ], 
            [
                'dosen_id' => $dosenId,
                'user_id' => '35401892-f9c7-4846-8741-c0c05f72a633',
                'nama' => 'Dosen Test 2',
                'jenis_kelamin' => 'Perempuan',
                'tanggal_lahir' => '1985-01-01',
                'telepon' => '081234567891',
                'profile_img' => null,
                'nidn' => '0987654321',
                'created_at' => $now,
                'created_by' => 0
            ], 
            [
                'dosen_id' => $dosenId,
                'user_id' => '8e41d3e7-d6e4-4e30-bb22-d4bb33294367',
                'nama' => 'Dosen Test 3',
                'jenis_kelamin' => 'Laki-Laki',
                'tanggal_lahir' => '1990-01-01',
                'telepon' => '081234567892',
                'profile_img' => null,
                'nidn' => '1122334455',
                'created_at' => $now,
                'created_by' => 0
            ], 
            [
                'dosen_id' => $dosenId,
                'user_id' => '6a01c9c4-3cc2-4746-8f5f-5a9bf1923790',
                'nama' => 'Dosen Test 4',
                'jenis_kelamin' => 'Laki-Laki',
                'tanggal_lahir' => '1990-01-01',
                'telepon' => '081234567892',
                'profile_img' => null,
                'nidn' => '1122334455',
                'created_at' => $now,
                'created_by' => 0
            ], 
            [
                'dosen_id' => $dosenId,
                'user_id' => 'bed744a2-615f-4e0d-a876-c323b27b422a',
                'nama' => 'Dosen Test 5',
                'jenis_kelamin' => 'Laki-Laki',
                'tanggal_lahir' => '1990-01-01',
                'telepon' => '081234567892',
                'profile_img' => null,
                'nidn' => '1122334455',
                'created_at' => $now,
                'created_by' => 0
            ]
    );
    }
}
