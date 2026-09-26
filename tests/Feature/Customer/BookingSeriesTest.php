<?php

namespace Tests\Feature\Customer;

use App\Models\Booking;
use App\Models\BookingSeries;
use App\Models\Court;
use App\Models\CourtSlot;
use App\Models\OperatingHour;
use App\Models\Sport;
use App\Models\User;
use App\Models\Venue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingSeriesTest extends TestCase
{
    use RefreshDatabase;

    private User $customer;
    private Court $court;

    protected function setUp(): void
    {
        parent::setUp();

        $this->customer = User::factory()->create(['role' => 'customer', 'email_verified_at' => now()]);

        $sport = Sport::create(['name' => 'Bóng đá', 'slug' => 'bong-da']);

        $venue = Venue::create([
            'name'     => 'Sân Test',
            'slug'     => 'san-test',
            'owner_id' => $this->customer->id,
            'phone'    => '0900000000',
            'city'     => 'TP.HCM',
            'district' => 'Q1',
            'address'  => '1 Đường Test',
            'status'   => 'active',
        ]);

        $this->court = Court::create([
            'venue_id' => $venue->id,
            'sport_id' => $sport->id,
            'name'     => 'Sân 1',
            'status'   => 'active',
        ]);

        for ($d = 0; $d <= 6; $d++) {
            OperatingHour::create([
                'venue_id'    => $venue->id,
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
            'end_time'    => '17:00:00',
            'price'       => 80000,
            'is_peak'     => false,
        ]);
    }

    private function payload(array $overrides = []): array
    {
        return array_merge([
            'court_id'     => $this->court->id,
            'booking_date' => now()->addDays(3)->format('Y-m-d'),
            'start_time'   => '07:00',
            'end_time'     => '08:00',
            'repeat_weeks' => 4,
        ], $overrides);
    }

    public function test_dat_lich_co_dinh_tao_du_cac_buoi_hang_tuan(): void
    {
        $this->actingAs($this->customer)
            ->post(route('customer.bookings.store'), $this->payload())
            ->assertRedirect(route('customer.bookings.index'));

        $this->assertEquals(4, Booking::count());
        $this->assertEquals(1, BookingSeries::count());

        $series = BookingSeries::first();
        $this->assertEquals(4, $series->weeks);
        $this->assertEquals($this->court->id, $series->court_id);

        // Các buổi cách nhau đúng 7 ngày, cùng thứ với buổi đầu
        $dates = Booking::orderBy('booking_date')->pluck('booking_date');
        $this->assertEquals(
            now()->addDays(3)->format('Y-m-d'),
            $dates[0]->format('Y-m-d')
        );
        foreach ($dates as $i => $date) {
            $this->assertEquals(
                now()->addDays(3)->addWeeks($i)->format('Y-m-d'),
                $date->format('Y-m-d')
            );
        }

        // Lịch cố định chốt sân luôn, thanh toán tại sân mỗi buổi
        Booking::all()->each(function (Booking $booking) use ($series) {
            $this->assertEquals('confirmed', $booking->status);
            $this->assertEquals('at_venue', $booking->payment_method);
            $this->assertEquals($series->id, $booking->series_id);
        });
    }

    public function test_xung_dot_mot_buoi_thi_huy_toan_bo_chuoi(): void
    {
        // Chặn sẵn buổi thứ 2 của chuỗi
        $secondDate = now()->addDays(3)->addWeeks(1)->format('Y-m-d');
        Booking::create([
            'code'           => 'BKBLOCK01',
            'user_id'        => $this->customer->id,
            'court_id'       => $this->court->id,
            'booking_date'   => $secondDate,
            'start_time'     => '07:00:00',
            'end_time'       => '08:00:00',
            'duration'       => 60,
            'price_snapshot' => 80000,
            'total_amount'   => 80000,
            'payment_method' => 'full_online',
            'status'         => 'confirmed',
        ]);

        $response = $this->actingAs($this->customer)
            ->post(route('customer.bookings.store'), $this->payload());

        $response->assertSessionHas('error');

        // Rollback toàn bộ: không buổi nào được tạo, không có chuỗi
        $this->assertEquals(1, Booking::count());
        $this->assertEquals(0, BookingSeries::count());
    }

    public function test_trang_dat_san_co_lua_chon_lap_tuan(): void
    {
        $this->actingAs($this->customer)
            ->get(route('customer.bookings.create', $this->court->id))
            ->assertOk()
            ->assertSee('Lặp lại hàng tuần');
    }

    public function test_vuot_qua_so_tuan_toi_da_bi_chan(): void
    {
        $this->actingAs($this->customer)
            ->post(route('customer.bookings.store'), $this->payload(['repeat_weeks' => 13]))
            ->assertSessionHasErrors('repeat_weeks');

        $this->assertEquals(0, Booking::count());
    }
}
