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
      Schema::create('court_holds', function (Blueprint $table) {
    $table->id();
    $table->foreignId('court_id')->constrained()->cascadeOnDelete();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->date('booking_date');
    $table->time('start_time');
    $table->time('end_time');
    $table->timestamp('expires_at'); // Thời điểm hết hạn giữ chỗ (10 phút sau)
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('court_holds');
    }
};
