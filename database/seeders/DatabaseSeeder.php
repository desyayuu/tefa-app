<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
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
