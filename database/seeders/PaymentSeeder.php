<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Database\Seeder;

class PaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();

        $payments = [
            // Đơn completed, thanh toán tại chỗ
            ['booking_code' => 'BOOK-20260820-00001', 'gateway' => 'cash',  'type' => 'full',    'amount' => 160000, 'status' => 'success'],
            // Đơn completed, đặt cọc online 200k + trả tại chỗ 500k
            ['booking_code' => 'BOOK-20260821-00002', 'gateway' => 'vnpay', 'type' => 'deposit', 'amount' => 200000, 'status' => 'success'],
            ['booking_code' => 'BOOK-20260821-00002', 'gateway' => 'cash',  'type' => 'full',    'amount' => 500000, 'status' => 'success'],
            // Đơn completed, thanh toán tại chỗ
            ['booking_code' => 'BOOK-20260822-00003', 'gateway' => 'cash',  'type' => 'full',    'amount' => 140000, 'status' => 'success'],
            // Đơn confirmed, mới đặt cọc
            ['booking_code' => 'BOOK-20260826-00004', 'gateway' => 'momo',  'type' => 'deposit', 'amount' => 80000,  'status' => 'success'],
            // Đơn pending, đặt cọc
            ['booking_code' => 'BOOK-20260828-00007', 'gateway' => 'vnpay', 'type' => 'deposit', 'amount' => 120000, 'status' => 'success'],
            // Đơn cancelled, hoàn tiền cọc
            ['booking_code' => 'BOOK-20260819-00008', 'gateway' => 'vnpay', 'type' => 'refund',  'amount' => 200000, 'status' => 'refunded'],
            // Đơn cancelled, hoàn cọc 50k
            ['booking_code' => 'BOOK-20260823-00009', 'gateway' => 'momo',  'type' => 'refund',  'amount' => 50000,  'status' => 'refunded'],
            // Đơn tương lai, đặt cọc
            ['booking_code' => 'BOOK-20260901-00012', 'gateway' => 'vnpay', 'type' => 'deposit', 'amount' => 100000, 'status' => 'success'],
        ];

        foreach ($payments as $p) {
            $booking = Booking::where('code', $p['booking_code'])->first();
            if (! $booking) continue;

            Payment::firstOrCreate([
                'booking_id' => $booking->id,
                'gateway'    => $p['gateway'],
                'type'       => $p['type'],
                'amount'     => $p['amount'],
            ], [
                'gateway_txn_id' => 'TXN-' . strtoupper($p['gateway']) . '-' . now()->format('YmdHis') . rand(100, 999),
                'status'         => $p['status'],
                'paid_at'        => in_array($p['status'], ['success', 'refunded']) ? $now : null,
            ]);
        }
    }
}
