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
        Schema::create('court_closures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('court_id')->constrained()->cascadeOnDelete(); // sân bị khóa
            $table->date('date');                                  // ngày bị khóa
            $table->time('start_time')->nullable();                // null = khóa cả ngày
            $table->time('end_time')->nullable();                  // null = khóa cả ngày
            $table->string('reason')->nullable();                  // lý do (bảo trì, giải đấu...)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('court_closures');
    }
};
