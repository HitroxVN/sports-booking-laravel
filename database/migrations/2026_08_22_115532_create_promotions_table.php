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
        Schema::create('promotions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('venue_id')->constrained()->cascadeOnDelete(); // khu sân
            $table->string('code')->unique();                                 // mã code (SUMMER20)
            $table->text('description')->nullable();                          // mô tả
            $table->enum('discount_type', ['percent', 'fixed']);              // % hoặc giảm cố định
            $table->decimal('discount_value', 10, 2);                         // giá trị giảm
            $table->decimal('min_amount', 10, 2)->nullable();                 // đơn tối thiểu để áp dụng
            $table->unsignedInteger('max_uses')->nullable();                  // số lần dùng tối đa
            $table->unsignedInteger('used_count')->default(0);                // số lần đã dùng
            $table->timestamp('starts_at');                                   // ngày bắt đầu
            $table->timestamp('expires_at');                                  // ngày hết hạn
            $table->boolean('is_active')->default(true);                      // đang bật
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promotions');
    }
};
