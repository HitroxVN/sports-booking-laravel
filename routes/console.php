<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Hủy đơn pending quá hạn chưa thanh toán — giải phóng khung giờ bị chặn
Schedule::command('app:cancel-expired-pending-bookings')->everyFiveMinutes();
