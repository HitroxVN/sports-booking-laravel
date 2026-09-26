<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Support\Carbon;

/**
 * Gửi cho KHÁCH trước giờ đặt sân 2 tiếng — giảm no-show.
 * Được gửi bởi command app:send-booking-reminders.
 */
class BookingReminder extends ArenaNotification
{
    public function __construct(Booking $booking)
    {
        $court = $booking->court?->name ?? 'Sân';
        $venue = $booking->court?->venue?->name ?? '';
        $where = $venue !== '' ? "{$court} ({$venue})" : $court;

        $time = Carbon::parse($booking->start_time)->format('H:i');

        parent::__construct(
            title: 'Sắp tới giờ đặt sân',
            message: "Bạn có lịch đặt {$where} lúc {$time} hôm nay. Mã đơn {$booking->code}. Nhớ tới đúng giờ nhé!",
            url: route('customer.bookings.show', $booking),
            level: 'warning',
        );
    }
}
