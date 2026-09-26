<?php

namespace App\Notifications;

use App\Models\Booking;

/**
 * Gửi cho KHÁCH khi hệ thống nhận được tiền chuyển khoản (webhook SePay).
 */
class PaymentReceived extends ArenaNotification
{
    public function __construct(Booking $booking, float $amount, bool $fullyPaid)
    {
        $court = $booking->court?->name ?? 'Sân';

        $message = "Đã nhận " . number_format($amount) . "đ cho đơn {$booking->code} tại {$court}. "
            . ($fullyPaid
                ? 'Đơn đã được thanh toán đủ.'
                : 'Đã đủ tiền cọc, phần còn lại thanh toán tại sân.');

        parent::__construct(
            title: 'Đã nhận thanh toán',
            message: $message,
            url: route('customer.bookings.show', $booking),
            level: 'success',
        );
    }
}
