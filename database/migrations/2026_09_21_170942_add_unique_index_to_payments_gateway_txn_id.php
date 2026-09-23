<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Idempotency webhook: 2 webhook trùng đến song song không được tạo 2 payment
        Schema::table('payments', function (Blueprint $table) {
            $table->unique(['gateway', 'gateway_txn_id'], 'payments_gateway_txn_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropUnique('payments_gateway_txn_unique');
        });
    }
};
