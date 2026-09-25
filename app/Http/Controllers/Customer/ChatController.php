<?php

namespace App\Http\Controllers\Customer;

use App\Events\MessageSent;
use App\Http\Controllers\Controller;
use App\Models\ChatConversation;
use App\Models\ChatMessage;
use App\Models\Venue;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ChatController extends Controller
{
    /**
     * Hội thoại hỗ trợ (khách ↔ admin) của khách đang đăng nhập.
     * Chat chỉ dành cho khách đã đăng nhập — không còn luồng khách vãng lai.
     */
    public function initiate(): JsonResponse
    {
        $user = Auth::user();

        $conversation = ChatConversation::where('user_id', $user->id)
            ->where('type', 'support')
            ->where('status', 'open')
            ->latest()
            ->first();

        if (! $conversation) {
            $conversation = $this->createConversation([
                'user_id'        => $user->id,
                'type'           => 'support',
                'customer_name'  => $user->name,
                'customer_email' => $user->email,
                'customer_phone' => $user->phone,
            ]);

            $this->systemMessage(
                $conversation,
                'admin',
                'Hỗ trợ Arena Booking',
                'Xin chào! Cảm ơn bạn đã liên hệ Arena Sports Booking. Chúng tôi có thể hỗ trợ gì cho bạn hôm nay?'
            );
        }

        return response()->json($this->payload($conversation));
    }

    /**
     * Hội thoại với chủ sân của một khu sân (khách ↔ owner).
     */
    public function initiateVenue(Venue $venue): JsonResponse
    {
        $user = Auth::user();

        // Chủ sân không tự mở hội thoại với chính mình
        abort_if((int) $venue->owner_id === (int) $user->id, 403);

        $conversation = ChatConversation::where('user_id', $user->id)
            ->where('type', 'owner')
            ->where('venue_id', $venue->id)
            ->where('status', 'open')
            ->latest()
            ->first();

        if (! $conversation) {
            $conversation = $this->createConversation([
                'user_id'        => $user->id,
                'type'           => 'owner',
                'venue_id'       => $venue->id,
                'owner_id'       => $venue->owner_id,
                'customer_name'  => $user->name,
                'customer_email' => $user->email,
                'customer_phone' => $user->phone,
            ]);

            $this->systemMessage(
                $conversation,
                'owner',
                'Chủ sân ' . $venue->name,
                'Xin chào! Bạn có thể để lại câu hỏi cho chủ sân ' . $venue->name . ' tại đây.'
            );
        }

        return response()->json($this->payload($conversation));
    }

    /**
     * Lấy danh sách tin nhắn của hội thoại (chỉ chủ hội thoại).
     */
    public function getMessages(ChatConversation $conversation): JsonResponse
    {
        abort_unless($conversation->user_id === Auth::id(), 403);

        // Khách mở hội thoại = đã đọc hết tin của admin/chủ sân
        $conversation->messages()
            ->whereIn('sender_type', ['admin', 'owner'])
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json($this->payload($conversation));
    }

    /**
     * Khách gửi tin nhắn.
     */
    public function sendMessage(Request $request, ChatConversation $conversation): JsonResponse
    {
        abort_unless($conversation->user_id === Auth::id(), 403);

        $validated = $request->validate([
            'message' => 'required|string|max:2000',
        ]);

        $message = ChatMessage::create([
            'conversation_id' => $conversation->id,
            'sender_type'     => 'customer',
            'sender_id'       => Auth::id(),
            'sender_name'     => Auth::user()->name,
            'message'         => $validated['message'],
        ]);

        $conversation->update(['last_message_at' => now()]);

        try {
            broadcast(new MessageSent($message))->toOthers();
        } catch (\Throwable $e) {
            // Reverb chưa chạy cũng không được làm hỏng việc gửi tin
            Log::warning('WebSocket broadcast deferred/failed: ' . $e->getMessage());
        }

        return response()->json($this->messagePayload($message, true));
    }

    private function createConversation(array $attributes): ChatConversation
    {
        return ChatConversation::create($attributes + [
            'status'          => 'open',
            'last_message_at' => now(),
        ]);
    }

    // Tin nhắn chào tự động của hệ thống (không gắn user nào)
    private function systemMessage(ChatConversation $conversation, string $senderType, string $senderName, string $text): void
    {
        ChatMessage::create([
            'conversation_id' => $conversation->id,
            'sender_type'     => $senderType,
            'sender_id'       => null,
            'sender_name'     => $senderName,
            'message'         => $text,
            'is_read'         => false,
        ]);
    }

    private function payload(ChatConversation $conversation): array
    {
        return [
            'conversation_id' => $conversation->id,
            'type'            => $conversation->type,
            'customer_name'   => $conversation->customer_name,
            'status'          => $conversation->status,
            'messages'        => $conversation->messages()
                ->orderBy('created_at')
                ->get()
                ->map(fn (ChatMessage $m) => $this->messagePayload($m, $m->sender_type === 'customer'))
                ->values(),
        ];
    }

    private function messagePayload(ChatMessage $message, bool $isMe): array
    {
        return [
            'id'              => $message->id,
            'conversation_id' => $message->conversation_id,
            'sender_type'     => $message->sender_type,
            'sender_name'     => $message->sender_name ?? $this->defaultSenderName($message->sender_type),
            'message'         => $message->message,
            'is_me'           => $isMe,
            'created_at'      => $message->created_at->format('H:i d/m/Y'),
            'created_at_time' => $message->created_at->format('H:i'),
        ];
    }

    private function defaultSenderName(string $senderType): string
    {
        return match ($senderType) {
            'admin' => 'Hỗ trợ viên',
            'owner' => 'Chủ sân',
            default => 'Bạn',
        };
    }
}
