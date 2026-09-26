<?php

namespace App\Notifications;

use App\Models\Booking;

/**
 * Gửi cho KHÁCH khi đơn bị hủy (chủ sân hủy hoặc hệ thống tự hủy do quá hạn thanh toán).
 */
class BookingCancelled extends ArenaNotification
{
    public function __construct(Booking $booking, ?string $reason = null)
    {
        $court  = $booking->court?->name ?? 'Sân';
        $reason = $reason ?: $booking->cancel_reason;

        $message = "Đơn {$booking->code} tại {$court}, ngày {$booking->booking_date->format('d/m/Y')} đã bị hủy.";

        if ($reason) {
            $message .= " Lý do: {$reason}.";
        }

        parent::__construct(
            title: 'Đơn đặt sân đã bị hủy',
            message: $message,
            url: route('customer.bookings.show', $booking),
            level: 'danger',
        );
    }
}
