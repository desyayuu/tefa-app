<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class JenisDokumenPenunjangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \DB::table('m_jenis_dokumen_penunjang')->insert([ 
            [
                'jenis_dokumen_penunjang_id' => \Str::uuid(),
                'nama_jenis_dokumen_penunjang' => 'Manual Book',
                'keterangan_jenis_dokumen_penunjang' => 'Panduan penggunaan sistem atau produk',
                'created_at' => now(),
            ],
            [
                'jenis_dokumen_penunjang_id' => \Str::uuid(),
                'nama_jenis_dokumen_penunjang' => 'Proposal Proyek',
                'keterangan_jenis_dokumen_penunjang' => 'Dokumen perencanaan proyek sebelum dimulai',
                'created_at' => now(),
            ],
            [
                'jenis_dokumen_penunjang_id' => \Str::uuid(),
                'nama_jenis_dokumen_penunjang' => 'Surat Penawaran',
                'keterangan_jenis_dokumen_penunjang' => 'Dokumen penawaran kerja sama atau harga proyek',
                'created_at' => now(),
            ],
            [
                'jenis_dokumen_penunjang_id' => \Str::uuid(),
                'nama_jenis_dokumen_penunjang' => 'Dokumen Kerjasama',
                'keterangan_jenis_dokumen_penunjang' => 'Perjanjian atau MoU antar pihak terkait proyek',
                'created_at' => now(),
            ],
            [
                'jenis_dokumen_penunjang_id' => \Str::uuid(),
                'nama_jenis_dokumen_penunjang' => 'Dokumen BAST',
                'keterangan_jenis_dokumen_penunjang' => 'Berita Acara Serah Terima hasil pekerjaan atau proyek',
                'created_at' => now(),
            ],
            [
                'jenis_dokumen_penunjang_id' => \Str::uuid(),
                'nama_jenis_dokumen_penunjang' => 'Dokumen Teknis',
                'keterangan_jenis_dokumen_penunjang' => 'Spesifikasi teknis sistem atau proyek',
                'created_at' => now(),
            ],
            [
                'jenis_dokumen_penunjang_id' => \Str::uuid(),
                'nama_jenis_dokumen_penunjang' => 'Dokumen Pengujian',
                'keterangan_jenis_dokumen_penunjang' => 'Hasil pengujian sistem atau produk',
                'created_at' => now(),
            ],
            [
                'jenis_dokumen_penunjang_id' => \Str::uuid(),
                'nama_jenis_dokumen_penunjang' => 'Dokumen Lainnya',
                'keterangan_jenis_dokumen_penunjang' => 'Dokumen yang tidak termasuk dalam kategori utama',
                'created_at' => now(),
            ]
        ]);
        
    }
}
