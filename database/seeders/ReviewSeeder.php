<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Review;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Chỉ seed review cho các đơn đã hoàn thành (completed)
        $completed = Booking::with(['user', 'court.venue'])
            ->where('status', 'completed')
            ->get();

        if ($completed->isEmpty()) return;

        $reviews = [
            ['booking_code' => 'BOOK-20260820-00001', 'rating' => 5, 'comment' => 'Sân đẹp, vệ sinh tốt, chủ sân nhiệt tình.',                            'is_visible' => true,  'owner_reply' => 'Cảm ơn bạn đã ủng hộ!'],
            ['booking_code' => 'BOOK-20260821-00002', 'rating' => 4, 'comment' => 'Sân bóng chuẩn, chỉ hơi đông lúc giờ vàng.',                         'is_visible' => true,  'owner_reply' => null],
            ['booking_code' => 'BOOK-20260822-00003', 'rating' => 3, 'comment' => 'Đặt sân ổn nhưng khu để xe hơi chật.',                               'is_visible' => true,  'owner_reply' => null],
        ];

        foreach ($reviews as $r) {
            $booking = $completed->firstWhere('code', $r['booking_code']);
            if (! $booking) continue;

            Review::firstOrCreate(
                ['booking_id' => $booking->id],
                [
                    'user_id'     => $booking->user_id,
                    'venue_id'    => $booking->court->venue_id,
                    'rating'      => $r['rating'],
                    'comment'     => $r['comment'],
                    'owner_reply' => $r['owner_reply'],
                    'is_visible'  => $r['is_visible'],
                ]
            );
        }
    }
}
