<?php

namespace Database\Seeders;

use App\Models\Favorite;
use App\Models\User;
use App\Models\Venue;
use Illuminate\Database\Seeder;

class FavoriteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $customers = User::where('role', 'customer')->pluck('id')->toArray();
        $venues    = Venue::where('status', 'active')->pluck('id')->toArray();

        if (empty($customers) || empty($venues)) return;

        $pairs = [
            // [user_index, venue_index]
            [0, 0], [0, 1],
            [1, 0],
            [2, 1],
            [3, 0],
        ];

        foreach ($pairs as [$u, $v]) {
            if (! isset($customers[$u], $venues[$v])) continue;
            Favorite::firstOrCreate([
                'user_id'  => $customers[$u],
                'venue_id' => $venues[$v],
            ]);
        }
    }
}
