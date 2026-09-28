<?php

namespace App\Observers;

use App\Models\Booking;
use App\Services\LoyaltyService;

class BookingObserver
{
    public function saved(Booking $booking): void
    {
        $loyalty = app(LoyaltyService::class);

        if ($booking->payment_status === 'fully_paid'
            && ($booking->wasRecentlyCreated || $booking->wasChanged('payment_status'))) {
            $loyalty->awardForPaidBooking($booking);
        }

        if ($booking->status === 'cancelled' && $booking->wasChanged('status')) {
            $loyalty->releaseVoucherFromCancelledBooking($booking);
        }
    }
}
