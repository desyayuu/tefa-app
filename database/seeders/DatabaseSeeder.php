<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            UserSeeder::class,
            JenisProyekSeeder::class,
            MitraProyekSeeder::class,
            BidangKeahlianSeeder::class,
            BahasaPemrogramanSeeder::class,
            ToolSeeder::class,
            JenisDokumenPenunjangSeeder::class,
            KeuanganSeeder::class,
        ]);
    }
}