<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BidangKeahlianSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('m_bidang_keahlian')->insert([
            [
                'bidang_keahlian_id' => Str::uuid(),
                'nama_bidang' => 'Full-Stack Development',
                'deskripsi' => 'Bidang keahlian dalam pengembangan aplikasi dari sisi front-end dan back-end.',
                'created_at' => now(),
                'created_by' => 0,
            ],
            [
                'bidang_keahlian_id' => Str::uuid(),
                'nama_bidang' => 'Mobile Development',
                'deskripsi' => 'Bidang keahlian dalam pengembangan aplikasi mobile.',
                'created_at' => now(),
                'created_by' => 0,
            ],
            [
                'bidang_keahlian_id' => Str::uuid(),
                'nama_bidang' => 'Front-End Development',
                'deskripsi' => 'Bidang keahlian dalam pengembangan aplikasi dari sisi front-end.',
                'created_at' => now(),
                'created_by' => 0,
            ],
            [
                'bidang_keahlian_id' => Str::uuid(),
                'nama_bidang' => 'Back-End Development',
                'deskripsi' => 'Bidang keahlian dalam pengembangan aplikasi dari sisi back-end.',
                'created_at' => now(),
                'created_by' => 0,
            ],
            [
                'bidang_keahlian_id' => Str::uuid(),
                'nama_bidang' => 'UI/UX Design',
                'deskripsi' => 'Bidang keahlian dalam desain antarmuka pengguna dan pengalaman pengguna.',
                'created_at' => now(),
                'created_by' => 0,
            ],
            [
                'bidang_keahlian_id' => Str::uuid(),
                'nama_bidang' => 'System Analyst',
                'deskripsi' => 'Bidang keahlian dalam analisis sistem dan perancangan sistem.',
                'created_at' => now(),
                'created_by' => 0,
            ]
        ]);
    }
}
