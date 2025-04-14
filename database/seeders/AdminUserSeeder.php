<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Ramsey\Uuid\Uuid;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Generate UUID
        $userId = Uuid::uuid4()->toString();
        
        // Insert koordinator user
        DB::table('d_user')->insert([
            'user_id' => $userId,
            'nama' => 'Admin TEFA',
            'email' => 'admin@tefa.polinema.ac.id',
            'password' => Hash::make('password123'),
            'jenis_kelamin' => 'Laki-Laki',
            'telepon' => '081234567890',
            'nip' => '12345678',
            'created_at' => now(),
        ]);
        
        // Assign role Koordinator TEFA (role_id = 1)
        DB::table('d_user_role')->insert([
            'user_id' => $userId,
            'role_id' => 1,
        ]);
    }
}

