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
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->unique()->constrained()->cascadeOnDelete(); // 1 booking = 1 review
            $table->foreignId('user_id')->constrained()->restrictOnDelete();             // người đánh giá
            $table->foreignId('venue_id')->constrained()->cascadeOnDelete();             // khu sân
            $table->unsignedTinyInteger('rating');               // số sao (1-5)
            $table->text('comment')->nullable();                 // bình luận
            $table->json('images')->nullable();                  // ảnh đính kèm
            $table->text('owner_reply')->nullable();             // phản hồi chủ sân (1 lần)
            $table->boolean('is_visible')->default(true);        // hiển thị hay ẩn
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
