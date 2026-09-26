<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Danh sách đầy đủ. Khách dùng layout ngoài (layouts.customer),
     * chủ sân và admin dùng layout panel tương ứng.
     */
    public function index(Request $request)
    {
        $notifications = $request->user()->notifications()->paginate(20);
        $unreadCount   = $request->user()->unreadNotifications()->count();

        return match ($request->user()->role) {
            'owner' => view('notifications.panel', ['layout' => 'owner-layout', 'notifications' => $notifications, 'unreadCount' => $unreadCount]),
            'admin' => view('notifications.panel', ['layout' => 'admin-layout', 'notifications' => $notifications, 'unreadCount' => $unreadCount]),
            default => view('notifications.index', compact('notifications', 'unreadCount')),
        };
    }

    /**
     * Đánh dấu 1 thông báo đã đọc.
     * Truy vấn qua quan hệ của chính người đang đăng nhập nên không đọc được của người khác.
     */
    public function markRead(Request $request, string $notification)
    {
        $request->user()->notifications()->findOrFail($notification)->markAsRead();

        return $request->expectsJson()
            ? response()->json(['ok' => true])
            : back();
    }

    public function markAllRead(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();

        return $request->expectsJson()
            ? response()->json(['ok' => true])
            : back()->with('success', 'Đã đánh dấu tất cả thông báo là đã đọc.');
    }
}
