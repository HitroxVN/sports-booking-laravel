<?php

namespace Tests\Feature\Customer;

use App\Models\Booking;
use App\Models\Court;
use App\Models\CourtSlot;
use App\Models\OperatingHour;
use App\Models\User;
use App\Models\Venue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class BookingStoreTest extends TestCase
{
    use RefreshDatabase;

    private User $customer;
    private Court $court;

    protected function setUp(): void
    {
        parent::setUp();

        $this->customer = User::factory()->create(['role' => 'customer', 'email_verified_at' => now()]);

        $sport = \App\Models\Sport::create(['name' => 'Bóng đá', 'slug' => 'bong-da']);

        $venue = Venue::create([
            'name'    => 'Sân Test',
            'slug'    => 'san-test',
            'owner_id' => $this->customer->id,
            'phone'   => '0900000000',
            'city'    => 'TP.HCM',
            'district' => 'Q1',
            'address' => '1 Đường Test',
            'status'  => 'active',
        ]);

        $this->court = Court::create([
            'venue_id'  => $venue->id,
            'sport_id'  => $sport->id,
            'name'      => 'Sân 1',
            'status'    => 'active',
        ]);

        // Giờ hoạt động: 5:00 - 22:00 tất cả các ngày
        for ($d = 0; $d <= 6; $d++) {
            OperatingHour::create([
                'venue_id'    => $this->court->venue_id,
                'day_of_week' => $d,
                'open_time'   => '05:00:00',
                'close_time'  => '22:00:00',
                'is_closed'   => false,
            ]);
        }

        // Slot giá: 05-17 = 80k, 17-22 = 120k (peak), áp dụng mọi ngày (day_of_week null)
        CourtSlot::create([
            'court_id'    => $this->court->id,
            'day_of_week' => null,
            'start_time'  => '05:00:00',
            'end_time'    => '17:00:00',
            'price'       => 80000,
            'is_peak'     => false,
        ]);
        CourtSlot::create([
            'court_id'    => $this->court->id,
            'day_of_week' => null,
            'start_time'  => '17:00:00',
            'end_time'    => '22:00:00',
            'price'       => 120000,
            'peak_price'  => 120000,
            'is_peak'     => true,
        ]);
    }

    private function validPayload(array $overrides = []): array
    {
        $date = now()->addDays(3)->format('Y-m-d');

        return array_merge([
            'court_id'     => $this->court->id,
            'booking_date' => $date,
            'start_time'   => '07:00',
            'end_time'     => '09:00',
        ], $overrides);
    }

    public function test_dat_san_thanh_cong_tinh_dung_gia_theo_slot(): void
    {
        $response = $this->actingAs($this->customer)
            ->post(route('customer.bookings.store'), $this->validPayload());

        $response->assertRedirect();
        $booking = Booking::latest('id')->first();

        $this->assertNotNull($booking);
        // 07-09 giờ thường: 2 giờ x 80k
        $this->assertEquals(160000, $booking->total_amount);
        $this->assertEquals('full_online', $booking->payment_method);
        $this->assertEquals('pending', $booking->status);
        // Mã mới không chứa dấu gạch
        $this->assertMatchesRegularExpression('/^BK[A-Z0-9]{8}$/', $booking->code);
    }

    public function test_gio_peak_duoc_tinh_dung(): void
    {
        $this->actingAs($this->customer)
            ->post(route('customer.bookings.store'), $this->validPayload([
                'start_time' => '18:00',
                'end_time'   => '20:00',
            ]));

        // 18-20 giờ peak: 2 giờ x 120k
        $this->assertEquals(240000, Booking::latest('id')->first()->total_amount);
    }

    public function test_chan_dat_ngoai_gio_hoat_dong(): void
    {
        $response = $this->actingAs($this->customer)
            ->post(route('customer.bookings.store'), $this->validPayload([
                'start_time' => '04:00',
                'end_time'   => '05:00',
            ]));

        $response->assertSessionHas('error');
        $this->assertEquals(0, Booking::count());
    }

    public function test_chan_dat_khung_khong_co_gia_slot(): void
    {
        // 05-17 và 17-22 có giá; khung 16-18 vắt qua 2 slot nhưng 16-17 + 17-18 đều có slot
        // → dùng khung ngoài phủ: xóa slot 17-22 để 18-19 không còn giá
        CourtSlot::where('court_id', $this->court->id)->where('start_time', '17:00:00')->delete();

        $response = $this->actingAs($this->customer)
            ->post(route('customer.bookings.store'), $this->validPayload([
                'start_time' => '18:00',
                'end_time'   => '19:00',
            ]));

        $response->assertSessionHas('error');
        $this->assertEquals(0, Booking::count());
    }

    public function test_chan_dat_gio_da_qua_trong_ngay(): void
    {
        // Ngày mai: set date vào quá khứ bằng cách đặt hôm nay với giờ nhỏ hơn hiện tại
        $response = $this->actingAs($this->customer)
            ->post(route('customer.bookings.store'), $this->validPayload([
                'booking_date' => now()->format('Y-m-d'),
                'start_time'   => '05:00',
                'end_time'     => '06:00',
            ]));

        // 05:00 hôm nay đã qua (trừ khi test chạy lúc nửa đêm) → bị chặn
        if (now()->hour >= 6) {
            $response->assertSessionHas('error');
            $this->assertEquals(0, Booking::count());
        } else {
            $this->markTestSkipped('Test chạy sau nửa đêm, bỏ qua case giờ đã qua.');
        }
    }

    public function test_chan_dat_quá_7_ngay(): void
    {
        $response = $this->actingAs($this->customer)
            ->post(route('customer.bookings.store'), $this->validPayload([
                'booking_date' => now()->addDays(8)->format('Y-m-d'),
            ]));

        $response->assertSessionHasErrors('booking_date');
    }

    public function test_chan_dat_trung_khung_gio(): void
    {
        // Tạo sẵn booking trùng khung
        Booking::create([
            'code'           => 'BKAAAAAAA1',
            'user_id'        => $this->customer->id,
            'court_id'       => $this->court->id,
            'booking_date'   => now()->addDays(3)->format('Y-m-d'),
            'start_time'     => '07:00',
            'end_time'       => '09:00',
            'duration'       => 120,
            'price_snapshot' => 80000,
            'total_amount'   => 160000,
            'payment_method' => 'full_online',
            'status'         => 'pending',
        ]);

        $response = $this->actingAs($this->customer)
            ->post(route('customer.bookings.store'), $this->validPayload());

        if (!$response->getSession()->has('error')) {
            \DB::enableQueryLog();
            $raw = \DB::table('bookings')->get(['id','booking_date','start_time','end_time','status']);
            dump($raw->toArray());
            // Tái hiện query conflict
            $hit = Booking::where('court_id', $this->court->id)
                ->where('booking_date', now()->addDays(3)->format('Y-m-d'))
                ->where('status', '!=', 'cancelled')
                ->where('start_time', '<', '09:00')
                ->where('end_time', '>', '07:00')
                ->toSql();
            dump($hit);
        }
        $response->assertSessionHas('error');
        $this->assertEquals(1, Booking::count());
    }

    public function test_san_nghi_ngay_khong_dat_duoc(): void
    {
        // CN (0) nghỉ
        OperatingHour::where('venue_id', $this->court->venue_id)->where('day_of_week', 0)->update(['is_closed' => true]);

        // Tìm CN kế tiếp trong phạm vi 7 ngày cho phép (tối đa today+6)
        $sunday = now()->copy();
        while ($sunday->dayOfWeek !== 0) {
            $sunday->addDay();
        }

        if ($sunday->isToday() || $sunday->gt(now()->addDays(6)->endOfDay())) {
            $this->markTestSkipped('CN không nằm trong khoảng đặt được từ hôm nay, bỏ qua.');
        }

        $response = $this->actingAs($this->customer)
            ->post(route('customer.bookings.store'), $this->validPayload([
                'booking_date' => $sunday->format('Y-m-d'),
            ]));

        $response->assertSessionHas('error');
        $this->assertEquals(0, Booking::count());
    }

    public function test_slot_day_of_week_chi_ap_dung_dung_ngay(): void
    {
        // Slot chỉ áp dụng thứ 2 (dow=1) giá 50k
        CourtSlot::create([
            'court_id'    => $this->court->id,
            'day_of_week' => 1,
            'start_time'  => '05:00:00',
            'end_time'    => '17:00:00',
            'price'       => 50000,
            'is_peak'     => false,
        ]);

        // Tìm thứ 2 kế tiếp
        $monday = now()->addDays(3);
        while ($monday->dayOfWeek !== 1) {
            $monday->addDay();
        }

        $this->actingAs($this->customer)
            ->post(route('customer.bookings.store'), $this->validPayload([
                'booking_date' => $monday->format('Y-m-d'),
            ]));

        // dow=1 → slot 50k được ưu tiên (first trong collection), không dùng slot null 80k
        $this->assertEquals(100000, Booking::latest('id')->first()->total_amount);
    }
}
