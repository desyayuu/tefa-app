<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class KoordinatorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $koordinatorId = (string) Str::uuid();
        $now = Carbon::now();
        
        DB::table('d_koordinator')->insert(
            [
                'koordinator_id' => $koordinatorId,
                'user_id' => '59bcf19b-3b97-4efa-918b-81d9d0995f5e',
                'nama' => 'Koordinator Test 1',
                'jenis_kelamin' => 'Laki-Laki',
                'tanggal_lahir' => '1975-01-01',
                'telepon' => '083456789012',
                'profile_img' => null,
                'nidn' => '0987654321',
                'created_at' => $now,
                'created_by' => 0
            ], 
            [
                'koordinator_id' => $koordinatorId,
                'user_id' => '0d680c1d-6c5d-497a-871f-c34c41a46b6b',
                'nama' => 'Koordinator Test 2',
                'jenis_kelamin' => 'Laki-Laki',
                'tanggal_lahir' => '1975-01-01',
                'telepon' => '083456789012',
                'profile_img' => null,
                'nidn' => '0987654321',
                'created_at' => $now,
                'created_by' => 0
            ]
        );
    }
}