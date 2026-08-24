<?php

namespace Database\Seeders;

use App\Models\Sport;
use Illuminate\Database\Seeder;

class SportSeeder extends Seeder
{
    public function run(): void
    {
        $sports = [
            ['name' => 'Cầu lông',   'icon' => 'badminton',   'is_active' => true],
            ['name' => 'Bóng đá',    'icon' => 'football',    'is_active' => true],
            ['name' => 'Pickleball', 'icon' => 'pickleball',  'is_active' => true],
        ];

        foreach ($sports as $sport) {
            Sport::firstOrCreate(['name' => $sport['name']], $sport);
        }
    }
}
