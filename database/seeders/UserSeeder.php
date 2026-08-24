<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        User::firstOrCreate(['email' => 'admin@gmail.com'], [
            'name'              => 'Admin',
            'password'          => Hash::make('12345678'),
            'role'              => 'admin',
            'status'            => 'active',
            'email_verified_at' => now(),
        ]);

        // Chủ sân
        User::firstOrCreate(['email' => 'owner@gmail.com'], [
            'name'              => 'Chủ Sân Mẫu',
            'password'          => Hash::make('12345678'),
            'phone'             => '0901234567',
            'role'              => 'owner',
            'status'            => 'active',
            'email_verified_at' => now(),
        ]);

        // Khách hàng
        User::firstOrCreate(['email' => 'customer@gmail.com'], [
            'name'              => 'Khách Hàng Mẫu',
            'password'          => Hash::make('12345678'),
            'phone'             => '0909876543',
            'role'              => 'customer',
            'status'            => 'active',
            'email_verified_at' => now(),
        ]);
    }
}
