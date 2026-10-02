<?php

namespace Tests\Feature\Customer;

use App\Models\Booking;
use App\Models\Court;
use App\Models\LoyaltyTransaction;
use App\Models\Reward;
use App\Models\Sport;
use App\Models\User;
use App\Models\Venue;
use App\Services\LoyaltyService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class LoyaltyProgramTest extends TestCase
{
    use RefreshDatabase;

    private User $customer;

    private Court $court;

    protected function setUp(): void
    {
        parent::setUp();

        $this->customer = User::factory()->create();
        $owner = User::factory()->owner()->create();
        $venue = Venue::factory()->for($owner, 'owner')->create();
        $sport = Sport::create(['name' => 'Bóng đá', 'is_active' => true]);
        $this->court = Court::create([
            'venue_id' => $venue->id,
            'sport_id' => $sport->id,
            'name' => 'Sân A',
            'status' => 'active',
        ]);
    }

    public function test_points_are_awarded_once_when_booking_becomes_fully_paid(): void
    {
        $booking = $this->booking(['total_amount' => 359_999]);

        $booking->update([
            'payment_status' => 'fully_paid',
            'status' => 'confirmed',
        ]);
        $booking->update(['notes' => 'Không được cộng lại khi sửa thông tin khác']);

        $this->assertSame(35, $this->customer->refresh()->points);
        $this->assertSame('bronze', $this->customer->tier);
        $this->assertDatabaseCount('loyalty_transactions', 1);
        $this->assertDatabaseHas('loyalty_transactions', [
            'user_id' => $this->customer->id,
            'booking_id' => $booking->id,
            'type' => 'earn',
            'points' => 35,
            'balance_after' => 35,
            'reference' => "booking:{$booking->id}:earn",
        ]);
    }

    public function test_deposit_payment_does_not_award_points(): void
    {
        $booking = $this->booking();

        $booking->update(['payment_status' => 'deposit_paid']);

        $this->assertSame(0, $this->customer->refresh()->points);
        $this->assertDatabaseCount('loyalty_transactions', 0);
    }

    public function test_member_tier_uses_lifetime_earned_points(): void
    {
        $booking = $this->booking(['total_amount' => 1_000_000]);

        $booking->update(['payment_status' => 'fully_paid']);

        $this->assertSame(100, $this->customer->refresh()->points);
        $this->assertSame('silver', $this->customer->tier);
    }

    public function test_customer_can_redeem_points_and_see_voucher_on_loyalty_page(): void
    {
        $this->customer->forceFill(['points' => 100])->save();
        $reward = $this->reward();

        $response = $this->actingAs($this->customer)
            ->post(route('customer.loyalty.rewards.redeem', $reward));

        $response->assertRedirect()->assertSessionHas('success');
        $this->assertSame(50, $this->customer->refresh()->points);

        $voucher = LoyaltyTransaction::where('type', 'redeem')->firstOrFail();
        $this->assertSame(-50, $voucher->points);
        $this->assertNotNull($voucher->voucher_code);

        $this->actingAs($this->customer)
            ->get(route('customer.loyalty.index'))
            ->assertOk()
            ->assertSee('50')
            ->assertSee($voucher->voucher_code)
            ->assertSee('Lịch sử điểm thưởng');

        $this->actingAs($this->customer)
            ->get(route('profile.edit'))
            ->assertOk()
            ->assertDontSee('Lịch sử điểm thưởng');
    }

    public function test_customer_cannot_redeem_reward_without_enough_points(): void
    {
        $reward = $this->reward();

        $this->actingAs($this->customer)
            ->post(route('customer.loyalty.rewards.redeem', $reward))
            ->assertRedirect()
            ->assertSessionHas('error');

        $this->assertSame(0, $this->customer->refresh()->points);
        $this->assertDatabaseMissing('loyalty_transactions', ['type' => 'redeem']);
    }

    public function test_redeemed_voucher_can_be_applied_to_payment_once(): void
    {
        $this->customer->forceFill(['points' => 100])->save();
        $voucher = app(LoyaltyService::class)->redeem($this->customer, $this->reward());
        $booking = $this->booking(['total_amount' => 300_000]);

        $this->actingAs($this->customer)
            ->get(route('customer.bookings.pay', $booking))
            ->assertOk()
            ->assertSee($voucher->voucher_code)
            ->assertSee('Áp dụng');

        $this->actingAs($this->customer)
            ->post(route('customer.bookings.loyalty-voucher.apply', $booking), [
                'loyalty_transaction_id' => $voucher->id,
            ])
            ->assertRedirect(route('customer.bookings.pay', $booking))
            ->assertSessionHas('success');

        $booking->refresh();
        $voucher->refresh();

        $this->assertSame('280000.00', $booking->total_amount);
        $this->assertSame('20000.00', $booking->discount_amount);
        $this->assertSame($voucher->id, $booking->loyalty_transaction_id);
        $this->assertSame($booking->id, $voucher->booking_id);
        $this->assertNotNull($voucher->used_at);

        $otherBooking = $this->booking(['total_amount' => 300_000]);
        $this->actingAs($this->customer)
            ->post(route('customer.bookings.loyalty-voucher.apply', $otherBooking), [
                'loyalty_transaction_id' => $voucher->id,
            ])
            ->assertSessionHas('error');

        $this->assertNull($otherBooking->refresh()->loyalty_transaction_id);
    }

    public function test_unpaid_cancelled_booking_releases_applied_voucher(): void
    {
        $this->customer->forceFill(['points' => 100])->save();
        $voucher = app(LoyaltyService::class)->redeem($this->customer, $this->reward());
        $booking = $this->booking(['total_amount' => 300_000]);

        app(LoyaltyService::class)->applyVoucher($booking, $voucher, $this->customer);
        $booking->refresh()->update(['status' => 'cancelled']);

        $this->assertSame('300000.00', $booking->refresh()->total_amount);
        $this->assertSame('0.00', $booking->discount_amount);
        $this->assertNull($booking->loyalty_transaction_id);
        $this->assertNull($voucher->refresh()->booking_id);
        $this->assertNull($voucher->used_at);
        $this->assertTrue($voucher->isAvailableVoucher());
    }

    private function booking(array $attributes = []): Booking
    {
        return Booking::create(array_merge([
            'code' => 'BK'.Str::upper(Str::random(8)),
            'user_id' => $this->customer->id,
            'court_id' => $this->court->id,
            'booking_date' => today()->addDay(),
            'start_time' => '18:00:00',
            'end_time' => '20:00:00',
            'duration' => 120,
            'price_snapshot' => 150_000,
            'total_amount' => 300_000,
            'discount_amount' => 0,
            'status' => 'pending',
            'payment_method' => 'full_online',
            'payment_status' => 'unpaid',
        ], $attributes));
    }

    private function reward(array $attributes = []): Reward
    {
        return Reward::create(array_merge([
            'name' => 'Voucher 20.000đ',
            'slug' => 'voucher-20k-'.Str::lower(Str::random(6)),
            'points_required' => 50,
            'discount_type' => 'fixed',
            'discount_value' => 20_000,
            'min_amount' => 100_000,
            'valid_days' => 30,
            'is_active' => true,
        ], $attributes));
    }
}
