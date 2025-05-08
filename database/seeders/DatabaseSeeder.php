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
            JenisDokumenPenunjangSeeder::class,
        ]);
    }
}