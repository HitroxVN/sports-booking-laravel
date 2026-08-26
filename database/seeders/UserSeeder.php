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

        // Chủ sân phụ
        $owners = [
            ['email' => 'owner2@gmail.com',  'name' => 'Chủ Sân Quận 7',   'phone' => '0912345678'],
            ['email' => 'owner3@gmail.com',  'name' => 'Chủ Sân Bình Thạnh', 'phone' => '0923456789'],
        ];
        foreach ($owners as $owner) {
            User::firstOrCreate(['email' => $owner['email']], [
                'name'              => $owner['name'],
                'password'          => Hash::make('12345678'),
                'phone'             => $owner['phone'],
                'role'              => 'owner',
                'status'            => 'active',
                'email_verified_at' => now(),
            ]);
        }

        // Khách hàng phụ
        $customers = [
            ['email' => 'khach1@gmail.com', 'name' => 'Nguyễn Văn An',    'phone' => '0901111222'],
            ['email' => 'khach2@gmail.com', 'name' => 'Trần Thị Bình',    'phone' => '0903333444'],
            ['email' => 'khach3@gmail.com', 'name' => 'Lê Văn Cường',     'phone' => '0905555666'],
            ['email' => 'khach4@gmail.com', 'name' => 'Phạm Thị Dung',    'phone' => '0907777888'],
            ['email' => 'khach5@gmail.com', 'name' => 'Hoàng Văn Em',     'phone' => '0909999000'],
        ];
        foreach ($customers as $customer) {
            User::firstOrCreate(['email' => $customer['email']], [
                'name'              => $customer['name'],
                'password'          => Hash::make('12345678'),
                'phone'             => $customer['phone'],
                'role'              => 'customer',
                'status'            => 'active',
                'email_verified_at' => now(),
            ]);
        }
    }
}
