<?php

namespace Tests\Feature\Notification;

use App\Models\Booking;
use App\Models\Court;
use App\Models\CourtSlot;
use App\Models\OperatingHour;
use App\Models\Payment;
use App\Models\Sport;
use App\Models\User;
use App\Models\Venue;
use App\Notifications\BookingCancelled;
use App\Notifications\BookingConfirmed;
use App\Notifications\BookingCreated;
use App\Notifications\BookingReminder;
use App\Notifications\NewBookingForOwner;
use App\Notifications\PaymentReceived;
use App\Notifications\VenuePendingApproval;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    use RefreshDatabase;

    private User $customer;
    private User $owner;
    private User $admin;
    private Court $court;
    private Venue $venue;

    protected function setUp(): void
    {
        parent::setUp();

        $this->customer = User::factory()->create(['role' => 'customer', 'email_verified_at' => now()]);
        $this->owner    = User::factory()->create(['role' => 'owner', 'email_verified_at' => now()]);
        $this->admin    = User::factory()->create(['role' => 'admin', 'email_verified_at' => now()]);

        $sport = Sport::create(['name' => 'Bóng đá', 'slug' => 'bong-da']);

        $this->venue = Venue::create([
            'name'     => 'Sân Thống Nhất',
            'slug'     => 'san-thong-nhat',
            'owner_id' => $this->owner->id,
            'phone'    => '0900000000',
            'city'     => 'TP.HCM',
            'district' => 'Q1',
            'address'  => '1 Đường Test',
            'status'   => 'active',
        ]);

        $this->court = Court::create([
            'venue_id' => $this->venue->id,
            'sport_id' => $sport->id,
            'name'     => 'Sân 1',
            'status'   => 'active',
        ]);

        for ($d = 0; $d <= 6; $d++) {
            OperatingHour::create([
                'venue_id'    => $this->venue->id,
                'day_of_week' => $d,
                'open_time'   => '05:00:00',
                'close_time'  => '22:00:00',
                'is_closed'   => false,
            ]);
        }

        CourtSlot::create([
            'court_id'    => $this->court->id,
            'day_of_week' => null,
            'start_time'  => '05:00:00',
            'end_time'    => '22:00:00',
            'price'       => 80000,
            'is_peak'     => false,
        ]);
    }

    private function makeBooking(array $overrides = []): Booking
    {
        // created_at không nằm trong $fillable nên phải gán riêng
        $createdAt = $overrides['created_at'] ?? null;
        unset($overrides['created_at']);

        $booking = Booking::create(array_merge([
            'code'           => 'BK' . strtoupper(\Illuminate\Support\Str::random(8)),
            'user_id'        => $this->customer->id,
            'court_id'       => $this->court->id,
            'booking_date'   => now()->addDays(3)->toDateString(),
            'start_time'     => '07:00:00',
            'end_time'       => '09:00:00',
            'duration'       => 120,
            'price_snapshot' => 80000,
            'total_amount'   => 160000,
            'status'         => 'pending',
            'payment_method' => 'full_online',
            'payment_status' => 'unpaid',
        ], $overrides));

        if ($createdAt) {
            $booking->forceFill(['created_at' => $createdAt])->save();
        }

        return $booking;
    }

    public function test_dat_san_bao_cho_ca_khach_va_chu_san(): void
    {
        Notification::fake();

        $this->actingAs($this->customer)->post(route('customer.bookings.store'), [
            'court_id'     => $this->court->id,
            'booking_date' => now()->addDays(3)->format('Y-m-d'),
            'start_time'   => '07:00',
            'end_time'     => '09:00',
        ])->assertRedirect();

        Notification::assertSentTo($this->customer, BookingCreated::class);
        Notification::assertSentTo($this->owner, NewBookingForOwner::class);
    }

    public function test_lich_co_dinh_chi_gui_mot_thong_bao_tom_tat(): void
    {
        Notification::fake();

        $this->actingAs($this->customer)->post(route('customer.bookings.store'), [
            'court_id'     => $this->court->id,
            'booking_date' => now()->addDays(3)->format('Y-m-d'),
            'start_time'   => '07:00',
            'end_time'     => '09:00',
            'repeat_weeks' => 4,
        ])->assertRedirect();

        // 4 buổi nhưng chỉ 1 thông báo (không spam 4 cái)
        Notification::assertSentToTimes($this->customer, BookingCreated::class, 1);
        Notification::assertSentToTimes($this->owner, NewBookingForOwner::class, 1);

        $this->assertSame(4, Booking::where('series_id', '!=', null)->count());
    }

    public function test_thanh_toan_du_bao_da_nhan_tien_va_da_chot_san(): void
    {
        Notification::fake();

        $booking = $this->makeBooking();

        $this->withHeader('Authorization', 'Bearer ' . config('services.sepay.webhook_key'))
            ->postJson(route('webhooks.sepay'), [
                'id'              => 998877,
                'content'         => 'Chuyen tien ' . $booking->code,
                'transferAmount'  => 160000,
                'transactionDate' => now()->toDateTimeString(),
                'transferType'    => 'in',
            ])->assertOk();

        $this->assertSame('confirmed', $booking->fresh()->status);

        Notification::assertSentTo($this->customer, PaymentReceived::class);
        Notification::assertSentTo($this->customer, BookingConfirmed::class);
    }

    public function test_chuyen_coc_thi_bao_da_nhan_tien_nhung_khong_bao_chot_san(): void
    {
        Notification::fake();

        // Có cọc 50k, chuyển 50k → chỉ đủ cọc, chưa chốt sân
        $booking = $this->makeBooking(['deposit_amount' => 50000]);

        $this->withHeader('Authorization', 'Bearer ' . config('services.sepay.webhook_key'))
            ->postJson(route('webhooks.sepay'), [
                'id'              => 998878,
                'content'         => 'Coc ' . $booking->code,
                'transferAmount'  => 50000,
                'transactionDate' => now()->toDateTimeString(),
                'transferType'    => 'in',
            ])->assertOk();

        $this->assertSame('pending', $booking->fresh()->status);

        Notification::assertSentTo($this->customer, PaymentReceived::class);
        Notification::assertNotSentTo($this->customer, BookingConfirmed::class);
    }

    public function test_don_qua_han_bao_cho_khach(): void
    {
        Notification::fake();

        $booking = $this->makeBooking(['created_at' => now()->subMinutes(30)]);

        $this->artisan('app:cancel-expired-pending-bookings')->assertSuccessful();

        $this->assertSame('cancelled', $booking->fresh()->status);
        Notification::assertSentTo($this->customer, BookingCancelled::class);
    }

    public function test_chu_san_huy_don_da_tra_tien_van_huy_that_va_bao_khach(): void
    {
        Notification::fake();

        $booking = $this->makeBooking(['status' => 'confirmed', 'payment_status' => 'fully_paid']);

        Payment::create([
            'booking_id' => $booking->id,
            'gateway'    => 'sepay',
            'amount'     => 160000,
            'type'       => 'full',
            'status'     => 'success',
            'paid_at'    => now(),
        ]);

        $this->actingAs($this->owner)->put(route('owner.bookings.update', $booking), [
            'status'        => 'cancelled',
            'cancel_reason' => 'Sân hỏng',
        ])->assertRedirect();

        $fresh = $booking->fresh();

        // Hoàn tiền không được làm đơn kẹt ở confirmed
        $this->assertSame('cancelled', $fresh->status);
        $this->assertNotNull($fresh->cancelled_at);
        $this->assertSame('refunded', $fresh->payment_status);

        Notification::assertSentTo($this->customer, BookingCancelled::class);
    }

    public function test_tao_khu_san_moi_bao_cho_moi_admin(): void
    {
        // 2 admin: mỗi người phải nhận 1 instance riêng — dùng chung instance sẽ
        // văng lỗi trùng khóa chính uuid của bảng notifications.
        $secondAdmin = User::factory()->create(['role' => 'admin', 'email_verified_at' => now()]);

        $this->actingAs($this->owner)->post(route('owner.venues.store'), [
            'name'     => 'Khu sân mới',
            'phone'    => '0900000001',
            'city'     => 'TP.HCM',
            'ward'     => 'Phường 1',
            'address'  => '2 Đường Mới',
        ])->assertRedirect();

        $this->assertSame(2, Venue::count());

        // Không fake: thông báo phải ghi được thật vào DB cho từng admin
        $this->assertSame(1, $this->admin->notifications()->count());
        $this->assertSame(1, $secondAdmin->notifications()->count());
        $this->assertSame(0, $this->customer->notifications()->count());

        // Phải là 2 bản ghi khác nhau, không phải cùng 1 uuid
        $this->assertNotSame(
            $this->admin->notifications()->first()->id,
            $secondAdmin->notifications()->first()->id
        );
    }

    public function test_nhac_lich_gui_dung_mot_lan_cho_don_sap_toi_gio(): void
    {
        Notification::fake();

        // Cố định giờ để 10:00 — nếu dùng giờ thật thì test sẽ hỏng khi chạy gần nửa đêm
        $this->travelTo(\Illuminate\Support\Carbon::parse('2026-09-25 10:00:00'));

        // Bắt đầu sau 1 giờ → nằm trong cửa sổ nhắc 2 giờ
        $soon = $this->makeBooking([
            'status'       => 'confirmed',
            'booking_date' => '2026-09-25',
            'start_time'   => '11:00:00',
            'end_time'     => '13:00:00',
        ]);

        // Còn 5 tiếng → chưa nhắc
        $later = $this->makeBooking([
            'status'       => 'confirmed',
            'booking_date' => '2026-09-25',
            'start_time'   => '15:00:00',
            'end_time'     => '17:00:00',
        ]);

        // Đơn chưa chốt sân thì không nhắc
        $pending = $this->makeBooking([
            'booking_date' => '2026-09-25',
            'start_time'   => '11:00:00',
            'end_time'     => '13:00:00',
        ]);

        $this->artisan('app:send-booking-reminders')->assertSuccessful();

        Notification::assertSentTo($this->customer, BookingReminder::class);
        $this->assertNotNull($soon->fresh()->reminder_sent_at);
        $this->assertNull($later->fresh()->reminder_sent_at);
        $this->assertNull($pending->fresh()->reminder_sent_at);

        // Chạy lại lần 2 không được nhắc trùng
        Notification::fake();
        $this->artisan('app:send-booking-reminders')->assertSuccessful();
        Notification::assertNothingSent();

        $this->travelBack();
    }

    // ─── Chuông + trang thông báo (dùng notification thật để có bản ghi trong DB) ───

    public function test_chuong_va_trang_thong_bao_hien_thi(): void
    {
        $booking = $this->makeBooking();
        $this->customer->notify(new BookingCreated($booking));

        $this->actingAs($this->customer)
            ->get(route('notifications.index'))
            ->assertOk()
            ->assertSee('Đặt sân thành công');

        // Chuông có badge trên navbar
        $this->actingAs($this->customer)
            ->get(route('customer.bookings.index'))
            ->assertOk()
            ->assertSee('notificationBell', false);
    }

    public function test_moi_vai_tro_mo_duoc_trang_thong_bao_con_khach_vang_lai_thi_khong(): void
    {
        // Chủ sân và admin dùng layout panel riêng
        $this->actingAs($this->owner)->get(route('notifications.index'))->assertOk()->assertSee('Thông báo');
        $this->actingAs($this->admin)->get(route('notifications.index'))->assertOk()->assertSee('Thông báo');

        auth()->logout();
        $this->get(route('notifications.index'))->assertRedirect(route('login'));
    }

    public function test_khong_doc_duoc_thong_bao_cua_nguoi_khac(): void
    {
        $booking = $this->makeBooking();
        $this->customer->notify(new BookingCreated($booking));

        $otherNotification = $this->customer->notifications()->first();

        // Admin không được đánh dấu đã đọc thông báo của khách
        $this->actingAs($this->admin)
            ->post(route('notifications.read', $otherNotification->id))
            ->assertNotFound();

        $this->assertNull($otherNotification->fresh()->read_at);
    }

    public function test_danh_dau_da_doc_va_danh_dau_tat_ca(): void
    {
        $booking = $this->makeBooking();
        $this->customer->notify(new BookingCreated($booking));
        $this->customer->notify(new BookingReminder($booking));

        $first = $this->customer->notifications()->first();

        $this->actingAs($this->customer)
            ->post(route('notifications.read', $first->id))
            ->assertRedirect();

        $this->assertNotNull($first->fresh()->read_at);
        $this->assertSame(1, $this->customer->unreadNotifications()->count());

        $this->actingAs($this->customer)
            ->post(route('notifications.read-all'))
            ->assertRedirect();

        $this->assertSame(0, $this->customer->unreadNotifications()->count());
    }
}
