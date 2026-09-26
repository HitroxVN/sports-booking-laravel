<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingSeries extends Model
{
    use HasFactory;

    protected $table = 'booking_series';

    protected $fillable = [
        'user_id', 'court_id', 'weekday', 'start_time', 'end_time',
        'duration', 'price_snapshot', 'weeks', 'starts_on', 'status', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'starts_on'      => 'date',
            'price_snapshot' => 'decimal:2',
        ];
    }

    // --- Relationships ---

    // Chuỗi thuộc về 1 khách hàng
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Chuỗi áp dụng cho 1 sân con
    public function court()
    {
        return $this->belongsTo(Court::class);
    }

    // Chuỗi gồm nhiều buổi (mỗi buổi là 1 booking)
    public function bookings()
    {
        return $this->hasMany(Booking::class, 'series_id');
    }
}
