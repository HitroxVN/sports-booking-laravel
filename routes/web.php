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
use App\Http\Controllers\Customer\ChatController as CustomerChatController;
use App\Http\Controllers\Customer\CustomerBookingController;
use App\Http\Controllers\Customer\SearchController;
use App\Http\Controllers\Customer\VenueController as CustomerVenueController;

// Admin
use App\Http\Controllers\Admin\BookingController as AdminBookingController;
use App\Http\Controllers\Admin\ChatController as AdminChatController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\PaymentController as AdminPaymentController;
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
Route::get('/lien-he', fn () => view('contact'))->name('contact');
Route::get('/venues/popular', [CustomerVenueController::class, 'popular'])->name('venues.popular');
Route::get('/venues/{slug}', [CustomerVenueController::class, 'show'])->name('venues.show');

// ─── Livechat Khách Hàng (WebSockets Realtime) ───────────────────────────────
Route::post('/chat/initiate',                  [CustomerChatController::class, 'initiate'])->name('chat.initiate');
Route::get('/chat/{conversation}/messages',   [CustomerChatController::class, 'getMessages'])->name('chat.messages');
Route::post('/chat/{conversation}/messages',  [CustomerChatController::class, 'sendMessage'])->name('chat.send');

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
    Route::get('/bookings/{booking}/pay', [CustomerBookingController::class, 'pay'])->name('bookings.pay');
    Route::get('/bookings/{booking}/status', [CustomerBookingController::class, 'status'])->name('bookings.status');

    // 2. Lịch sử đặt sân của tôi (Trang danh sách)
    Route::get('/my-bookings', [CustomerBookingController::class, 'index'])->name('bookings.index');
});

// ─── Chủ sân ─────────────────────────────────────────────────────────────────
Route::prefix('owner')->name('owner.')->middleware(['auth', 'verified', 'role:owner'])->group(function () {
    // Trang tổng quan Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // 1. Quản lý Khu Sân (Venues)
    Route::resource('venues', VenueController::class);
    Route::put('/venues/{venue}/operating-hours', [VenueController::class, 'updateOperatingHours'])
        ->name('venues.operating-hours.update');

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

    // Users: CRUD + ban/unban + reset password
    Route::get('/users',                  [AdminUserController::class, 'index'])->name('users.index');
    Route::get('/users/create',           [AdminUserController::class, 'create'])->name('users.create');
    Route::post('/users',                 [AdminUserController::class, 'store'])->name('users.store');
    Route::get('/users/{user}',           [AdminUserController::class, 'show'])->name('users.show')->withTrashed();
    Route::get('/users/{user}/edit',      [AdminUserController::class, 'edit'])->name('users.edit');
    Route::patch('/users/{user}',         [AdminUserController::class, 'update'])->name('users.update');
    Route::patch('/users/{user}/password',[AdminUserController::class, 'updatePassword'])->name('users.password');
    Route::delete('/users/{user}',        [AdminUserController::class, 'destroy'])->name('users.destroy');
    Route::post('/users/{user}/restore',  [AdminUserController::class, 'restore'])->name('users.restore')->withTrashed();
    Route::post('/users/{user}/ban',      [AdminUserController::class, 'ban'])->name('users.ban');
    Route::post('/users/{user}/unban',    [AdminUserController::class, 'unban'])->name('users.unban');

    // Venues: index + approve + reject + destroy (dùng {venue} — implicit binding theo slug)
    Route::get('/venues',               [AdminVenueController::class, 'index'])->name('venues.index');
    Route::post('/venues/{venue}/approve', [AdminVenueController::class, 'approve'])->name('venues.approve');
    Route::post('/venues/{venue}/reject',  [AdminVenueController::class, 'reject'])->name('venues.reject');
    Route::delete('/venues/{venue}',       [AdminVenueController::class, 'destroy'])->name('venues.destroy');
    Route::post('/venues/{venue}/restore', [AdminVenueController::class, 'restore'])->name('venues.restore')->withTrashed();

    // Bookings: read-only
    Route::get('/bookings', [AdminBookingController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/{booking}', [AdminBookingController::class, 'show'])->name('bookings.show');

    // Sports: CRUD (không cần create/edit view riêng — inline modal)
    Route::resource('sports', AdminSportController::class)->only(['index', 'store', 'update', 'destroy']);

    // Reports + export CSV
    Route::get('/reports',        [AdminReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export', [AdminReportController::class, 'export'])->name('reports.export');

    // Payments: Quản lý chi tiết lịch sử thanh toán đơn hàng
    Route::get('/payments',           [AdminPaymentController::class, 'index'])->name('payments.index');
    Route::get('/payments/{payment}', [AdminPaymentController::class, 'show'])->name('payments.show');

    // Livechat: Quản trị tin nhắn thời gian thực
    Route::get('/chats',                         [AdminChatController::class, 'index'])->name('chats.index');
    Route::get('/chats/{conversation}',          [AdminChatController::class, 'show'])->name('chats.show');
    Route::post('/chats/{conversation}/reply',   [AdminChatController::class, 'reply'])->name('chats.reply');
    Route::patch('/chats/{conversation}/status', [AdminChatController::class, 'toggleStatus'])->name('chats.status');
});

require __DIR__ . '/auth.php';
