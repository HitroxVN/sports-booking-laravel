<?php

namespace Database\Seeders;

use App\Models\Sport;
use Illuminate\Database\Seeder;

class SportSeeder extends Seeder
{
    public function run(): void
    {
        $sports = [
            ['name' => 'Cầu lông',   'icon' => null, 'is_active' => true],
            ['name' => 'Bóng đá',    'icon' => null, 'is_active' => true],
            ['name' => 'Pickleball', 'icon' => null, 'is_active' => true],
        ];

        foreach ($sports as $sport) {
            Sport::firstOrCreate(['name' => $sport['name']], $sport);
        }
    }
}
