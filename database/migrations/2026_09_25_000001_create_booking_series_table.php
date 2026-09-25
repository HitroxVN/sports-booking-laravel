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
        // Lịch cố định: 1 chuỗi gồm nhiều buổi giống nhau lặp lại hàng tuần
        Schema::create('booking_series', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();   // khách đặt lịch cố định
            $table->foreignId('court_id')->constrained()->cascadeOnDelete();  // sân con
            $table->unsignedTinyInteger('weekday');                           // thứ trong tuần (0 = CN ... 6 = Thứ 7)
            $table->time('start_time');                                       // giờ bắt đầu mỗi buổi
            $table->time('end_time');                                         // giờ kết thúc mỗi buổi
            $table->unsignedSmallInteger('duration');                         // thời lượng mỗi buổi (phút)
            $table->decimal('price_snapshot', 10, 2);                         // đơn giá 1 giờ tại lúc đặt
            $table->unsignedTinyInteger('weeks');                             // số tuần lặp
            $table->date('starts_on');                                        // ngày buổi đầu
            $table->enum('status', ['active', 'cancelled'])->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
        });

        // Mỗi buổi của chuỗi là 1 booking bình thường, gắn về chuỗi để nhóm/hủy cả chuỗi
        Schema::table('bookings', function (Blueprint $table) {
            $table->foreignId('series_id')->nullable()->after('court_id')
                ->constrained('booking_series')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropConstrainedForeignId('series_id');
        });

        Schema::dropIfExists('booking_series');
    }
};
