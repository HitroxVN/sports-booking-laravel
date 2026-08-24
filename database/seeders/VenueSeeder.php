<?php

namespace Database\Seeders;

use App\Models\Court;
use App\Models\CourtSlot;
use App\Models\OperatingHour;
use App\Models\Sport;
use App\Models\User;
use App\Models\Venue;
use Illuminate\Database\Seeder;

class VenueSeeder extends Seeder
{
    public function run(): void
    {
        $owner  = User::where('email', 'owner@gmail.com')->first();
        $badminton = Sport::where('name', 'Cầu lông')->first();
        $football  = Sport::where('name', 'Bóng đá')->first();

        $venue = Venue::firstOrCreate(
            ['slug' => 'san-the-thao-thu-duc'],
            [
                'owner_id'    => $owner->id,
                'name'        => 'Sân Thể Thao Thủ Đức',
                'description' => 'Khu sân thể thao hiện đại tại Thủ Đức với đầy đủ tiện nghi.',
                'address'     => '123 Võ Văn Ngân',
                'ward'        => 'Phường Linh Chiểu',
                'district'    => 'Thành phố Thủ Đức',
                'city'        => 'TP. Hồ Chí Minh',
                'phone'       => '0281234567',
                'email'       => 'contact@santhuduc.vn',
                'status'      => 'active',
                'amenities'   => ['wifi', 'parking', 'shower', 'canteen'],
            ]
        );

        // Giờ hoạt động: mở cửa 5h-22h mỗi ngày
        if ($venue->operatingHours()->count() === 0) {
            for ($day = 0; $day <= 6; $day++) {
                OperatingHour::create([
                    'venue_id'     => $venue->id,
                    'day_of_week'  => $day,
                    'open_time'    => '05:00',
                    'close_time'   => '22:00',
                    'is_closed'    => false,
                ]);
            }
        }

        // Sân cầu lông A
        $courtA = Court::firstOrCreate(
            ['venue_id' => $venue->id, 'name' => 'Sân Cầu Lông A'],
            [
                'sport_id'     => $badminton->id,
                'description'  => 'Sân cầu lông tiêu chuẩn, sàn gỗ.',
                'surface_type' => 'wood',
                'max_players'  => 4,
                'status'       => 'active',
            ]
        );

        // Slot giá sân A: 05:00-17:00 giá thường, 17:00-22:00 giờ vàng
        if ($courtA->slots()->count() === 0) {
            CourtSlot::insert([
                ['court_id' => $courtA->id, 'day_of_week' => null, 'start_time' => '05:00', 'end_time' => '17:00', 'price' => 80000,  'peak_price' => null,   'is_peak' => false, 'created_at' => now(), 'updated_at' => now()],
                ['court_id' => $courtA->id, 'day_of_week' => null, 'start_time' => '17:00', 'end_time' => '22:00', 'price' => 120000, 'peak_price' => 120000, 'is_peak' => true,  'created_at' => now(), 'updated_at' => now()],
            ]);
        }

        // Sân bóng đá 5 người
        $courtB = Court::firstOrCreate(
            ['venue_id' => $venue->id, 'name' => 'Sân Bóng Đá Mini'],
            [
                'sport_id'     => $football->id,
                'description'  => 'Sân bóng đá 5 người mặt cỏ nhân tạo.',
                'surface_type' => 'artificial_turf',
                'max_players'  => 10,
                'status'       => 'active',
            ]
        );

        if ($courtB->slots()->count() === 0) {
            CourtSlot::insert([
                ['court_id' => $courtB->id, 'day_of_week' => null, 'start_time' => '05:00', 'end_time' => '17:00', 'price' => 200000, 'peak_price' => null,   'is_peak' => false, 'created_at' => now(), 'updated_at' => now()],
                ['court_id' => $courtB->id, 'day_of_week' => null, 'start_time' => '17:00', 'end_time' => '22:00', 'price' => 350000, 'peak_price' => 350000, 'is_peak' => true,  'created_at' => now(), 'updated_at' => now()],
            ]);
        }
    }
}
