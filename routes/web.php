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
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Customer\SearchController;

// ─── Public ──────────────────────────────────────────────────────────────────
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/search', [SearchController::class, 'index'])->name('search');
Route::get('/venues/{slug}', [\App\Http\Controllers\Customer\VenueController::class, 'show'])->name('venues.show');

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
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\VenueController as AdminVenueController;
use App\Http\Controllers\Admin\BookingController as AdminBookingController;
use App\Http\Controllers\Admin\SportController as AdminSportController;
use App\Http\Controllers\Admin\ReportController as AdminReportController;

Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Users: index + ban/unban
    Route::get('/users',               [AdminUserController::class, 'index'])->name('users.index');
    Route::post('/users/{user}/ban',   [AdminUserController::class, 'ban'])->name('users.ban');
    Route::post('/users/{user}/unban', [AdminUserController::class, 'unban'])->name('users.unban');

    // Venues: index + approve + reject (dùng {id} integer, không phải slug)
    Route::get('/venues',               [AdminVenueController::class, 'index'])->name('venues.index');
    Route::post('/venues/{id}/approve', [AdminVenueController::class, 'approve'])->name('venues.approve');
    Route::post('/venues/{id}/reject',  [AdminVenueController::class, 'reject'])->name('venues.reject');

    // Bookings: read-only
    Route::get('/bookings', [AdminBookingController::class, 'index'])->name('bookings.index');

    // Sports: CRUD (không cần create/edit view riêng — inline modal)
    Route::resource('sports', AdminSportController::class)->only(['index', 'store', 'update', 'destroy']);

    // Reports + export CSV
    Route::get('/reports',        [AdminReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export', [AdminReportController::class, 'export'])->name('reports.export');
});

use App\Http\Controllers\ProfileController;

// ─── Profile ─────────────────────────────────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
