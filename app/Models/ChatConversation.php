<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatConversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'venue_id',
        'owner_id',
        'customer_name',
        'customer_phone',
        'customer_email',
        'admin_id',
        'status',
        'last_message_at',
    ];

    protected function casts(): array
    {
        return [
            'last_message_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    // Khu sân của hội thoại loại 'owner'
    public function venue()
    {
        return $this->belongsTo(Venue::class);
    }

    // Chủ sân nhận hội thoại loại 'owner'
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function messages()
    {
        return $this->hasMany(ChatMessage::class, 'conversation_id');
    }

    public function latestMessage()
    {
        return $this->hasOne(ChatMessage::class, 'conversation_id')->latestOfMany();
    }

    public function unreadCountForAdmin(): int
    {
        return $this->messages()
            ->where('sender_type', 'customer')
            ->where('is_read', false)
            ->count();
    }

    public function unreadCountForCustomer(): int
    {
        return $this->messages()
            ->whereIn('sender_type', ['admin', 'owner'])
            ->where('is_read', false)
            ->count();
    }
}
