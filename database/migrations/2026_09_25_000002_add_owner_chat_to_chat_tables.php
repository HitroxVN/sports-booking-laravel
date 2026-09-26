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
        // Hội thoại có 2 loại: 'support' (khách ↔ admin) và 'owner' (khách ↔ chủ sân).
        // venue_id/owner_id chỉ có giá trị với loại 'owner'.
        Schema::table('chat_conversations', function (Blueprint $table) {
            $table->enum('type', ['support', 'owner'])->default('support')->after('id')->index();
            $table->foreignId('venue_id')->nullable()->after('type')->constrained()->nullOnDelete();
            $table->foreignId('owner_id')->nullable()->after('venue_id')->constrained('users')->nullOnDelete();
        });

        // Chat giờ chỉ dành cho khách đã đăng nhập → session_token không còn dùng.
        // Để nullable thay vì xóa cột nhằm giữ lại dữ liệu hội thoại cũ.
        Schema::table('chat_conversations', function (Blueprint $table) {
            $table->string('session_token', 80)->nullable()->change();
        });

        // Bổ sung 'owner' cho phía gửi (trước chỉ có customer/admin)
        Schema::table('chat_messages', function (Blueprint $table) {
            $table->enum('sender_type', ['customer', 'admin', 'owner'])->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chat_messages', function (Blueprint $table) {
            $table->enum('sender_type', ['customer', 'admin'])->change();
        });

        Schema::table('chat_conversations', function (Blueprint $table) {
            $table->string('session_token', 80)->nullable(false)->change();

            $table->dropConstrainedForeignId('venue_id');
            $table->dropConstrainedForeignId('owner_id');
            $table->dropColumn('type');
        });
    }
};
