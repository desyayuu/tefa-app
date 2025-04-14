<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    public function run()
    {
        $roles = [
            [
                'role_id' => 1,
                'nama_role' => 'Koordinator TEFA',
                'created_at' => now(),
            ],
            [
                'role_id' => 2,
                'nama_role' => 'Project Leader',
                'created_at' => now(),
            ],
            [
                'role_id' => 3,
                'nama_role' => 'Project Member',
                'created_at' => now(),
            ],
            [
                'role_id' => 4,
                'nama_role' => 'Mahasiswa',
                'created_at' => now(),
            ],
            [
                'role_id' => 5,
                'nama_role' => 'Pengunjung',
                'created_at' => now(),
            ]
        ];

        DB::table('r_role')->insert($roles);
    }
}