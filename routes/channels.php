<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Kênh riêng từng hội thoại: chỉ chủ hội thoại (user_id khớp) hoặc
// khách vãng lai giữ đúng session_token của hội thoại được vào.
// Guest không đăng nhập => $user null, token lấy từ query string do client gửi kèm.
Broadcast::channel('chat.conversation.{id}', function ($user = null, $id = null, ?string $sessionToken = null) {
    $conversation = \App\Models\ChatConversation::find($id);
    if (! $conversation) {
        return false;
    }

    if ($user) {
        // Admin/owner được vào mọi hội thoại để hỗ trợ
        if (in_array($user->role, ['admin', 'owner'])) {
            return true;
        }

        return $conversation->user_id === $user->id;
    }

    // Khách vãng lai: phải khớp session_token của hội thoại
    return $sessionToken !== null
        && hash_equals($conversation->session_token, $sessionToken);
});

// Kênh tổng hợp cho phía admin: chỉ admin/owner.
Broadcast::channel('chat.admin', function ($user = null) {
    return $user && in_array($user->role, ['admin', 'owner']);
});
