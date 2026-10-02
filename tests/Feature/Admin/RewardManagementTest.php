<?php

namespace Tests\Feature\Admin;

use App\Models\LoyaltyTransaction;
use App\Models\Reward;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RewardManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $customer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->admin()->create();
        $this->customer = User::factory()->create(['role' => 'customer']);
    }

    public function test_non_admin_cannot_access_rewards_management(): void
    {
        $this->actingAs($this->customer)
            ->get(route('admin.rewards.index'))
            ->assertForbidden();

        $this->get(route('admin.rewards.index'))
            ->assertForbidden();
    }

    public function test_admin_can_view_rewards_index(): void
    {
        Reward::create([
            'name' => 'Voucher Test 20k',
            'slug' => 'voucher-test-20k',
            'points_required' => 50,
            'discount_type' => 'fixed',
            'discount_value' => 20000,
            'valid_days' => 30,
            'is_active' => true,
        ]);

        $this->actingAs($this->admin)
            ->get(route('admin.rewards.index'))
            ->assertOk()
            ->assertSee('Voucher Test 20k')
            ->assertSee('50 điểm');
    }

    public function test_admin_can_create_a_new_reward(): void
    {
        $payload = [
            'name' => 'Voucher Mùa Hè 15%',
            'description' => 'Ưu đãi dịp hè',
            'points_required' => 150,
            'discount_type' => 'percent',
            'discount_value' => 15,
            'min_amount' => 200000,
            'max_discount' => 50000,
            'valid_days' => 45,
            'is_active' => 1,
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('admin.rewards.store'), $payload);

        $response->assertRedirect()->assertSessionHas('success');

        $this->assertDatabaseHas('rewards', [
            'name' => 'Voucher Mùa Hè 15%',
            'points_required' => 150,
            'discount_type' => 'percent',
            'discount_value' => 15,
            'valid_days' => 45,
            'is_active' => 1,
        ]);
    }

    public function test_reward_creation_validates_percent_max(): void
    {
        $payload = [
            'name' => 'Voucher Lỗi',
            'points_required' => 100,
            'discount_type' => 'percent',
            'discount_value' => 120, // > 100%
            'valid_days' => 30,
        ];

        $this->actingAs($this->admin)
            ->post(route('admin.rewards.store'), $payload)
            ->assertSessionHasErrors(['discount_value']);
    }

    public function test_admin_can_update_a_reward(): void
    {
        $reward = Reward::create([
            'name' => 'Voucher Ban Đầu',
            'slug' => 'voucher-ban-dau',
            'points_required' => 60,
            'discount_type' => 'fixed',
            'discount_value' => 30000,
            'valid_days' => 30,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)
            ->patch(route('admin.rewards.update', $reward), [
                'name' => 'Voucher Đã Sửa',
                'description' => 'Mô tả mới',
                'points_required' => 80,
                'discount_type' => 'fixed',
                'discount_value' => 40000,
                'valid_days' => 60,
                'is_active' => 0,
            ]);

        $response->assertRedirect()->assertSessionHas('success');

        $this->assertDatabaseHas('rewards', [
            'id' => $reward->id,
            'name' => 'Voucher Đã Sửa',
            'points_required' => 80,
            'discount_value' => 40000,
            'valid_days' => 60,
            'is_active' => 0,
        ]);
    }

    public function test_admin_can_delete_unused_reward(): void
    {
        $reward = Reward::create([
            'name' => 'Voucher Chưa Dùng',
            'slug' => 'voucher-chua-dung',
            'points_required' => 50,
            'discount_type' => 'fixed',
            'discount_value' => 20000,
            'valid_days' => 30,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)
            ->delete(route('admin.rewards.destroy', $reward));

        $response->assertRedirect()->assertSessionHas('success');
        $this->assertDatabaseMissing('rewards', ['id' => $reward->id]);
    }

    public function test_deleting_reward_with_transactions_deactivates_it_instead(): void
    {
        $reward = Reward::create([
            'name' => 'Voucher Đã Có Lượt Đổi',
            'slug' => 'voucher-da-co-luot-doi',
            'points_required' => 50,
            'discount_type' => 'fixed',
            'discount_value' => 20000,
            'valid_days' => 30,
            'is_active' => true,
        ]);

        LoyaltyTransaction::create([
            'user_id' => $this->customer->id,
            'reward_id' => $reward->id,
            'type' => 'redeem',
            'points' => -50,
            'balance_after' => 0,
            'voucher_code' => 'ARENA-TESTVOUCHER',
            'reference' => 'voucher:ARENA-TESTVOUCHER',
            'description' => 'Đổi voucher test',
            'status' => 'available',
        ]);

        $response = $this->actingAs($this->admin)
            ->delete(route('admin.rewards.destroy', $reward));

        $response->assertRedirect()->assertSessionHas('warning');

        // Bảng không bị xóa mà chỉ chuyển is_active = false
        $this->assertDatabaseHas('rewards', [
            'id' => $reward->id,
            'is_active' => 0,
        ]);
    }
}
