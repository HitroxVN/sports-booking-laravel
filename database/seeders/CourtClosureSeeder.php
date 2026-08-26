<?php

namespace Database\Seeders;

use App\Models\Court;
use App\Models\CourtClosure;
use Illuminate\Database\Seeder;

class CourtClosureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $courts = Court::where('status', 'active')->get();
        if ($courts->isEmpty()) return;

        $now = now();

        $closures = [
            // Bảo trì cả ngày, sân 1 (Thủ Đức)
            ['court_index' => 0, 'date' => $now->copy()->addDays(2)->toDateString(),   'start_time' => null,  'end_time' => null,  'reason' => 'Bảo trì định kỳ mặt sân.'],
            // Khóa giờ vàng tối thứ 7, sân 2 (Quận 7) — nếu có index 1
            ['court_index' => 1, 'date' => $now->copy()->addDays(5)->toDateString(),   'start_time' => '18:00', 'end_time' => '22:00', 'reason' => 'Tổ chức giải đấu nội bộ.'],
        ];

        foreach ($closures as $c) {
            $court = $courts->get($c['court_index']);
            if (! $court) continue;

            CourtClosure::firstOrCreate(
                ['court_id' => $court->id, 'date' => $c['date']],
                [
                    'start_time' => $c['start_time'],
                    'end_time'   => $c['end_time'],
                    'reason'     => $c['reason'],
                ]
            );
        }
    }
}
