<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class KeuanganSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();
        
        // Seed data jenis transaksi
        $jenisTransaksiPemasukan = Str::uuid();
        $jenisTransaksiPengeluaran = Str::uuid();
        
        DB::table('m_jenis_transaksi')->insert([
            [
                'jenis_transaksi_id' => $jenisTransaksiPemasukan,
                'nama_jenis_transaksi' => 'Pemasukan',
                'deskripsi_jenis_transaksi' => 'Segala jenis transaksi yang bersifat pemasukan',
                'created_at' => $now,
                'created_by' => 0,
            ],
            [
                'jenis_transaksi_id' => $jenisTransaksiPengeluaran,
                'nama_jenis_transaksi' => 'Pengeluaran',
                'deskripsi_jenis_transaksi' => 'Segala jenis transaksi yang bersifat pengeluaran',
                'created_at' => $now,
                'created_by' => 0,
            ],
        ]);

        // Seed data jenis keuangan tefa
        $jenisKeuanganProyek = Str::uuid(); 
        $jenisKeuanganNonProyek = Str::uuid();
        
        DB::table('m_jenis_keuangan_tefa')->insert([
            [
                'jenis_keuangan_tefa_id' => $jenisKeuanganProyek,
                'nama_jenis_keuangan_tefa' => 'Proyek',
                'deskripsi_jenis_keuangan_tefa' => 'Segala jenis keuangan yang digunakan untuk proyek',
                'created_at' => $now,
                'created_by' => 0,
            ],
            [
                'jenis_keuangan_tefa_id' => $jenisKeuanganNonProyek, 
                'nama_jenis_keuangan_tefa' => 'Non Proyek',
                'deskripsi_jenis_keuangan_tefa' => 'Segala jenis keuangan yang tidak digunakan untuk proyek',
                'created_at' => $now,
                'created_by' => 0,
            ],
        ]);
        
        // Seed data sub jenis transaksi untuk pengeluaran proyek
        DB::table('m_sub_jenis_transaksi')->insert([
            [
                'sub_jenis_transaksi_id' => Str::uuid(),
                'jenis_transaksi_id' => $jenisTransaksiPengeluaran,
                'jenis_keuangan_tefa_id' => $jenisKeuanganProyek, 
                'nama_sub_jenis_transaksi' => 'HR Developer',
                'deskripsi_sub_jenis_transaksi' => 'Pengeluaran untuk honor developer', 
                'created_at' => $now,
                'created_by' => 0,
            ],
            [
                'sub_jenis_transaksi_id' => Str::uuid(),
                'jenis_transaksi_id' => $jenisTransaksiPengeluaran,
                'jenis_keuangan_tefa_id' => $jenisKeuanganProyek,
                'nama_sub_jenis_transaksi' => 'HR PM',
                'deskripsi_sub_jenis_transaksi' => 'Pengeluaran untuk honor project manager',
                'created_at' => $now,
                'created_by' => 0,
            ],
            [
                'sub_jenis_transaksi_id' => Str::uuid(),
                'jenis_transaksi_id' => $jenisTransaksiPengeluaran,
                'jenis_keuangan_tefa_id' => $jenisKeuanganProyek,
                'nama_sub_jenis_transaksi' => 'ATK',
                'deskripsi_sub_jenis_transaksi' => 'Pengeluaran untuk alat tulis kantor',
                'created_at' => $now,
                'created_by' => 0,
            ],
            [
                'sub_jenis_transaksi_id' => Str::uuid(),
                'jenis_transaksi_id' => $jenisTransaksiPengeluaran,
                'jenis_keuangan_tefa_id' => $jenisKeuanganProyek,
                'nama_sub_jenis_transaksi' => 'Perjalanan Dinas',
                'deskripsi_sub_jenis_transaksi' => 'Pengeluaran untuk perjalanan dinas',
                'created_at' => $now,
                'created_by' => 0,
            ],
            [
                'sub_jenis_transaksi_id' => Str::uuid(),
                'jenis_transaksi_id' => $jenisTransaksiPengeluaran,
                'jenis_keuangan_tefa_id' => $jenisKeuanganProyek,
                'nama_sub_jenis_transaksi' => 'BHP',
                'deskripsi_sub_jenis_transaksi' => 'Pengeluaran untuk bahan habis pakai',
                'created_at' => $now,
                'created_by' => 0,
            ],
            [
                'sub_jenis_transaksi_id' => Str::uuid(),
                'jenis_transaksi_id' => $jenisTransaksiPengeluaran,
                'jenis_keuangan_tefa_id' => $jenisKeuanganProyek,
                'nama_sub_jenis_transaksi' => 'Konsumsi',
                'deskripsi_sub_jenis_transaksi' => 'Pengeluaran untuk konsumsi rapat atau kegiatan',
                'created_at' => $now,
                'created_by' => 0,
            ],
            [
                'sub_jenis_transaksi_id' => Str::uuid(),
                'jenis_transaksi_id' => $jenisTransaksiPengeluaran,
                'jenis_keuangan_tefa_id' => $jenisKeuanganProyek,
                'nama_sub_jenis_transaksi' => 'Kontribusi Jurusan',
                'deskripsi_sub_jenis_transaksi' => 'Pengeluaran untuk kontribusi jurusan',
                'created_at' => $now,
                'created_by' => 0,
            ],
            [
                'sub_jenis_transaksi_id' => Str::uuid(),
                'jenis_transaksi_id' => $jenisTransaksiPengeluaran,
                'jenis_keuangan_tefa_id' => $jenisKeuanganProyek,
                'nama_sub_jenis_transaksi' => 'Hosting',
                'deskripsi_sub_jenis_transaksi' => 'Pengeluaran untuk layanan hosting',
                'created_at' => $now,
                'created_by' => 0,
            ],
            [
                'sub_jenis_transaksi_id' => Str::uuid(),
                'jenis_transaksi_id' => $jenisTransaksiPengeluaran,
                'jenis_keuangan_tefa_id' => $jenisKeuanganProyek,
                'nama_sub_jenis_transaksi' => 'Lainnya',
                'deskripsi_sub_jenis_transaksi' => 'Pengeluaran untuk kategori lainnya',
                'created_at' => $now,
                'created_by' => 0,
            ]
        ]);
        
        DB::table('m_sub_jenis_transaksi')->insert([
            [
                'sub_jenis_transaksi_id' => Str::uuid(),
                'jenis_transaksi_id' => $jenisTransaksiPengeluaran,
                'jenis_keuangan_tefa_id' => $jenisKeuanganNonProyek,
                'nama_sub_jenis_transaksi' => 'Honorarium',
                'deskripsi_sub_jenis_transaksi' => 'Pengeluaran untuk honorarium',
                'created_at' => $now,
                'created_by' => 0,  
            ],
            [
                'sub_jenis_transaksi_id' => Str::uuid(),
                'jenis_transaksi_id' => $jenisTransaksiPengeluaran,
                'jenis_keuangan_tefa_id' => $jenisKeuanganNonProyek,
                'nama_sub_jenis_transaksi' => 'Konsumsi',
                'deskripsi_sub_jenis_transaksi' => 'Pengeluaran untuk konsumsi',
                'created_at' => $now,
                'created_by' => 0,
            ],
            [
                'sub_jenis_transaksi_id' => Str::uuid(),
                'jenis_transaksi_id' => $jenisTransaksiPengeluaran,
                'jenis_keuangan_tefa_id' => $jenisKeuanganNonProyek,
                'nama_sub_jenis_transaksi' => 'Kontribusi Jurusan',
                'deskripsi_sub_jenis_transaksi' => 'Pengeluaran untuk kontribusi jurusan',
                'created_at' => $now,
                'created_by' => 0,
            ],
            [
                'sub_jenis_transaksi_id' => Str::uuid(),
                'jenis_transaksi_id' => $jenisTransaksiPengeluaran,
                'jenis_keuangan_tefa_id' => $jenisKeuanganNonProyek,
                'nama_sub_jenis_transaksi' => 'Hosting',
                'deskripsi_sub_jenis_transaksi' => 'Pengeluaran untuk layanan hosting',
                'created_at' => $now,
                'created_by' => 0,
            ],
            [
                'sub_jenis_transaksi_id' => Str::uuid(),
                'jenis_transaksi_id' => $jenisTransaksiPengeluaran,
                'jenis_keuangan_tefa_id' => $jenisKeuanganNonProyek,
                'nama_sub_jenis_transaksi' => 'Lainnya',
                'deskripsi_sub_jenis_transaksi' => 'Pengeluaran non-proyek lainnya',
                'created_at' => $now,
                'created_by' => 0,
            ], 
            [
                'sub_jenis_transaksi_id' => Str::uuid(),
                'jenis_transaksi_id' => $jenisTransaksiPengeluaran,
                'jenis_keuangan_tefa_id' => $jenisKeuanganNonProyek, 
                'nama_sub_jenis_transaksi' => 'HR Developer',
                'deskripsi_sub_jenis_transaksi' => 'Pengeluaran untuk honor developer', 
                'created_at' => $now,
                'created_by' => 0,
            ],
            [
                'sub_jenis_transaksi_id' => Str::uuid(),
                'jenis_transaksi_id' => $jenisTransaksiPengeluaran,
                'jenis_keuangan_tefa_id' => $jenisKeuanganNonProyek,
                'nama_sub_jenis_transaksi' => 'HR PM',
                'deskripsi_sub_jenis_transaksi' => 'Pengeluaran untuk honor project manager',
                'created_at' => $now,
                'created_by' => 0,
            ],
            [
                'sub_jenis_transaksi_id' => Str::uuid(),
                'jenis_transaksi_id' => $jenisTransaksiPengeluaran,
                'jenis_keuangan_tefa_id' => $jenisKeuanganNonProyek,
                'nama_sub_jenis_transaksi' => 'ATK',
                'deskripsi_sub_jenis_transaksi' => 'Pengeluaran untuk alat tulis kantor',
                'created_at' => $now,
                'created_by' => 0,
            ],
            [
                'sub_jenis_transaksi_id' => Str::uuid(),
                'jenis_transaksi_id' => $jenisTransaksiPengeluaran,
                'jenis_keuangan_tefa_id' => $jenisKeuanganNonProyek,
                'nama_sub_jenis_transaksi' => 'Perjalanan Dinas',
                'deskripsi_sub_jenis_transaksi' => 'Pengeluaran untuk perjalanan dinas',
                'created_at' => $now,
                'created_by' => 0,
            ],
            [
                'sub_jenis_transaksi_id' => Str::uuid(),
                'jenis_transaksi_id' => $jenisTransaksiPengeluaran,
                'jenis_keuangan_tefa_id' => $jenisKeuanganNonProyek,
                'nama_sub_jenis_transaksi' => 'BHP',
                'deskripsi_sub_jenis_transaksi' => 'Pengeluaran untuk bahan habis pakai',
                'created_at' => $now,
                'created_by' => 0,
            ]
        ]);
    }
}