<?php

namespace App\Http\Controllers\Owner;

use App\Events\MessageSent;
use App\Http\Controllers\Controller;
use App\Models\ChatConversation;
use App\Models\ChatMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ChatController extends Controller
{
    /**
     * Danh sách hội thoại khách gửi tới các khu sân của chủ sân đang đăng nhập.
     */
    public function index(Request $request)
    {
        $query = ChatConversation::where('type', 'owner')
            ->where('owner_id', Auth::id())
            ->with(['latestMessage', 'user', 'venue'])
            ->withCount(['messages as unread_customer_count' => function ($q) {
                $q->where('sender_type', 'customer')->where('is_read', false);
            }])
            ->latest('last_message_at');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $conversations = $query->paginate(20)->withQueryString();

        return view('owner.chats.index', compact('conversations'));
    }

    /**
     * Lấy tin nhắn của 1 hội thoại và đánh dấu khách đã được đọc.
     */
    public function show(ChatConversation $conversation): JsonResponse
    {
        $this->authorizeConversation($conversation);

        $conversation->messages()
            ->where('sender_type', 'customer')
            ->where('is_read', false)
            ->update(['is_read' => true]);

        $conversation->load(['user', 'venue']);

        return response()->json([
            'id'             => $conversation->id,
            'customer_name'  => $conversation->customer_name,
            'customer_email' => $conversation->customer_email,
            'customer_phone' => $conversation->customer_phone,
            'venue_name'     => $conversation->venue?->name,
            'status'         => $conversation->status,
            'messages'       => $conversation->messages()
                ->orderBy('created_at')
                ->get()
                ->map(fn (ChatMessage $m) => $this->messagePayload($m))
                ->values(),
        ]);
    }

    /**
     * Chủ sân trả lời khách.
     */
    public function reply(Request $request, ChatConversation $conversation): JsonResponse
    {
        $this->authorizeConversation($conversation);

        $validated = $request->validate([
            'message' => 'required|string|max:2000',
        ]);

        $owner = Auth::user();

        $message = ChatMessage::create([
            'conversation_id' => $conversation->id,
            'sender_type'     => 'owner',
            'sender_id'       => $owner->id,
            'sender_name'     => $owner->name,
            'message'         => trim($validated['message']),
            'is_read'         => false,
        ]);

        $conversation->update(['last_message_at' => now()]);

        try {
            broadcast(new MessageSent($message))->toOthers();
        } catch (\Throwable $e) {
            Log::warning('WebSocket broadcast deferred/failed: ' . $e->getMessage());
        }

        return response()->json($this->messagePayload($message));
    }

    /**
     * Đóng / mở lại hội thoại.
     */
    public function toggleStatus(ChatConversation $conversation): JsonResponse
    {
        $this->authorizeConversation($conversation);

        $newStatus = $conversation->status === 'open' ? 'closed' : 'open';
        $conversation->update(['status' => $newStatus]);

        return response()->json([
            'success' => true,
            'status'  => $newStatus,
        ]);
    }

    // Chủ sân chỉ được thao tác trên hội thoại của khu sân mình
    private function authorizeConversation(ChatConversation $conversation): void
    {
        abort_unless(
            $conversation->type === 'owner' && (int) $conversation->owner_id === (int) Auth::id(),
            404
        );
    }

    private function messagePayload(ChatMessage $message): array
    {
        return [
            'id'              => $message->id,
            'conversation_id' => $message->conversation_id,
            'sender_type'     => $message->sender_type,
            'sender_name'     => $message->sender_name
                ?? ($message->sender_type === 'customer' ? 'Khách hàng' : 'Bạn'),
            'message'         => $message->message,
            'is_owner'        => $message->sender_type === 'owner',
            'created_at'      => $message->created_at->format('H:i d/m/Y'),
            'created_at_time' => $message->created_at->format('H:i'),
        ];
    }
}
