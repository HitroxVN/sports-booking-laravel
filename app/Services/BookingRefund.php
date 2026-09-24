<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;

/**
 * Hoàn tiền khi hủy đơn đã thanh toán.
 * Ghi 1 dòng Payment type=refund — số tiền = tổng đã thanh toán thành công của đơn.
 */
class BookingRefund
{
    public static function refund(Booking $booking, string $reason): bool
    {
        // Tổng tiền khách đã trả thành công cho đơn này
        $paidAmount = (float) $booking->payments()
            ->where('type', '!=', 'refund')
            ->where('status', 'success')
            ->sum('amount');

        if ($paidAmount <= 0) {
            return false;
        }

        DB::transaction(function () use ($booking, $paidAmount, $reason) {
            Payment::create([
                'booking_id' => $booking->id,
                'gateway'    => 'manual', // hoàn thủ công bởi chủ sân qua ngân hàng
                'amount'     => $paidAmount,
                'type'       => 'refund',
                'status'     => 'success',
                'gateway_response' => ['reason' => $reason],
                'paid_at'    => now(),
            ]);

            $booking->update(['payment_status' => 'refunded']);
        });

        return true;
    }
}
