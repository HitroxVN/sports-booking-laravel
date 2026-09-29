<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Hủy đơn pending quá hạn chưa thanh toán — giải phóng khung giờ bị chặn
Schedule::command('app:cancel-expired-pending-bookings')->everyMinute();

// Nhắc khách sắp tới giờ đặt sân (trước 2 giờ) — giảm no-show
Schedule::command('app:send-booking-reminders')->everyTenMinutes();

// Đồng bộ tin thể thao từ VnExpress và Thanh Niên, tránh chạy chồng khi nguồn phản hồi chậm
Schedule::command('news:fetch')->everyThirtyMinutes()->withoutOverlapping();
