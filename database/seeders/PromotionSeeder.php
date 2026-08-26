<?php

namespace Database\Seeders;

use App\Models\Promotion;
use App\Models\Venue;
use Illuminate\Database\Seeder;

class PromotionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $venues = Venue::where('status', 'active')->get();
        if ($venues->isEmpty()) return;

        $now = now();

        $promotions = [
            // Venue 1 — Sân Thủ Đức
            ['venue_slug' => 'san-the-thao-thu-duc', 'code' => 'SUMMER28',  'description' => 'Giảm 10% cho đơn từ 300k trong mùa hè.',      'discount_type' => 'percent', 'discount_value' => 10,  'min_amount' => 300000, 'max_uses' => 50,  'used_count' => 12, 'starts_at' => $now->copy()->subDays(10), 'expires_at' => $now->copy()->addDays(20),  'is_active' => true],
            ['venue_slug' => 'san-the-thao-thu-duc', 'code' => 'WEEKEND',   'description' => 'Giảm 50k cho đơn cuối tuần.',                  'discount_type' => 'fixed',   'discount_value' => 50000, 'min_amount' => 200000, 'max_uses' => 30,  'used_count' => 5,  'starts_at' => $now->copy()->subDays(5),  'expires_at' => $now->copy()->addDays(7),   'is_active' => true],
            // Venue 2 — Sân Quận 7
            ['venue_slug' => 'san-cau-long-quan-7', 'code' => 'QL7NEW',    'description' => 'Khuyến mãi khai trương, giảm 15%.',            'discount_type' => 'percent', 'discount_value' => 15,  'min_amount' => null,     'max_uses' => null, 'used_count' => 0,  'starts_at' => $now->copy()->subDays(2),  'expires_at' => $now->copy()->addDays(30),  'is_active' => true],
        ];

        foreach ($promotions as $p) {
            $venue = $venues->firstWhere('slug', $p['venue_slug']);
            if (! $venue) continue;

            unset($p['venue_slug']);
            $p['venue_id'] = $venue->id;

            Promotion::firstOrCreate(
                ['code' => $p['code'], 'venue_id' => $p['venue_id']],
                $p
            );
        }
    }
}
