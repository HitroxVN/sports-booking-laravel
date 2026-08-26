<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Court;
use App\Models\User;
use Illuminate\Database\Seeder;

class BookingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $customers = User::where('role', 'customer')->pluck('id')->toArray();
        $courts    = Court::where('status', 'active')->get(); // các sân active đã seed

        if ($courts->isEmpty() || empty($customers)) return;

        $bookings = [
            // ── Đã hoàn thành (completed) ──
            [
                'code'           => 'BOOK-20260820-00001',
                'user_id'        => $customers[0],
                'court_id'       => $courts[0]->id,
                'booking_date'   => '2026-08-20',
                'start_time'     => '08:00',
                'end_time'       => '10:00',
                'duration'       => 120,
                'price_snapshot' => 80000,
                'total_amount'   => 160000,
                'deposit_amount' => null,
                'status'         => 'completed',
                'payment_method' => 'at_venue',
                'payment_status' => 'fully_paid',
            ],
            [
                'code'           => 'BOOK-20260821-00002',
                'user_id'        => $customers[1],
                'court_id'       => $courts[1]->id,
                'booking_date'   => '2026-08-21',
                'start_time'     => '17:00',
                'end_time'       => '19:00',
                'duration'       => 120,
                'price_snapshot' => 350000,
                'total_amount'   => 700000,
                'deposit_amount' => 200000,
                'status'         => 'completed',
                'payment_method' => 'partial_online',
                'payment_status' => 'fully_paid',
            ],
            [
                'code'           => 'BOOK-20260822-00003',
                'user_id'        => $customers[2],
                'court_id'       => $courts[2]->id,
                'booking_date'   => '2026-08-22',
                'start_time'     => '07:00',
                'end_time'       => '09:00',
                'duration'       => 120,
                'price_snapshot' => 70000,
                'total_amount'   => 140000,
                'deposit_amount' => null,
                'status'         => 'completed',
                'payment_method' => 'at_venue',
                'payment_status' => 'fully_paid',
            ],
            // ── Đã xác nhận (confirmed) ──
            [
                'code'           => 'BOOK-20260826-00004',
                'user_id'        => $customers[0],
                'court_id'       => $courts[0]->id,
                'booking_date'   => '2026-08-26',
                'start_time'     => '14:00',
                'end_time'       => '16:00',
                'duration'       => 120,
                'price_snapshot' => 80000,
                'total_amount'   => 160000,
                'deposit_amount' => 80000,
                'status'         => 'confirmed',
                'payment_method' => 'partial_online',
                'payment_status' => 'deposit_paid',
            ],
            [
                'code'           => 'BOOK-20260826-00005',
                'user_id'        => $customers[3],
                'court_id'       => $courts[3]->id,
                'booking_date'   => '2026-08-26',
                'start_time'     => '18:00',
                'end_time'       => '20:00',
                'duration'       => 120,
                'price_snapshot' => 110000,
                'total_amount'   => 220000,
                'deposit_amount' => null,
                'status'         => 'confirmed',
                'payment_method' => 'at_venue',
                'payment_status' => 'unpaid',
            ],
            // ── Đang chờ (pending) ──
            [
                'code'           => 'BOOK-20260827-00006',
                'user_id'        => $customers[4],
                'court_id'       => $courts[4]->id,
                'booking_date'   => '2026-08-27',
                'start_time'     => '09:00',
                'end_time'       => '11:00',
                'duration'       => 120,
                'price_snapshot' => 100000,
                'total_amount'   => 200000,
                'deposit_amount' => null,
                'status'         => 'pending',
                'payment_method' => 'full_online',
                'payment_status' => 'unpaid',
            ],
            [
                'code'           => 'BOOK-20260828-00007',
                'user_id'        => $customers[1],
                'court_id'       => $courts[0]->id,
                'booking_date'   => '2026-08-28',
                'start_time'     => '17:00',
                'end_time'       => '19:00',
                'duration'       => 120,
                'price_snapshot' => 120000,
                'total_amount'   => 240000,
                'deposit_amount' => 120000,
                'status'         => 'pending',
                'payment_method' => 'partial_online',
                'payment_status' => 'deposit_paid',
            ],
            // ── Đã huỷ (cancelled) ──
            [
                'code'           => 'BOOK-20260819-00008',
                'user_id'        => $customers[2],
                'court_id'       => $courts[1]->id,
                'booking_date'   => '2026-08-19',
                'start_time'     => '06:00',
                'end_time'       => '08:00',
                'duration'       => 120,
                'price_snapshot' => 200000,
                'total_amount'   => 400000,
                'deposit_amount' => null,
                'status'         => 'cancelled',
                'payment_method' => 'at_venue',
                'payment_status' => 'refunded',
                'cancelled_at'   => '2026-08-18 10:30:00',
                'cancel_reason'  => 'Khách bận việc đột xuất.',
            ],
            [
                'code'           => 'BOOK-20260823-00009',
                'user_id'        => $customers[3],
                'court_id'       => $courts[2]->id,
                'booking_date'   => '2026-08-23',
                'start_time'     => '19:00',
                'end_time'       => '21:00',
                'duration'       => 120,
                'price_snapshot' => 110000,
                'total_amount'   => 220000,
                'deposit_amount' => 50000,
                'status'         => 'cancelled',
                'payment_method' => 'partial_online',
                'payment_status' => 'refunded',
                'cancelled_at'   => '2026-08-22 08:15:00',
                'cancel_reason'  => 'Chủ sân báo lịch bảo trì sân.',
            ],
            // ── Không đến (no_show) ──
            [
                'code'           => 'BOOK-20260824-00010',
                'user_id'        => $customers[4],
                'court_id'       => $courts[0]->id,
                'booking_date'   => '2026-08-24',
                'start_time'     => '07:00',
                'end_time'       => '09:00',
                'duration'       => 120,
                'price_snapshot' => 80000,
                'total_amount'   => 160000,
                'deposit_amount' => null,
                'status'         => 'no_show',
                'payment_method' => 'at_venue',
                'payment_status' => 'unpaid',
            ],
            // ── Đơn tương lai (pending) ──
            [
                'code'           => 'BOOK-20260830-00011',
                'user_id'        => $customers[0],
                'court_id'       => $courts[3]->id,
                'booking_date'   => '2026-08-30',
                'start_time'     => '08:00',
                'end_time'       => '10:00',
                'duration'       => 120,
                'price_snapshot' => 70000,
                'total_amount'   => 140000,
                'deposit_amount' => null,
                'status'         => 'pending',
                'payment_method' => 'full_online',
                'payment_status' => 'unpaid',
            ],
            [
                'code'           => 'BOOK-20260901-00012',
                'user_id'        => $customers[1],
                'court_id'       => $courts[4]->id,
                'booking_date'   => '2026-09-01',
                'start_time'     => '16:00',
                'end_time'       => '18:00',
                'duration'       => 120,
                'price_snapshot' => 100000,
                'total_amount'   => 200000,
                'deposit_amount' => 100000,
                'status'         => 'pending',
                'payment_method' => 'partial_online',
                'payment_status' => 'deposit_paid',
            ],
        ];

        foreach ($bookings as $data) {
            Booking::firstOrCreate(['code' => $data['code']], $data);
        }
    }
}
