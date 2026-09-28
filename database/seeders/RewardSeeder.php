<?php

namespace Database\Seeders;

use App\Models\Reward;
use Illuminate\Database\Seeder;

class RewardSeeder extends Seeder
{
    public function run(): void
    {
        $rewards = [
            [
                'name' => 'Voucher 20.000đ',
                'slug' => 'voucher-20k',
                'description' => 'Giảm trực tiếp 20.000đ cho đơn đặt sân từ 100.000đ.',
                'points_required' => 50,
                'discount_type' => 'fixed',
                'discount_value' => 20_000,
                'min_amount' => 100_000,
                'max_discount' => null,
                'valid_days' => 30,
                'is_active' => true,
            ],
            [
                'name' => 'Voucher 50.000đ',
                'slug' => 'voucher-50k',
                'description' => 'Giảm trực tiếp 50.000đ cho đơn đặt sân từ 250.000đ.',
                'points_required' => 120,
                'discount_type' => 'fixed',
                'discount_value' => 50_000,
                'min_amount' => 250_000,
                'max_discount' => null,
                'valid_days' => 30,
                'is_active' => true,
            ],
            [
                'name' => 'Voucher giảm 10%',
                'slug' => 'voucher-10-percent',
                'description' => 'Giảm 10% tối đa 100.000đ cho đơn đặt sân từ 300.000đ.',
                'points_required' => 200,
                'discount_type' => 'percent',
                'discount_value' => 10,
                'min_amount' => 300_000,
                'max_discount' => 100_000,
                'valid_days' => 30,
                'is_active' => true,
            ],
        ];

        foreach ($rewards as $reward) {
            Reward::updateOrCreate(['slug' => $reward['slug']], $reward);
        }
    }
}
