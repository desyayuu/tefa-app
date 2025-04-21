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
        $koordinatorUserId = (string) Str::uuid();
        $koordinatorId = (string) Str::uuid();
        $now = Carbon::now();
        
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