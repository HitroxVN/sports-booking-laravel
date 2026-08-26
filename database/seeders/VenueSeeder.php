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
        $owner       = User::where('email', 'owner@gmail.com')->first();
        $owner2      = User::where('email', 'owner2@gmail.com')->first();
        $owner3      = User::where('email', 'owner3@gmail.com')->first();
        $badminton   = Sport::where('name', 'Cầu lông')->first();
        $football    = Sport::where('name', 'Bóng đá')->first();
        $pickleball  = Sport::where('name', 'Pickleball')->first();

        // ── Venue 1: Đã active (cũ) ──────────────────────────────────
        $venue1 = Venue::firstOrCreate(
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
        $this->createOperatingHours($venue1, '05:00', '22:00');
        $this->createCourtWithSlots($venue1, $badminton, 'Sân Cầu Lông A', 'wood', 4, [
            ['05:00', '17:00', 80000,  false],
            ['17:00', '22:00', 120000, true],
        ]);
        $this->createCourtWithSlots($venue1, $football, 'Sân Bóng Đá Mini', 'artificial_turf', 10, [
            ['05:00', '17:00', 200000, false],
            ['17:00', '22:00', 350000, true],
        ]);

        // ── Venue 2: Active — owner2, Quận 7 ─────────────────────────
        $venue2 = Venue::firstOrCreate(
            ['slug' => 'san-cau-long-quan-7'],
            [
                'owner_id'    => $owner2->id,
                'name'        => 'Sân Cầu Lông Quận 7',
                'description' => 'Hệ thống sân cầu lông đạt chuẩn thi đấu tại Khu đô thị Phú Mỹ Hưng.',
                'address'     => '50 Nguyễn Lương Bằng',
                'ward'        => 'Phường Tân Phú',
                'district'    => 'Quận 7',
                'city'        => 'TP. Hồ Chí Minh',
                'phone'       => '0282244668',
                'email'       => 'info@cauLongquan7.vn',
                'status'      => 'active',
                'amenities'   => ['wifi', 'parking', 'shower', 'air_conditioner'],
            ]
        );
        $this->createOperatingHours($venue2, '06:00', '23:00');
        $this->createCourtWithSlots($venue2, $badminton, 'Sân Cầu Lông 1', 'wood', 4, [
            ['06:00', '18:00', 70000,  false],
            ['18:00', '23:00', 110000, true],
        ]);
        $this->createCourtWithSlots($venue2, $badminton, 'Sân Cầu Lông 2', 'wood', 4, [
            ['06:00', '18:00', 70000,  false],
            ['18:00', '23:00', 110000, true],
        ]);
        $this->createCourtWithSlots($venue2, $pickleball, 'Sân Pickleball', 'concrete', 4, [
            ['06:00', '18:00', 100000, false],
            ['18:00', '23:00', 150000, true],
        ]);

        // ── Venue 3: Pending — chờ admin duyệt ───────────────────────
        Venue::firstOrCreate(
            ['slug' => 'san-bong-da-binh-thanh'],
            [
                'owner_id'    => $owner3->id,
                'name'        => 'Sân Bóng Đá Bình Thạnh',
                'description' => 'Sân bóng đá mini 5-7 người mới xây, đang chờ duyệt.',
                'address'     => '123 Nguyễn Xí',
                'ward'        => 'Phường 26',
                'district'    => 'Bình Thạnh',
                'city'        => 'TP. Hồ Chí Minh',
                'phone'       => '0283366999',
                'email'       => 'binhthanhfc@gmail.com',
                'status'      => 'pending',
                'amenities'   => ['parking', 'shower'],
            ]
        );

        // ── Venue 4: Rejected ────────────────────────────────────────
        Venue::firstOrCreate(
            ['slug' => 'san-the-thao-go-vap'],
            [
                'owner_id'      => $owner3->id,
                'name'          => 'Sân Thể Thao Gò Vấp',
                'description'   => 'Khu sân thể thao tổng hợp.',
                'address'       => '456 Phan Văn Trị',
                'ward'          => 'Phường 10',
                'district'      => 'Gò Vấp',
                'city'          => 'TP. Hồ Chí Minh',
                'phone'         => '0283456789',
                'email'         => 'govap.sports@example.com',
                'status'        => 'rejected',
                'reject_reason' => 'Thiếu giấy phép kinh doanh, vui lòng bổ sung và gửi lại.',
                'amenities'     => ['parking'],
            ]
        );
    }

    // ─── Helpers ─────────────────────────────────────────────────────

    private function createOperatingHours(Venue $venue, string $open, string $close): void
    {
        if ($venue->operatingHours()->count() > 0) return;
        for ($day = 0; $day <= 6; $day++) {
            OperatingHour::create([
                'venue_id'    => $venue->id,
                'day_of_week' => $day,
                'open_time'   => $open,
                'close_time'  => $close,
                'is_closed'   => false,
            ]);
        }
    }

    private function createCourtWithSlots(Venue $venue, $sport, string $name, string $surface, ?int $maxPlayers, array $slots): void
    {
        $court = Court::firstOrCreate(
            ['venue_id' => $venue->id, 'name' => $name],
            [
                'sport_id'     => $sport->id,
                'description'  => "Sân {$name} — {$sport->name}.",
                'surface_type' => $surface,
                'max_players'  => $maxPlayers,
                'status'       => 'active',
            ]
        );

        if ($court->slots()->count() > 0) return;

        $now = now();
        $rows = [];
        foreach ($slots as [$start, $end, $price, $isPeak]) {
            $rows[] = [
                'court_id'   => $court->id,
                'day_of_week' => null,
                'start_time' => $start,
                'end_time'   => $end,
                'price'      => $price,
                'peak_price' => $isPeak ? $price : null,
                'is_peak'    => $isPeak,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }
        CourtSlot::insert($rows);
    }
}
