<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Court extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'venue_id', 'sport_id', 'name', 'description',
        'surface_type', 'max_players', 'status',
    ];

    // --- Relationships ---

    // Sân con thuộc về 1 khu sân
    public function venue()
    {
        return $this->belongsTo(Venue::class);
    }

    // Sân con thuộc về 1 môn thể thao
    public function sport()
    {
        return $this->belongsTo(Sport::class);
    }

    // Khung giờ + giá của sân này
    public function slots()
    {
        return $this->hasMany(CourtSlot::class);
    }

    // Lịch khóa đột xuất
    public function closures()
    {
        return $this->hasMany(CourtClosure::class);
    }

    // Đơn đặt sân của sân này
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}
