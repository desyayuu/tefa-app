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
        $dosenUserId = (string) Str::uuid();
        $dosenId = (string) Str::uuid();
        $now = Carbon::now();

        DB::table('d_user')->insert([
            'user_id' => $dosenUserId,
            'email' => 'dosen@example.com',
            'password' => Hash::make('password123'),
            'role' => 'Dosen',
            'created_at' => $now,
        ]);
        
        DB::table('d_dosen')->insert([
            'dosen_id' => $dosenId,
            'user_id' => $dosenUserId,
            'nama' => 'Dosen Test',
            'jenis_kelamin' => 'Laki-Laki',
            'tanggal_lahir' => '1980-01-01',
            'telepon' => '081234567890',
            'profile_img' => null,
            'nidn' => '1234567890',
            'created_at' => $now,
            'created_by' => 0
        ]);
    }
}
