<?php

namespace App\Console\Commands;

use App\Models\Booking;
use App\Notifications\BookingReminder;
use App\Services\Notifier;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

#[Signature('app:send-booking-reminders {--hours=2 : Nhắc trước bao nhiêu giờ}')]
#[Description('Nhắc khách sắp tới giờ đặt sân để giảm no-show')]
class SendBookingReminders extends Command
{
    public function handle(): int
    {
        $now   = now();
        $hours = max(1, (int) ($this->option('hours') ?: 2));
        $limit = $now->copy()->addHours($hours);

        // Lọc thô theo ngày ở DB (tận dụng index), rồi lọc chính xác theo giờ ở PHP
        // vì giờ đặt nằm riêng ở cột start_time.
        $bookings = Booking::with(['user', 'court.venue'])
            ->where('status', 'confirmed')       // chỉ đơn đã chốt sân
            ->whereNull('reminder_sent_at')      // chưa nhắc lần nào
            // whereDate (không phải whereBetween): cột lưu kèm giờ nên so sánh chuỗi thuần sẽ trượt
            ->whereDate('booking_date', '>=', $now->toDateString())
            ->whereDate('booking_date', '<=', $limit->toDateString())
            ->get()
            ->filter(function (Booking $booking) use ($now, $limit) {
                $start = Carbon::parse($booking->booking_date->toDateString() . ' ' . $booking->start_time);

                return $start->betweenIncluded($now, $limit);
            });

        foreach ($bookings as $booking) {
            Notifier::send($booking->user, new BookingReminder($booking));
            $booking->forceFill(['reminder_sent_at' => now()])->save();
        }

        if ($bookings->isNotEmpty()) {
            $this->info("Đã gửi {$bookings->count()} nhắc lịch (trước {$hours} giờ).");
        }

        return self::SUCCESS;
    }
}
