<?php

namespace Tests\Feature\Customer;

use App\Models\Booking;
use App\Models\Court;
use App\Models\CourtSlot;
use App\Models\OperatingHour;
use App\Models\Sport;
use App\Models\User;
use App\Models\Venue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerBookingStoreTest extends TestCase
{
    use RefreshDatabase;

    private User $customer;

    private Court $court;

    // Ngày đặt = 3 ngày tới: luôn nằm trong cửa sổ 7 ngày, không phải hôm nay → né chặn giờ quá khứ
    private string $bookingDate;

    // day_of_week khớp ngày đặt (slot gắn thứ cụ thể)
    private int $slotDow;

    protected function setUp(): void
    {
        parent::setUp();

        $this->customer = User::factory()->create();
        $venue = Venue::factory()->create(['owner_id' => User::factory()->owner()->create()->id, 'status' => 'active']);
        $sport = Sport::create(['name' => 'Bóng đá', 'icon' => '⚽', 'is_active' => true]);
        $this->court = Court::create([
            'venue_id' => $venue->id,
            'sport_id' => $sport->id,
            'name'     => 'Sân 1',
            'status'   => 'active',
        ]);

        $target = now()->addDays(3);
        $this->bookingDate = $target->format('Y-m-d');
        $this->slotDow = $target->dayOfWeek;

        // Giờ hoạt động của khu sân: 5:00 - 22:00 mọi ngày (controller chặn theo operating_hours)
        for ($d = 0; $d <= 6; $d++) {
            OperatingHour::create([
                'venue_id'    => $venue->id,
                'day_of_week' => $d,
                'open_time'   => '05:00',
                'close_time'  => '22:00',
                'is_closed'   => false,
            ]);
        }
    }

    private function createStorePayload(string $date, string $start, string $end): array
    {
        return [
            'court_id'     => $this->court->id,
            'booking_date' => $date,
            'start_time'   => $start,
            'end_time'     => $end,
        ];
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function rejects_booking_when_court_has_no_slots(): void
    {
        // Chủ sân chưa cài khung giờ nào → không được đặt
        $response = $this->actingAs($this->customer)
            ->post('/bookings', $this->createStorePayload($this->bookingDate, '06:00', '07:00'));

        $response->assertSessionHas("error");
        $this->assertDatabaseMissing('bookings', ['court_id' => $this->court->id]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function rejects_booking_outside_configured_cells(): void
    {
        // Chủ sân chỉ mở 6:00 - 8:00; khách đòi đặt 9:00 - 10:00 → từ chối
        CourtSlot::create([
            'court_id'    => $this->court->id,
            'day_of_week' => $this->slotDow, // khớp ngày đặt
            'start_time'  => '06:00',
            'end_time'    => '08:00',
            'price'       => 200000,
        ]);

        $response = $this->actingAs($this->customer)
            ->post('/bookings', $this->createStorePayload($this->bookingDate, '09:00', '10:00'));

        $response->assertSessionHas("error");
        $this->assertDatabaseMissing('bookings', ['court_id' => $this->court->id]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function rejects_booking_misaligned_with_cell_boundaries(): void
    {
        // Mở 6:30 - 8:30 (cắt thành 6:30-7:30 + 7:30-8:30); khách đòi 7:00 - 8:00 → từ chối
        CourtSlot::create([
            'court_id'    => $this->court->id,
            'day_of_week' => $this->slotDow,
            'start_time'  => '06:30',
            'end_time'    => '08:30',
            'price'       => 240000,
        ]);

        $response = $this->actingAs($this->customer)
            ->post('/bookings', $this->createStorePayload($this->bookingDate, '07:00', '08:00'));

        $response->assertSessionHas("error");
        $this->assertDatabaseMissing('bookings', ['court_id' => $this->court->id]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function accepts_booking_exactly_matching_cells_with_correct_price(): void
    {
        // Mở 6:30 - 8:00 giá 250k/h → 6:30-7:30 (250k) + 7:30-8:00 (125k) = 375k
        CourtSlot::create([
            'court_id'    => $this->court->id,
            'day_of_week' => $this->slotDow,
            'start_time'  => '06:30',
            'end_time'    => '08:00',
            'price'       => 250000,
        ]);

        $response = $this->actingAs($this->customer)
            ->post('/bookings', $this->createStorePayload($this->bookingDate, '06:30', '08:00'));

        $response->assertRedirect();
        $booking = Booking::where('court_id', $this->court->id)->first();
        $this->assertNotNull($booking);
        $this->assertEquals(375000, (int) $booking->total_amount);
        $this->assertEquals('06:30', substr($booking->start_time, 0, 5));
        $this->assertEquals('08:00', substr($booking->end_time, 0, 5));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function applies_peak_price_within_booking(): void
    {
        // 18:00-20:00 giờ vàng 500k/h + 20:00-21:00 thường 200k/h
        // Đặt 19:00-21:00 = 500k (19-20 giờ vàng) + 200k = 700k
        CourtSlot::create([
            'court_id'    => $this->court->id,
            'day_of_week' => $this->slotDow,
            'start_time'  => '18:00',
            'end_time'    => '20:00',
            'price'       => 200000,
            'is_peak'     => true,
            'peak_price'  => 500000,
        ]);
        CourtSlot::create([
            'court_id'    => $this->court->id,
            'day_of_week' => $this->slotDow,
            'start_time'  => '20:00',
            'end_time'    => '21:00',
            'price'       => 200000,
        ]);

        $response = $this->actingAs($this->customer)
            ->post('/bookings', $this->createStorePayload($this->bookingDate, '19:00', '21:00'));

        $response->assertRedirect();
        $booking = Booking::where('court_id', $this->court->id)->first();
        $this->assertEquals(700000, (int) $booking->total_amount);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function rejects_booking_spanning_gap_between_frames(): void
    {
        // Mở 6:00-7:00 và 8:00-9:00 (trống 7:00-8:00); đặt vắt qua → từ chối
        CourtSlot::create([
            'court_id'    => $this->court->id,
            'day_of_week' => $this->slotDow,
            'start_time'  => '06:00',
            'end_time'    => '07:00',
            'price'       => 150000,
        ]);
        CourtSlot::create([
            'court_id'    => $this->court->id,
            'day_of_week' => $this->slotDow,
            'start_time'  => '08:00',
            'end_time'    => '09:00',
            'price'       => 150000,
        ]);

        $response = $this->actingAs($this->customer)
            ->post('/bookings', $this->createStorePayload($this->bookingDate, '06:00', '09:00'));

        $response->assertSessionHas("error");
        $this->assertDatabaseMissing('bookings', ['court_id' => $this->court->id]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function create_page_renders_with_slot_cells_config(): void
    {
        CourtSlot::create([
            'court_id'    => $this->court->id,
            'day_of_week' => $this->slotDow,
            'start_time'  => '06:30',
            'end_time'    => '08:00',
            'price'       => 250000,
        ]);

        $response = $this->actingAs($this->customer)
            ->get('/courts/' . $this->court->id . '/book');

        $response->assertOk();
        // Config Alpine phải chứa slotCells JSON với ô đã cắt
        $response->assertSee('06:30');
        $response->assertSee('07:30');
        // Không còn vòng lặp giờ cứng 5:00-22:00 trong view
        $response->assertDontSee('for (let hour = 5');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function create_page_does_not_load_alpine_from_cdn(): void
    {
        // Alpine chỉ được load 1 lần qua bundle Vite (app.js đăng ký bookingGrid).
        // Nếu load thêm bản CDN, Alpine CDN start trước → bookingGrid is not defined.
        $response = $this->actingAs($this->customer)
            ->get('/courts/' . $this->court->id . '/book');

        $response->assertOk();
        $response->assertDontSee('cdn.jsdelivr.net/npm/alpinejs');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function create_page_shows_no_slots_message_when_owner_has_not_configured(): void
    {
        $response = $this->actingAs($this->customer)
            ->get('/courts/' . $this->court->id . '/book');

        $response->assertOk();
        // hasNoSlots phải có trong DOM (Alpine sẽ hiện thông báo)
        $response->assertSee('hasNoSlots');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function accepts_booking_when_venue_has_no_operating_hours_configured(): void
    {
        // Khu sân chưa cài giờ hoạt động nào (bảng operating_hours rỗng)
        // ≠ "khu sân nghỉ" → chỉ cần có slot là được đặt
        OperatingHour::where('venue_id', $this->court->venue_id)->delete();
        CourtSlot::create([
            'court_id'    => $this->court->id,
            'day_of_week' => $this->slotDow,
            'start_time'  => '06:00',
            'end_time'    => '08:00',
            'price'       => 200000,
        ]);

        $response = $this->actingAs($this->customer)
            ->post('/bookings', $this->createStorePayload($this->bookingDate, '06:00', '07:00'));

        $response->assertRedirect();
        $this->assertDatabaseHas('bookings', ['court_id' => $this->court->id]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function create_page_does_not_flag_venue_without_operating_hours_as_closed(): void
    {
        // UI phải phân biệt "chưa cài giờ hoạt động" (không chặn, hiện lưới giờ)
        // với "đã cài mà ngày đó nghỉ" (banner nghỉ)
        OperatingHour::where('venue_id', $this->court->venue_id)->delete();

        $response = $this->actingAs($this->customer)
            ->get('/courts/' . $this->court->id . '/book');

        $response->assertOk();
        $response->assertSee('"operatingHours":[]', false);
        $response->assertSee('venueHasOperatingHours', false);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function create_page_config_contains_operating_hours(): void
    {
        // View dùng dayOperatingHour để phân biệt "khu sân nghỉ" và "chưa cài khung giờ"
        // → config Alpine phải mang operatingHours từ server
        $response = $this->actingAs($this->customer)
            ->get('/courts/' . $this->court->id . '/book');

        $response->assertOk();
        $response->assertSee('"operatingHours"', false);
        $response->assertSee('is_closed', false);
    }
}
