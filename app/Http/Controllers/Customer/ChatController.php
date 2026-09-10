<?php

namespace App\Http\Controllers\Customer;

use App\Events\MessageSent;
use App\Http\Controllers\Controller;
use App\Models\ChatConversation;
use App\Models\ChatMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ChatController extends Controller
{
    /**
     * Khởi tạo hoặc khôi phục hội thoại chat của khách hàng
     */
    public function initiate(Request $request): JsonResponse
    {
        $sessionToken = $request->input('session_token') ?: (string) Str::uuid();
        $user = Auth::user();

        $conversation = null;

        // Nếu đã đăng nhập, tìm cuộc trò chuyện mở của user trước
        if ($user) {
            $conversation = ChatConversation::where('user_id', $user->id)
                ->where('status', 'open')
                ->latest()
                ->first();
        }

        // Nếu chưa có, tìm theo session_token
        if (!$conversation) {
            $conversation = ChatConversation::where('session_token', $sessionToken)
                ->where('status', 'open')
                ->latest()
                ->first();
        }

        // Nếu vẫn chưa có, tạo mới
        if (!$conversation) {
            $customerName = $user ? $user->name : ($request->input('customer_name') ?: 'Khách hàng');
            $customerEmail = $user ? $user->email : $request->input('customer_email');
            $customerPhone = $user ? $user->phone : $request->input('customer_phone');

            $conversation = ChatConversation::create([
                'user_id'         => $user?->id,
                'session_token'   => $sessionToken,
                'customer_name'   => $customerName,
                'customer_email'  => $customerEmail,
                'customer_phone'  => $customerPhone,
                'status'          => 'open',
                'last_message_at' => now(),
            ]);

            // Tin nhắn chào mừng tự động từ hệ thống
            ChatMessage::create([
                'conversation_id' => $conversation->id,
                'sender_type'     => 'admin',
                'sender_id'       => null,
                'sender_name'     => 'Hỗ trợ Arena Booking',
                'message'         => 'Xin chào! Cảm ơn bạn đã liên hệ Arena Sports Booking. Chúng tôi có thể hỗ trợ gì cho bạn hôm nay?',
                'is_read'         => false,
            ]);
        } elseif ($user && !$conversation->user_id) {
            // Liên kết user vào conversation nếu lúc trước là khách vãng lai
            $conversation->update([
                'user_id'       => $user->id,
                'customer_name' => $user->name,
                'customer_email'=> $user->email ?? $conversation->customer_email,
            ]);
        }

        $messages = $conversation->messages()
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(fn ($m) => [
                'id'              => $m->id,
                'conversation_id' => $m->conversation_id,
                'sender_type'     => $m->sender_type,
                'sender_name'     => $m->sender_name ?? ($m->sender_type === 'admin' ? 'Hỗ trợ viên' : 'Bạn'),
                'message'         => $m->message,
                'is_me'           => $m->sender_type === 'customer',
                'created_at'      => $m->created_at->format('H:i d/m/Y'),
                'created_at_time' => $m->created_at->format('H:i'),
            ]);

        return response()->json([
            'session_token'   => $conversation->session_token,
            'conversation_id' => $conversation->id,
            'customer_name'   => $conversation->customer_name,
            'status'          => $conversation->status,
            'messages'        => $messages,
        ]);
    }

    /**
     * Lấy danh sách tin nhắn của hội thoại
     */
    public function getMessages(ChatConversation $conversation, Request $request): JsonResponse
    {
        $sessionToken = $request->header('X-Chat-Session') ?: $request->input('session_token');
        $user = Auth::user();

        // Kiểm tra quyền truy cập (user_id hoặc session_token khớp)
        if ($conversation->user_id && $user && $conversation->user_id !== $user->id && !in_array($user->role, ['admin', 'owner'])) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if (!$user && $conversation->session_token !== $sessionToken) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Đánh dấu tin nhắn của admin đã được khách đọc
        $conversation->messages()
            ->where('sender_type', 'admin')
            ->where('is_read', false)
            ->update(['is_read' => true]);

        $messages = $conversation->messages()
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(fn ($m) => [
                'id'              => $m->id,
                'conversation_id' => $m->conversation_id,
                'sender_type'     => $m->sender_type,
                'sender_name'     => $m->sender_name ?? ($m->sender_type === 'admin' ? 'Hỗ trợ viên' : 'Bạn'),
                'message'         => $m->message,
                'is_me'           => $m->sender_type === 'customer',
                'created_at'      => $m->created_at->format('H:i d/m/Y'),
                'created_at_time' => $m->created_at->format('H:i'),
            ]);

        return response()->json([
            'conversation_id' => $conversation->id,
            'status'          => $conversation->status,
            'messages'        => $messages,
        ]);
    }

    /**
     * Khách hàng gửi tin nhắn
     */
    public function sendMessage(ChatConversation $conversation, Request $request): JsonResponse
    {
        $request->validate([
            'message' => 'required|string|max:2000',
        ]);

        $sessionToken = $request->header('X-Chat-Session') ?: $request->input('session_token');
        $user = Auth::user();

        if ($conversation->user_id && $user && $conversation->user_id !== $user->id && !in_array($user->role, ['admin', 'owner'])) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if (!$user && $conversation->session_token !== $sessionToken) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $senderName = $user ? $user->name : ($conversation->customer_name ?: 'Khách hàng');

        $message = ChatMessage::create([
            'conversation_id' => $conversation->id,
            'sender_type'     => 'customer',
            'sender_id'       => $user?->id,
            'sender_name'     => $senderName,
            'message'         => trim($request->input('message')),
            'is_read'         => false,
        ]);

        $conversation->update([
            'last_message_at' => now(),
            'status'          => 'open',
        ]);

        // Broadcast realtime qua WebSockets (nếu Reverb đang chạy)
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
            'is_me'           => true,
            'created_at'      => $message->created_at->format('H:i d/m/Y'),
            'created_at_time' => $message->created_at->format('H:i'),
        ]);
    }
}
