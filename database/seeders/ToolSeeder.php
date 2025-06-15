<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ToolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('m_tools')->insert([
            [
                'tool_id' => Str::uuid(),
                'nama_tool' => 'Visual Studio Code',
                'deskripsi_tool' => 'Editor kode sumber yang ringan namun kuat, mendukung berbagai bahasa pemrograman.',
                'created_at' => now(),
                'created_by' => 0,
            ],
            [
                'tool_id' => Str::uuid(),
                'nama_tool' => 'Git',
                'deskripsi_tool' => 'Sistem kontrol versi terdistribusi yang digunakan untuk mengelola kode sumber.',
                'created_at' => now(),
                'created_by' => 0,
            ],
            [
                'tool_id' => Str::uuid(),
                'nama_tool' => 'Docker',
                'deskripsi_tool' => 'Platform untuk mengembangkan, mengirim, dan menjalankan aplikasi dalam kontainer.',
                'created_at' => now(),
                'created_by' => 0,
            ],
            [
                'tool_id' => Str::uuid(),
                'nama_tool' => 'Postman',
                'deskripsi_tool' => 'Alat untuk menguji API dengan antarmuka pengguna yang intuitif.',
                'created_at' => now(),
                'created_by' => 0,
            ],
            [
                'tool_id' => Str::uuid(),
                'nama_tool'=> 'Figma',
                'deskripsi_tool' => 'Alat desain antarmuka pengguna yang memungkinkan kolaborasi secara real-time.',
                'created_at' => now(),
                'created_by' => 0,
            ],
            [
                'tool_id' => Str::uuid(),
                'nama_tool' => 'Jira',
                'deskripsi_tool' => 'Alat manajemen proyek yang digunakan untuk perencanaan, pelacakan, dan pengelolaan proyek perangkat lunak.',
                'created_at' => now(),
                'created_by' => 0,
            ],
            [
                'tool_id' => Str::uuid(),
                'nama_tool' => 'Power BI',
                'deskripsi_tool' => 'Alat analisis bisnis yang memungkinkan pengguna untuk visualisasi data dan berbagi wawasan.',
                'created_at' => now(),
                'created_by' => 0,
            ],
        ]);
    }
}
