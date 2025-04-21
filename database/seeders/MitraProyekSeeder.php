<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MitraProyekSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('d_mitra_proyek')->insert([
            [
                'mitra_proyek_id' => Str::uuid(),
                'nama_mitra' => 'Mitra A',
                'telepon_mitra' => '081234567890',
                'email_mitra' => 'mitra_a@example.com',
                'alamat_mitra' => 'Jl. Contoh Alamat A, Kota A',
                'created_at' => now(),
                'created_by' => 0,
            ],
            [
                'mitra_proyek_id' => Str::uuid(),
                'nama_mitra' => 'Mitra B',
                'telepon_mitra' => '081234567891',
                'email_mitra' => 'mitra_b@example.com',
                'alamat_mitra' => 'Jl. Contoh Alamat B, Kota B',
                'created_at' => now(),
                'created_by' => 0,
            ],
            [
                'mitra_proyek_id' => Str::uuid(),
                'nama_mitra' => 'Mitra C',
                'telepon_mitra' => '081234567892',
                'email_mitra' => 'mitra_c@example.com',
                'alamat_mitra' => 'Jl. Contoh Alamat C, Kota C',
                'created_at' => now(),
                'created_by' => 0,
            ],
            [
                'mitra_proyek_id' => Str::uuid(),
                'nama_mitra' => 'Mitra D',
                'telepon_mitra' => '081234567893',
                'email_mitra' => 'mitra_d@example.com',
                'alamat_mitra' => 'Jl. Contoh Alamat D, Kota D',
                'created_at' => now(),
                'created_by' => 0,
            ],
            [
                'mitra_proyek_id' => Str::uuid(),
                'nama_mitra' => 'Mitra E',
                'telepon_mitra' => '081234567892',
                'email_mitra' => 'mitra_e@example.com',
                'alamat_mitra' => 'Jl. Contoh Alamat E, Kota E',
                'created_at' => now(),
                'created_by' => 0,
            ],
        ]);
    }
}
