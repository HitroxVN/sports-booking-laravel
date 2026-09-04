<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Thêm cổng thanh toán SePay (chuyển khoản ngân hàng qua webhook) vào enum gateway
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->enum('gateway', ['vnpay', 'momo', 'cash', 'sepay'])->change();
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->enum('gateway', ['vnpay', 'momo', 'cash'])->change();
        });
    }
};
