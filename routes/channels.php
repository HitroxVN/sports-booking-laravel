<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('chat.conversation.{id}', function ($user = null) {
    return true; // Cho phép cả khách vãng lai và user tham gia channel trao đổi của mình
});

Broadcast::channel('chat.admin', function ($user = null) {
    return $user && in_array($user->role, ['admin', 'owner']);
});
