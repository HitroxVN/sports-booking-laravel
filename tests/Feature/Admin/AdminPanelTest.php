<?php

namespace Tests\Feature\Admin;

use App\Models\Booking;
use App\Models\Court;
use App\Models\Sport;
use App\Models\User;
use App\Models\Venue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class AdminPanelTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->admin()->create();
    }

    private function owner(): User
    {
        return User::factory()->owner()->create();
    }

    private function customer(): User
    {
        return User::factory()->create(); // role mặc định = customer
    }

    private function createVenueWithCourt(array $venueOverrides = [], array $courtOverrides = []): Court
    {
        $owner = $this->owner();
        $venue = Venue::factory()->create(array_merge(['owner_id' => $owner->id, 'status' => 'active'], $venueOverrides));
        $sport = Sport::create(['name' => 'Bóng đá', 'icon' => '⚽', 'is_active' => true]);
        return Court::create(array_merge([
            'venue_id'    => $venue->id,
            'sport_id'    => $sport->id,
            'name'        => 'Sân 1',
            'status'      => 'active',
        ], $courtOverrides));
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/admin/dashboard')->assertRedirect('/login');
    }

    public function test_owner_cannot_access_admin(): void
    {
        $this->actingAs($this->owner())
            ->get('/admin/dashboard')
            ->assertStatus(403);
    }

    public function test_admin_dashboard_loads_with_stats(): void
    {
        $this->actingAs($this->admin())
            ->get('/admin/dashboard')
            ->assertStatus(200)
            ->assertSee('Tổng Khách Hàng');
    }

    public function test_users_index_lists_and_filters(): void
    {
        $owner = $this->owner();
        $customer = $this->customer();

        $this->actingAs($this->admin())
            ->get('/admin/users')
            ->assertStatus(200)
            ->assertSee($customer->email)
            ->assertSee($owner->email)
            ->assertDontSee('admin@gmail.com');

        $this->actingAs($this->admin())
            ->get('/admin/users?role=owner')
            ->assertStatus(200)
            ->assertSee($owner->email)
            ->assertDontSee($customer->email);
    }

    public function test_ban_and_unban_user(): void
    {
        $user = $this->customer();

        $this->actingAs($this->admin())
            ->post("/admin/users/{$user->id}/ban")
            ->assertRedirect();

        $this->assertSame('banned', $user->fresh()->status);

        $this->actingAs($this->admin())
            ->post("/admin/users/{$user->id}/unban")
            ->assertRedirect();

        $this->assertSame('active', $user->fresh()->status);
    }

    public function test_admin_cannot_ban_self_or_other_admin(): void
    {
        $admin = $this->admin();
        $otherAdmin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->post("/admin/users/{$admin->id}/ban")
            ->assertStatus(403);

        $this->actingAs($admin)
            ->post("/admin/users/{$otherAdmin->id}/ban")
            ->assertStatus(403);
    }

    public function test_banned_user_cannot_login(): void
    {
        $user = $this->customer();
        $user->update(['status' => 'banned']);

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_venues_index_and_approve(): void
    {
        $owner = $this->owner();
        $venue = Venue::factory()->create(['owner_id' => $owner->id, 'status' => 'pending']);

        $this->actingAs($this->admin())
            ->get('/admin/venues')
            ->assertStatus(200);

        $this->actingAs($this->admin())
            ->post("/admin/venues/{$venue->id}/approve")
            ->assertRedirect();

        $this->assertSame('active', $venue->fresh()->status);
    }

    public function test_venues_reject_saves_reason(): void
    {
        $owner = $this->owner();
        $venue = Venue::factory()->create(['owner_id' => $owner->id, 'status' => 'pending']);

        $this->actingAs($this->admin())
            ->post("/admin/venues/{$venue->id}/reject", ['reason' => 'Thiếu giấy phép'])
            ->assertRedirect();

        $venue->refresh();
        $this->assertSame('rejected', $venue->status);
        $this->assertSame('Thiếu giấy phép', $venue->reject_reason);
    }

    public function test_venues_reject_requires_reason(): void
    {
        $owner = $this->owner();
        $venue = Venue::factory()->create(['owner_id' => $owner->id, 'status' => 'pending']);

        $this->actingAs($this->admin())
            ->post("/admin/venues/{$venue->id}/reject", ['reason' => ''])
            ->assertSessionHasErrors('reason');
    }

    public function test_bookings_index_is_read_only(): void
    {
        $user = $this->customer();
        $court = $this->createVenueWithCourt();
        Booking::create([
            'code'           => 'BK-TEST-001',
            'user_id'        => $user->id,
            'court_id'       => $court->id,
            'booking_date'   => now(),
            'start_time'     => '10:00:00',
            'end_time'       => '11:00:00',
            'duration'       => 60,
            'price_snapshot' => 200000,
            'total_amount'   => 200000,
            'deposit_amount' => 50000,
            'status'         => 'pending',
            'payment_method' => 'at_venue',
            'payment_status' => 'unpaid',
        ]);

        $this->actingAs($this->admin())
            ->get('/admin/bookings')
            ->assertStatus(200)
            ->assertSee('BK-TEST-001');
    }

    public function test_sports_crud(): void
    {
        $this->actingAs($this->admin());

        // store
        $this->post('/admin/sports', ['name' => 'Bóng chuyền', 'icon' => UploadedFile::fake()->image('icon.png')])
            ->assertRedirect();
        $this->assertDatabaseHas('sports', ['name' => 'Bóng chuyền']);

        $sport = Sport::where('name', 'Bóng chuyền')->first();

        // update
        $this->patch("/admin/sports/{$sport->id}", ['name' => 'Bóng chuyền bãi biển', 'icon' => UploadedFile::fake()->image('icon.png')])
            ->assertRedirect();
        $this->assertDatabaseHas('sports', ['name' => 'Bóng chuyền bãi biển']);

        // destroy
        $this->delete("/admin/sports/{$sport->id}")
            ->assertRedirect();
        $this->assertDatabaseMissing('sports', ['name' => 'Bóng chuyền bãi biển']);
    }

    public function test_cannot_delete_sport_with_courts(): void
    {
        $sport = Sport::create(['name' => 'Bóng đá', 'icon' => '⚽', 'is_active' => true]);
        $this->createVenueWithCourt([], ['sport_id' => $sport->id]);

        $this->actingAs($this->admin())
            ->delete("/admin/sports/{$sport->id}")
            ->assertRedirect()
            ->assertSessionHas('error');

        $this->assertDatabaseHas('sports', ['id' => $sport->id]);
    }

    public function test_reports_index_and_export(): void
    {
        $user = $this->customer();
        $court = $this->createVenueWithCourt();
        Booking::create([
            'code'           => 'BK-REPORT-001',
            'user_id'        => $user->id,
            'court_id'       => $court->id,
            'booking_date'   => now(),
            'start_time'     => '10:00:00',
            'end_time'       => '11:00:00',
            'duration'       => 60,
            'price_snapshot' => 200000,
            'total_amount'   => 200000,
            'deposit_amount' => 50000,
            'status'         => 'completed',
            'payment_method' => 'at_venue',
            'payment_status' => 'fully_paid',
        ]);

        $this->actingAs($this->admin())
            ->get('/admin/reports')
            ->assertStatus(200)
            ->assertSee('200.000');

        $response = $this->actingAs($this->admin())
            ->get('/admin/reports/export');
        $response->assertStatus(200);
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
    }
}
