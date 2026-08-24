<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            SportSeeder::class,  // môn thể thao trước (courts phụ thuộc)
            UserSeeder::class,   // user mẫu 3 role
            VenueSeeder::class,  // venue + courts + slots (phụ thuộc user+sport)
        ]);
    }
}
