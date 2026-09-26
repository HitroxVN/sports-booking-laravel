<?php

namespace App\Notifications;

use App\Models\Booking;

/**
 * Gửi cho KHÁCH khi đơn được chốt sân (chủ sân xác nhận, hoặc thanh toán online đủ).
 */
class BookingConfirmed extends ArenaNotification
{
    public function __construct(Booking $booking, string $reason = '')
    {
        $court = $booking->court?->name ?? 'Sân';

        $message = "Đơn {$booking->code} tại {$court}, ngày {$booking->booking_date->format('d/m/Y')} "
            . "đã được xác nhận. Hẹn gặp bạn đúng giờ!";

        if ($reason !== '') {
            $message .= " ({$reason})";
        }

        parent::__construct(
            title: 'Đơn đặt sân đã được xác nhận',
            message: $message,
            url: route('customer.bookings.show', $booking),
            level: 'success',
        );
    }
}
