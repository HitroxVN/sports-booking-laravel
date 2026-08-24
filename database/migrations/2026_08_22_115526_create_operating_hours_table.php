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
        Schema::create('operating_hours', function (Blueprint $table) {
            $table->id();
            $table->foreignId('venue_id')->constrained()->cascadeOnDelete(); // khu sân
            $table->unsignedTinyInteger('day_of_week');   // 0=Chủ nhật ... 6=Thứ 7
            $table->time('open_time');                    // giờ mở cửa
            $table->time('close_time');                   // giờ đóng cửa
            $table->boolean('is_closed')->default(false); // ngày nghỉ (không mở)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('operating_hours');
    }
};
