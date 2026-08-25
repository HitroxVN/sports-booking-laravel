<?php

use Illuminate\Support\Facades\Route;

// Import các Controller của Owner
use App\Http\Controllers\Owner\VenueController;
use App\Http\Controllers\Owner\CourtController;
use App\Http\Controllers\Owner\SlotController;
use App\Http\Controllers\Owner\ClosureController;
use App\Http\Controllers\Owner\BookingController;
use App\Http\Controllers\Owner\PromotionController;
use App\Http\Controllers\Owner\ScheduleController;
use App\Http\Controllers\Owner\ReviewController;
use App\Http\Controllers\Owner\ReportController;
use App\Http\Controllers\Owner\DashboardController;

// ─── Public ──────────────────────────────────────────────────────────────────
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Route trung gian giải quyết lỗi Route [dashboard] not defined của Breeze
Route::get('/dashboard', function () {
    $user = auth()->user();
    if ($user->role === 'admin') {
        return redirect()->route('admin.dashboard');
    }
    return redirect()->route('owner.dashboard');
})->middleware(['auth'])->name('dashboard');

// ─── Khách hàng ──────────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:customer'])->group(function () {
    // TODO Sprint 2-3: Booking, Review, Favorite, Notification, Profile
});

// ─── Chủ sân ─────────────────────────────────────────────────────────────────
Route::prefix('owner')->name('owner.')->middleware(['auth', 'role:owner'])->group(function () {
    // Trang tổng quan Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // 1. Quản lý Khu Sân (Venues)
    Route::resource('venues', VenueController::class);
    
    // 2. Quản lý Sân Con (Courts)
    Route::resource('venues.courts', CourtController::class)->shallow();

    // 3. Quản lý Khuyến Mãi (Promotions)
    Route::resource('venues.promotions', PromotionController::class)->shallow();

    // 4. Quản lý Khung Giờ (Slots)
    Route::resource('courts.slots', SlotController::class)->shallow();
    
    // 5. Quản lý Khóa Lịch (Closures)
    Route::resource('courts.closures', ClosureController::class)->shallow();

    // 6. Quản lý Đơn Đặt Sân (Bookings)
    Route::resource('bookings', BookingController::class)->only(['index', 'show', 'update']);

    // 7. Lịch biểu tổng quan (Schedule)
    Route::get('/schedule', [ScheduleController::class, 'index'])->name('schedule.index');

    // 8. Quản lý Đánh Giá (Reviews) - Chỉ xem và phản hồi
    Route::resource('reviews', ReviewController::class)->only(['index', 'update']);

    // 9. Báo Cáo Doanh Thu (Reports)
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
});

// ─── Admin ───────────────────────────────────────────────────────────────────
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/dashboard', function () {
        return 'Admin Dashboard — Sprint 6';
    })->name('dashboard');
});

require __DIR__.'/auth.php';