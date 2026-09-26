<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Support\Carbon;

/**
 * Gửi cho CHỦ SÂN khi có đơn mới thuộc khu sân của mình.
 */
class NewBookingForOwner extends ArenaNotification
{
    public function __construct(Booking $booking, int $totalSessions = 1)
    {
        $court = $booking->court?->name ?? 'Sân';
        $customer = $booking->user?->name ?? 'Khách hàng';

        $time = Carbon::parse($booking->start_time)->format('H:i')
            . ' - ' . Carbon::parse($booking->end_time)->format('H:i');

        if ($totalSessions > 1) {
            parent::__construct(
                title: 'Có lịch cố định mới',
                message: "{$customer} đã đặt {$totalSessions} buổi tại {$court}, khung {$time} hàng tuần (từ {$booking->booking_date->format('d/m/Y')}).",
                url: route('owner.bookings.show', $booking),
                level: 'info',
            );

            return;
        }

        parent::__construct(
            title: 'Có đơn đặt sân mới',
            message: "{$customer} vừa đặt {$court} ngày {$booking->booking_date->format('d/m/Y')} khung {$time}. Mã đơn {$booking->code}.",
            url: route('owner.bookings.show', $booking),
            level: 'info',
        );
    }
}
