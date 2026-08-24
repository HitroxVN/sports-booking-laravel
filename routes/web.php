<?php

use Illuminate\Support\Facades\Route;

// ─── Public ──────────────────────────────────────────────────────────────────
Route::get('/', function () {
    return view('welcome');
})->name('home');

// TODO Sprint 2: VenueController@index, show
// Route::get('/venues', [App\Http\Controllers\Customer\VenueController::class, 'index'])->name('venues.index');
// Route::get('/venues/{slug}', [App\Http\Controllers\Customer\VenueController::class, 'show'])->name('venues.show');

// ─── Khách hàng ──────────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:customer'])->group(function () {
    // TODO Sprint 2-3: Booking, Review, Favorite, Notification, Profile
});

// ─── Chủ sân ─────────────────────────────────────────────────────────────────
Route::prefix('owner')->name('owner.')->middleware(['auth', 'role:owner'])->group(function () {
    Route::get('/dashboard', function () {
        return 'Owner Dashboard — Sprint 5';
    })->name('dashboard');

    // TODO Sprint 5: Venue, Court, Slot, Closure, Booking, Schedule, Review, Promotion, Report
});

// ─── Admin ───────────────────────────────────────────────────────────────────
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/dashboard', function () {
        return 'Admin Dashboard — Sprint 6';
    })->name('dashboard');

    // TODO Sprint 6: User, Venue (approve/reject), Sport, Booking, Report
});

// ─── Thanh toán webhook (CSRF exempt — đã cấu hình bootstrap/app.php) ───────
// TODO Sprint 4: PaymentController webhook
// Route::post('/payment/vnpay/webhook', ...);
// Route::post('/payment/momo/webhook', ...);

// ─── Breeze auth routes ───────────────────────────────────────────────────────
require __DIR__.'/auth.php';
