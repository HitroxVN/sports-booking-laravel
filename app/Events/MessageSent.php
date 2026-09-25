<?php

namespace App\Events;

use App\Models\ChatMessage;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public ChatMessage $message;

    public function __construct(ChatMessage $message)
    {
        $this->message = $message;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        $channels = [
            new PrivateChannel('chat.conversation.' . $this->message->conversation_id),
        ];

        $conversation = $this->message->conversation;

        if ($conversation?->type === 'owner' && $conversation->owner_id) {
            // Hội thoại với chủ sân: chỉ báo cho đúng chủ sân đó
            $channels[] = new PrivateChannel('chat.owner.' . $conversation->owner_id);
        } else {
            // Hội thoại hỗ trợ: báo cho quầy livechat của admin
            $channels[] = new PrivateChannel('chat.admin');
        }

        return $channels;
    }

    public function broadcastAs(): string
    {
        return 'message.sent';
    }

    public function broadcastWith(): array
    {
        return [
            'id'              => $this->message->id,
            'conversation_id' => $this->message->conversation_id,
            'sender_type'     => $this->message->sender_type,
            'sender_id'       => $this->message->sender_id,
            'sender_name'     => $this->message->sender_name ?? match ($this->message->sender_type) {
                'admin' => 'Hỗ trợ viên',
                'owner' => 'Chủ sân',
                default => 'Khách hàng',
            },
            'message'         => $this->message->message,
            'created_at'      => $this->message->created_at->format('H:i d/m/Y'),
            'created_at_time' => $this->message->created_at->format('H:i'),
        ];
    }
}
