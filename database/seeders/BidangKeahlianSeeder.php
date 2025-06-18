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
                'nama_bidang_keahlian' => 'Full-Stack Development',
                'deskripsi_bidang_keahlian' => 'Bidang keahlian dalam pengembangan aplikasi dari sisi front-end dan back-end.',
                'created_at' => now(),
                'created_by' => 0,
            ],
            [
                'bidang_keahlian_id' => Str::uuid(),
                'nama_bidang_keahlian' => 'Mobile Development',
                'deskripsi_bidang_keahlian' => 'Bidang keahlian dalam pengembangan aplikasi mobile.',
                'created_at' => now(),
                'created_by' => 0,
            ],
            [
                'bidang_keahlian_id' => Str::uuid(),
                'nama_bidang_keahlian' => 'Front-End Development',
                'deskripsi_bidang_keahlian' => 'Bidang keahlian dalam pengembangan aplikasi dari sisi front-end.',
                'created_at' => now(),
                'created_by' => 0,
            ],
            [
                'bidang_keahlian_id' => Str::uuid(),
                'nama_bidang_keahlian' => 'Back-End Development',
                'deskripsi_bidang_keahlian' => 'Bidang keahlian dalam pengembangan aplikasi dari sisi back-end.',
                'created_at' => now(),
                'created_by' => 0,
            ],
            [
                'bidang_keahlian_id' => Str::uuid(),
                'nama_bidang_keahlian' => 'UI/UX Design',
                'deskripsi_bidang_keahlian' => 'Bidang keahlian dalam desain antarmuka pengguna dan pengalaman pengguna.',
                'created_at' => now(),
                'created_by' => 0,
            ],
            [
                'bidang_keahlian_id' => Str::uuid(),
                'nama_bidang_keahlian' => 'System Analyst',
                'deskripsi_bidang_keahlian' => 'Bidang keahlian dalam analisis sistem dan perancangan sistem.',
                'created_at' => now(),
                'created_by' => 0,
            ]
        ]);
    }
}
