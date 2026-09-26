<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Kênh riêng từng hội thoại: chỉ người liên quan mới vào được.
// Chat chỉ dành cho người đã đăng nhập (không còn khách vãng lai).
Broadcast::channel('chat.conversation.{id}', function ($user, $id) {
    $conversation = \App\Models\ChatConversation::find($id);
    if (! $conversation || ! $user) {
        return false;
    }

    // Admin vào mọi hội thoại hỗ trợ (quầy livechat)
    if ($user->role === 'admin' && $conversation->type === 'support') {
        return true;
    }

    // Chủ sân vào hội thoại của khu sân mình
    if ($conversation->type === 'owner' && (int) $conversation->owner_id === (int) $user->id) {
        return true;
    }

    // Khách chỉ vào hội thoại của chính mình
    return (int) $conversation->user_id === (int) $user->id;
});

// Quầy livechat của admin: chỉ admin.
Broadcast::channel('chat.admin', function ($user) {
    return $user && $user->role === 'admin';
});

// Kênh riêng của từng chủ sân: nhận thông báo có tin nhắn mới từ khách.
Broadcast::channel('chat.owner.{ownerId}', function ($user, $ownerId) {
    return $user
        && $user->role === 'owner'
        && (int) $user->id === (int) $ownerId;
});
