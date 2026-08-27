<?php

use Illuminate\Support\Facades\Route;

// Owner
use App\Http\Controllers\Owner\BookingController;
use App\Http\Controllers\Owner\ClosureController;
use App\Http\Controllers\Owner\CourtController;
use App\Http\Controllers\Owner\DashboardController;
use App\Http\Controllers\Owner\PromotionController;
use App\Http\Controllers\Owner\ReportController;
use App\Http\Controllers\Owner\ReviewController;
use App\Http\Controllers\Owner\ScheduleController;
use App\Http\Controllers\Owner\SlotController;
use App\Http\Controllers\Owner\VenueController;

// Customer
use App\Http\Controllers\Customer\CustomerBookingController;
use App\Http\Controllers\Customer\SearchController;
use App\Http\Controllers\Customer\VenueController as CustomerVenueController;

// Admin
use App\Http\Controllers\Admin\BookingController as AdminBookingController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ReportController as AdminReportController;
use App\Http\Controllers\Admin\SportController as AdminSportController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\VenueController as AdminVenueController;

// Chung
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;

// ─── Public ──────────────────────────────────────────────────────────────────
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/search', [SearchController::class, 'index'])->name('search');
Route::get('/venues/{slug}', [CustomerVenueController::class, 'show'])->name('venues.show');

// Route trung gian giải quyết lỗi Route [dashboard] not defined của Breeze
Route::get('/dashboard', function () {
    $user = \Illuminate\Support\Facades\Auth::user();

    // user null -> về login
    if (! $user) {
        return redirect()->route('login');
    }

    // user chưa xác thực email -> về trang yêu cầu xác thực
    if (! $user->hasVerifiedEmail()) {
        return redirect()->route('verification.notice');
    }

    return match ($user->role) {
        'admin'    => redirect()->route('admin.dashboard'),
        'customer' => redirect()->route('home'),
        default    => redirect()->route('owner.dashboard'),
    };
})->middleware(['auth'])->name('dashboard');

// ─── Profile Chung Cho Mọi User (Vá Bug #3) ───────────────────────────────────
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ─── Khách hàng ──────────────────────────────────────────────────────────────
Route::middleware(['auth', 'verified', 'role:customer'])->name('customer.')->group(function () {
    // 1. Đặt sân (Booking)
    Route::get('/courts/{courtId}/book', [CustomerBookingController::class, 'create'])->name('bookings.create');
    Route::post('/bookings', [CustomerBookingController::class, 'store'])->name('bookings.store');
    
    // 2. Lịch sử đặt sân của tôi (Trang danh sách)
    Route::get('/my-bookings', [CustomerBookingController::class, 'index'])->name('bookings.index');
});

// ─── Chủ sân ─────────────────────────────────────────────────────────────────
Route::prefix('owner')->name('owner.')->middleware(['auth', 'verified', 'role:owner'])->group(function () {
    // Trang tổng quan Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // 1. Quản lý Khu Sân (Venues)
    Route::resource('venues', VenueController::class);

    // 2. Quản lý Sân Con (Courts)
    Route::resource('venues.courts', CourtController::class)->shallow()->except(['show']);

    // 3. Quản lý Khuyến Mãi (Promotions) - Đã giữ nguyên shallow để khớp với logic Controller
    Route::resource('venues.promotions', PromotionController::class)->shallow();

    // 4. Quản lý Khung Giờ (Slots) - Vá Bug #4: Chặn các route rác không dùng
    Route::resource('courts.slots', SlotController::class)->shallow()->except(['show', 'edit', 'update']);

    // 5. Quản lý Khóa Lịch (Closures) - Vá Bug #4: Chặn các route rác không dùng
    Route::resource('courts.closures', ClosureController::class)->shallow()->except(['show', 'edit', 'update']);

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
Route::prefix('admin')->name('admin.')->middleware(['auth', 'verified', 'role:admin'])->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Users: index + ban/unban
    Route::get('/users',               [AdminUserController::class, 'index'])->name('users.index');
    Route::post('/users/{user}/ban',   [AdminUserController::class, 'ban'])->name('users.ban');
    Route::post('/users/{user}/unban', [AdminUserController::class, 'unban'])->name('users.unban');

    // Venues: index + approve + reject (dùng {venue} — implicit binding theo slug)
    Route::get('/venues',               [AdminVenueController::class, 'index'])->name('venues.index');
    Route::post('/venues/{venue}/approve', [AdminVenueController::class, 'approve'])->name('venues.approve');
    Route::post('/venues/{venue}/reject',  [AdminVenueController::class, 'reject'])->name('venues.reject');

    // Bookings: read-only
    Route::get('/bookings', [AdminBookingController::class, 'index'])->name('bookings.index');

    // Sports: CRUD (không cần create/edit view riêng — inline modal)
    Route::resource('sports', AdminSportController::class)->only(['index', 'store', 'update', 'destroy']);

    // Reports + export CSV
    Route::get('/reports',        [AdminReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export', [AdminReportController::class, 'export'])->name('reports.export');
});

require __DIR__ . '/auth.php';
