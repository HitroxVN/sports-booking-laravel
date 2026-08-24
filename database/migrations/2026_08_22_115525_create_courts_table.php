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
        Schema::create('courts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('venue_id')->constrained()->cascadeOnDelete();  // khu sân
            $table->foreignId('sport_id')->constrained()->restrictOnDelete(); // môn thể thao
            $table->string('name');                                            // tên sân (Sân A, Sân 1...)
            $table->text('description')->nullable();                           // mô tả
            $table->enum('surface_type', [
                'natural_grass',    // cỏ tự nhiên
                'artificial_turf',  // cỏ nhân tạo
                'wood',             // sàn gỗ
                'concrete',         // bê tông
            ])->nullable();
            $table->unsignedTinyInteger('max_players')->nullable();            // số người tối đa
            $table->enum('status', ['active', 'maintenance', 'closed'])
                  ->default('active');                                         // trạng thái sân
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courts');
    }
};
