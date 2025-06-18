<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;


class BahasaPemrogramanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('m_bahasa_pemrograman')->insert([
            [
                'bahasa_pemrograman_id' => Str::uuid(),
                'nama_bahasa_pemrograman' => 'JavaScript',
                'deskripsi_bahasa_pemrograman' => 'Bahasa pemrograman yang digunakan untuk pengembangan web, baik di sisi klien maupun server.',
                'created_at' => now(),
                'created_by' => 0,
            ],
            [
                'bahasa_pemrograman_id' => Str::uuid(),
                'nama_bahasa_pemrograman' => 'Dart',
                'deskripsi_bahasa_pemrograman' => 'Bahasa pemrograman yang dikembangkan oleh Google, digunakan untuk pengembangan aplikasi mobile dan web dengan framework Flutter.',
                'created_at' => now(),
                'created_by' => 0,
            ],
            [
                'bahasa_pemrograman_id' => Str::uuid(),
                'nama_bahasa_pemrograman' => 'Python',
                'deskripsi_bahasa_pemrograman' => 'Bahasa pemrograman yang populer untuk pengembangan web, analisis data, dan kecerdasan buatan.',
                'created_at' => now(),
                'created_by' => 0,
            ], 
            [
                'bahasa_pemrograman_id' => Str::uuid(),
                'nama_bahasa_pemrograman' => 'Java',
                'deskripsi_bahasa_pemrograman' => 'Bahasa pemrograman yang banyak digunakan untuk pengembangan aplikasi enterprise dan mobile.',
                'created_at' => now(),
                'created_by' => 0,
            ],
            [
                'bahasa_pemrograman_id' => Str::uuid(),
                'nama_bahasa_pemrograman' => 'C#',
                'deskripsi_bahasa_pemrograman' => 'Bahasa pemrograman yang dikembangkan oleh Microsoft, sering digunakan untuk pengembangan aplikasi desktop dan web.',
                'created_at' => now(),
                'created_by' => 0,
            ],
            [
                'bahasa_pemrograman_id' => Str::uuid(),
                'nama_bahasa_pemrograman' => 'C++',
                'deskripsi_bahasa_pemrograman' => 'Bahasa pemrograman yang merupakan pengembangan dari C, sering digunakan dalam pengembangan perangkat lunak sistem dan game.',
                'created_at' => now(),
                'created_by' => 0,
            ],
            [
                'bahasa_pemrograman_id' => Str::uuid(),
                'nama_bahasa_pemrograman' => 'PHP',
                'deskripsi_bahasa_pemrograman' => 'Bahasa pemrograman yang banyak digunakan untuk pengembangan web, terutama di sisi server.',
                'created_at' => now(),
                'created_by' => 0,
            ],
            [
                'bahasa_pemrograman_id' => Str::uuid(),
                'nama_bahasa_pemrograman' => 'Ruby',
                'deskripsi_bahasa_pemrograman' => 'Bahasa pemrograman yang dikenal dengan sintaksisnya yang elegan, sering digunakan dalam pengembangan web dengan framework Ruby on Rails.',
                'created_at' => now(),
                'created_by' => 0,
            ],
            [
                'bahasa_pemrograman_id' => Str::uuid(),
                'nama_bahasa_pemrograman' => 'Go',
                'deskripsi_bahasa_pemrograman' => 'Bahasa pemrograman yang dikembangkan oleh Google, dikenal dengan performa tinggi dan kemudahan dalam pengembangan aplikasi berskala besar.',
                'created_at' => now(),
                'created_by' => 0,
            ],
            [
                'bahasa_pemrograman_id' => Str::uuid(),
                'nama_bahasa_pemrograman' => 'Swift',
                'deskripsi_bahasa_pemrograman' => 'Bahasa pemrograman yang dikembangkan oleh Apple, digunakan untuk pengembangan aplikasi iOS dan macOS.',
                'created_at' => now(),
                'created_by' => 0,
            ],
            [
                'bahasa_pemrograman_id' => Str::uuid(),
                'nama_bahasa_pemrograman' => 'Kotlin',
                'deskripsi_bahasa_pemrograman' => 'Bahasa pemrograman yang dikembangkan oleh JetBrains, digunakan untuk pengembangan aplikasi Android dan interoperabilitas dengan Java.',
                'created_at' => now(),
                'created_by' => 0,
            ],
            [
                'bahasa_pemrograman_id' => Str::uuid(),
                'nama_bahasa_pemrograman' => 'TypeScript',
                'deskripsi_bahasa_pemrograman' => 'Bahasa pemrograman yang merupakan superset dari JavaScript, menambahkan tipe statis untuk meningkatkan pengembangan aplikasi besar.',
                'created_at' => now(),
                'created_by' => 0,
            ],

        ]);
    }
}
