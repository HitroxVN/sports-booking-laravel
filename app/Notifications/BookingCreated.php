<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Support\Carbon;

/**
 * Gửi cho KHÁCH ngay sau khi tạo đơn: đơn lẻ thì nhắc thanh toán,
 * lịch cố định thì thanh toán tại sân mỗi buổi nên chỉ xác nhận đã giữ sân.
 */
class BookingCreated extends ArenaNotification
{
    public function __construct(Booking $booking, int $totalSessions = 1)
    {
        $court = $booking->court?->name ?? 'Sân';
        $venue = $booking->court?->venue?->name ?? '';
        $where = $venue !== '' ? "{$court} ({$venue})" : $court;

        $time = Carbon::parse($booking->start_time)->format('H:i')
            . ' - ' . Carbon::parse($booking->end_time)->format('H:i');

        if ($totalSessions > 1) {
            // Lịch cố định: nhiều buổi, thanh toán tại sân từng buổi
            parent::__construct(
                title: 'Đã giữ sân cho lịch cố định',
                message: "Đã giữ {$totalSessions} buổi tại {$where}, khung {$time} hàng tuần. Vui lòng thanh toán tại sân mỗi buổi.",
                url: route('customer.bookings.index'),
                level: 'success',
            );

            return;
        }

        parent::__construct(
            title: 'Đặt sân thành công',
            message: "Đơn {$booking->code} tại {$where}, ngày {$booking->booking_date->format('d/m/Y')} khung {$time}. Vui lòng hoàn tất thanh toán để giữ sân.",
            url: route('customer.bookings.show', $booking),
            level: 'info',
        );
    }
}
