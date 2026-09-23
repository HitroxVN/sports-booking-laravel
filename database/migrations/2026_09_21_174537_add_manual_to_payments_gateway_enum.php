<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Thêm 'manual' vào enum gateway — hoàn tiền thủ công bởi chủ sân (BookingRefund)
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->enum('gateway', ['vnpay', 'momo', 'cash', 'sepay', 'manual'])->change();
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->enum('gateway', ['vnpay', 'momo', 'cash', 'sepay'])->change();
        });
    }
};
