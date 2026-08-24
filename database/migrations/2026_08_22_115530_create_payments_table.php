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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();  // đơn đặt sân
            $table->enum('gateway', ['vnpay', 'momo', 'cash']);                 // cổng thanh toán
            $table->string('gateway_txn_id')->nullable()->index();              // mã GD từ cổng (idempotency)
            $table->decimal('amount', 10, 2);                                   // số tiền
            $table->enum('type', ['deposit', 'full', 'refund']);                // loại giao dịch
            $table->enum('status', ['pending', 'success', 'failed', 'refunded'])
                  ->default('pending');                                          // trạng thái
            $table->json('gateway_response')->nullable();                       // raw response từ cổng
            $table->timestamp('paid_at')->nullable();                           // thời điểm thành công
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
