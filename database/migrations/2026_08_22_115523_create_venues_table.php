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
        Schema::create('venues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->constrained('users')->cascadeOnDelete(); // chủ sân
            $table->string('name');                                // tên khu sân
            $table->string('slug')->unique();                      // URL slug
            $table->text('description')->nullable();               // mô tả
            $table->string('address');                             // số nhà, tên đường
            $table->string('ward')->nullable();                    // phường/xã
            $table->string('district')->nullable();                // quận/huyện
            $table->string('city');                                // thành phố
            $table->decimal('latitude', 10, 7)->nullable();        // vĩ độ
            $table->decimal('longitude', 10, 7)->nullable();       // kinh độ
            $table->string('phone', 20)->nullable();               // số điện thoại liên hệ
            $table->string('email')->nullable();                   // email liên hệ
            $table->enum('status', ['pending', 'active', 'rejected', 'closed'])
                  ->default('pending');                            // trạng thái duyệt
            $table->string('cover_image')->nullable();             // ảnh bìa
            $table->json('amenities')->nullable();                 // tiện ích: wifi, parking...
            $table->decimal('rating_avg', 3, 2)->default(0);       // điểm đánh giá trung bình
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('venues');
    }
};
