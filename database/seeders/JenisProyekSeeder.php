<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;



class JenisProyekSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        DB::table('m_jenis_proyek')->insert([
            [
                'jenis_proyek_id' => Str::uuid(),
                'nama_jenis_proyek' => 'Pengembangan Software',
                'deskripsi_jenis_proyek' => 'Kami menghadirkan solusi perangkat lunak yang inovatif dan sesuai kebutuhan bisnis Anda. Dari pembuatan website hingga aplikasi mobile, TEFA JTI Polinema siap membantu meningkatkan efisiensi dan produktivitas perusahaan Anda dengan teknologi terkini.',
                'img_jenis_proyek' => 'section4-laptop.png',
                'created_at' => now(),
                'created_by' => 0,
            ],
            [
                'jenis_proyek_id' => Str::uuid(),
                'nama_jenis_proyek' => 'Konsultan IT',
                'deskripsi_jenis_proyek' => 'Butuh arahan strategis dalam transformasi digital? Tim ahli kami siap memberikan konsultasi dan solusi terbaik dalam perancangan serta implementasi teknologi untuk meningkatkan daya saing bisnis Anda. Kami siap membantu Anda!',
                'img_jenis_proyek' => 'section4-operator.png',
                'created_at' => now(),
                'created_by' => 0,
            ],
            [
                'jenis_proyek_id' => Str::uuid(),
                'nama_jenis_proyek' => 'Instalasi Jaringan',
                'deskripsi_jenis_proyek' => 'Koneksi yang stabil dan aman adalah kunci kelancaran operasional bisnis Anda. Kami menyediakan layanan instalasi dan konfigurasi jaringan yang optimal, baik untuk skala kecil maupun besar.',
                'img_jenis_proyek' => 'section4-world.png',
                'created_at' => now(),
                'created_by' => 0,
            ],
            [
                'jenis_proyek_id' => Str::uuid(),
                'nama_jenis_proyek' => 'Instalasi IOT',
                'deskripsi_jenis_proyek' => 'Tingkatkan efisiensi operasional dengan teknologi Internet of Things (IoT)! TEFA JTI Polinema menyediakan layanan instalasi dan konfigurasi perangkat IoT, membantu industri dalam pemantauan otomatis, kontrol jarak jauh, dan analisis data real-time untuk berbagai kebutuhan bisnis.',
                'img_jenis_proyek' => 'section4-iot.png',
                'created_at' => now(),
                'created_by' => 0,
            ],
            [
                'jenis_proyek_id' => Str::uuid(),
                'nama_jenis_proyek' => 'Pelatihan',
                'deskripsi_jenis_proyek' => 'Kami tidak hanya mengembangkan teknologi, tetapi juga berbagi ilmu! TEFA JTI Polinema menawarkan pelatihan dan workshop dalam berbagai bidang IT. Dirancang untuk mahasiswa, profesional, maupun perusahaan yang ingin meningkatkan kompetensi digitalnya.',
                'img_jenis_proyek' => 'section4-teaching.png',
                'created_at' => now(),
                'created_by' => 0,
            ],
        ]);

    }
}
