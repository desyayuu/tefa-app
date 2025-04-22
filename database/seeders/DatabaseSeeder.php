<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            BidangKeahlianSeeder::class,
            KoordinatorSeeder::class,
            DosenSeeder::class,
            MahasiswaSeeder::class,
            JenisProyekSeeder::class,
            MitraProyekSeeder::class,
        ]);
    }
}