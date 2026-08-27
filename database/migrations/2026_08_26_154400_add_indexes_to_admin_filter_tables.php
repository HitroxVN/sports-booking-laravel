<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations — index cho các query filter của Admin (users/venues/bookings).
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->index(['role', 'status']);
        });

        Schema::table('venues', function (Blueprint $table) {
            $table->index('owner_id');
            $table->index('status');
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->index('booking_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['role', 'status']);
        });

        Schema::table('venues', function (Blueprint $table) {
            $table->dropIndex(['owner_id']);
            $table->dropIndex(['status']);
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->dropIndex(['booking_date']);
        });
    }
};
