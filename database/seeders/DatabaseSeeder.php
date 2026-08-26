<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            SportSeeder::class,       // môn thể thao trước (courts phụ thuộc)
            UserSeeder::class,        // user mẫu 3 role
            VenueSeeder::class,       // venue + courts + slots (phụ thuộc user+sport)
            PromotionSeeder::class,   // khuyến mãi (phụ thuộc venue)
            BookingSeeder::class,     // đơn đặt sân (phụ thuộc user + court)
            PaymentSeeder::class,     // thanh toán (phụ thuộc booking)
            ReviewSeeder::class,      // đánh giá (phụ thuộc booking completed)
            FavoriteSeeder::class,    // yêu thích (phụ thuộc user + venue)
            CourtClosureSeeder::class,// lịch khóa sân (phụ thuộc court)
        ]);
    }
}
