<?php

namespace App\Console\Commands;

use App\Models\Booking;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('app:cancel-expired-pending-bookings {--minutes= : Số phút giữ đơn pending trước khi hủy (mặc định = Booking::PAYMENT_EXPIRY_MINUTES)}')]
#[Description('Hủy các đơn đặt sân pending quá hạn chưa thanh toán để giải phóng khung giờ')]
class CancelExpiredPendingBookings extends Command
{
    public function handle(): int
    {
        $minutes = (int) ($this->option('minutes') ?: Booking::PAYMENT_EXPIRY_MINUTES);
        $minutes = max(1, $minutes);

        // Chỉ hủy đơn online chưa xác nhận: pending quá X phút kể từ khi tạo
        // (webhook SePay sẽ không bao giờ xác nhận đơn đã hủy — xem SePayWebhookController).
        $count = Booking::where('status', 'pending')
            ->where('created_at', '<', now()->subMinutes($minutes))
            ->update([
                'status'        => 'cancelled',
                'cancelled_at'  => now(),
                'cancel_reason' => "Hết hạn thanh toán ({$minutes} phút) — tự động hủy hệ thống",
            ]);

        if ($count > 0) {
            $this->info("Đã hủy {$count} đơn pending quá hạn.");
        }

        return self::SUCCESS;
    }
}
