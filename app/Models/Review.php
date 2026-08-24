<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Review extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'booking_id', 'user_id', 'venue_id',
        'rating', 'comment', 'images',
        'owner_reply', 'is_visible',
    ];

    protected function casts(): array
    {
        return [
            'images'     => 'array',
            'is_visible' => 'boolean',
        ];
    }

    // --- Relationships ---

    // Đánh giá thuộc về 1 đơn
    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    // Đánh giá thuộc về 1 người dùng
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Đánh giá thuộc về 1 khu sân
    public function venue()
    {
        return $this->belongsTo(Venue::class);
    }
}
