<?php

namespace App\Http\Controllers\Admin;

use App\Events\MessageSent;
use App\Http\Controllers\Controller;
use App\Models\ChatConversation;
use App\Models\ChatMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    /**
     * Màn hình quản lý hội thoại Livechat
     */
    public function index(Request $request)
    {
        $query = ChatConversation::with(['latestMessage', 'user'])
            ->withCount(['messages as unread_customer_count' => function ($q) {
                $q->where('sender_type', 'customer')->where('is_read', false);
            }])
            ->latest('last_message_at');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_email', 'like', "%{$search}%")
                  ->orWhere('customer_phone', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $conversations = $query->paginate(20)->withQueryString();

        // Đếm tổng tin chưa đọc từ khách gửi tới admin
        $totalUnread = ChatMessage::where('sender_type', 'customer')
            ->where('is_read', false)
            ->count();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'conversations' => $conversations->items(),
                'total_unread'  => $totalUnread,
            ]);
        }

        return view('admin.chats.index', compact('conversations', 'totalUnread'));
    }

    /**
     * Lấy chi tiết tin nhắn của 1 hội thoại và đánh dấu đã đọc
     */
    public function show(ChatConversation $conversation): JsonResponse
    {
        // Đánh dấu toàn bộ tin nhắn của khách là admin đã đọc
        $conversation->messages()
            ->where('sender_type', 'customer')
            ->where('is_read', false)
            ->update(['is_read' => true]);

        $conversation->load(['user', 'admin']);

        $messages = $conversation->messages()
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(fn ($m) => [
                'id'              => $m->id,
                'conversation_id' => $m->conversation_id,
                'sender_type'     => $m->sender_type,
                'sender_name'     => $m->sender_name ?? ($m->sender_type === 'admin' ? 'Hỗ trợ viên' : 'Khách hàng'),
                'message'         => $m->message,
                'is_admin'        => $m->sender_type === 'admin',
                'created_at'      => $m->created_at->format('H:i d/m/Y'),
                'created_at_time' => $m->created_at->format('H:i'),
            ]);

        return response()->json([
            'id'              => $conversation->id,
            'customer_name'   => $conversation->customer_name,
            'customer_email'  => $conversation->customer_email,
            'customer_phone'  => $conversation->customer_phone,
            'status'          => $conversation->status,
            'is_registered'   => (bool) $conversation->user_id,
            'user'            => $conversation->user ? [
                'id'         => $conversation->user->id,
                'name'       => $conversation->user->name,
                'email'      => $conversation->user->email,
                'phone'      => $conversation->user->phone,
                'created_at' => $conversation->user->created_at->format('d/m/Y'),
            ] : null,
            'messages'        => $messages,
        ]);
    }

    /**
     * Admin gửi tin nhắn phản hồi tới khách
     */
    public function reply(ChatConversation $conversation, Request $request): JsonResponse
    {
        $request->validate([
            'message' => 'required|string|max:2000',
        ]);

        $admin = Auth::user();

        $message = ChatMessage::create([
            'conversation_id' => $conversation->id,
            'sender_type'     => 'admin',
            'sender_id'       => $admin?->id,
            'sender_name'     => $admin?->name ?? 'Hỗ trợ Arena Booking',
            'message'         => trim($request->input('message')),
            'is_read'         => false,
        ]);

        $conversation->update([
            'admin_id'        => $admin?->id,
            'last_message_at' => now(),
        ]);

        // Phát sóng realtime qua WebSockets tới khách và các admin khác (nếu Reverb đang chạy)
        try {
            broadcast(new MessageSent($message))->toOthers();
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('WebSocket broadcast deferred/failed: ' . $e->getMessage());
        }

        return response()->json([
            'id'              => $message->id,
            'conversation_id' => $message->conversation_id,
            'sender_type'     => $message->sender_type,
            'sender_name'     => $message->sender_name,
            'message'         => $message->message,
            'is_admin'        => true,
            'created_at'      => $message->created_at->format('H:i d/m/Y'),
            'created_at_time' => $message->created_at->format('H:i'),
        ]);
    }

    /**
     * Đổi trạng thái hội thoại (open <-> closed)
     */
    public function toggleStatus(ChatConversation $conversation): JsonResponse
    {
        $newStatus = $conversation->status === 'open' ? 'closed' : 'open';
        $conversation->update(['status' => $newStatus]);

        return response()->json([
            'success' => true,
            'status'  => $newStatus,
        ]);
    }
}
