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
        Schema::create('court_slots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('court_id')->constrained()->cascadeOnDelete(); // sân con
            $table->unsignedTinyInteger('day_of_week')->nullable(); // null = áp dụng mọi ngày
            $table->time('start_time');                             // giờ bắt đầu khung
            $table->time('end_time');                               // giờ kết thúc khung
            $table->decimal('price', 10, 2);                        // giá mỗi khung giờ
            $table->decimal('peak_price', 10, 2)->nullable();       // giá giờ vàng
            $table->boolean('is_peak')->default(false);             // có phải giờ vàng không
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('court_slots');
    }
};
