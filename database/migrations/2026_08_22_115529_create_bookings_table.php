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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();                                      // mã đơn BOOK-YYYYMMDD-XXXXX
            $table->foreignId('user_id')->constrained()->restrictOnDelete();       // khách hàng
            $table->foreignId('court_id')->constrained()->restrictOnDelete();      // sân con
            $table->date('booking_date');                                          // ngày đặt sân
            $table->time('start_time');                                            // giờ bắt đầu
            $table->time('end_time');                                              // giờ kết thúc
            $table->unsignedSmallInteger('duration');                              // thời lượng (phút)
            $table->decimal('price_snapshot', 10, 2);                             // đơn giá tại lúc đặt
            $table->decimal('total_amount', 10, 2);                               // tổng tiền
            $table->decimal('deposit_amount', 10, 2)->nullable();                 // tiền cọc
            $table->enum('status', ['pending', 'confirmed', 'cancelled', 'completed', 'no_show'])
                  ->default('pending');                                            // trạng thái đơn
            $table->enum('payment_method', ['at_venue', 'partial_online', 'full_online']); // phương thức TT
            $table->enum('payment_status', ['unpaid', 'deposit_paid', 'fully_paid', 'refunded'])
                  ->default('unpaid');                                             // trạng thái thanh toán
            $table->timestamp('cancelled_at')->nullable();                        // thời điểm hủy
            $table->text('cancel_reason')->nullable();                            // lý do hủy
            $table->text('notes')->nullable();                                    // ghi chú của khách
            $table->timestamps();
            $table->softDeletes();

            $table->index(['court_id', 'booking_date', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
