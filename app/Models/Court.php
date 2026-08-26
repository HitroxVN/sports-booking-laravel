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
        'surface_type', 'max_players', 'status', 'image',
    ];

    // --- Accessors (Tự động dịch dữ liệu sang Tiếng Việt) ---

    /**
     * Dịch mặt sân.
     * Cách gọi ở View: {{ $court->surface_type_name }}
     */
    public function getSurfaceTypeNameAttribute()
    {
        return match($this->surface_type) {
            'artificial_turf' => 'Cỏ nhân tạo',
            'natural_grass'   => 'Cỏ tự nhiên',
            'wood'            => 'Sàn gỗ',
            'concrete'        => 'Bê tông',
            default           => 'Chưa xác định',
        };
    }

    /**
     * Dịch trạng thái.
     * Cách gọi ở View: {{ $court->status_name }}
     */
    public function getStatusNameAttribute()
    {
        return match($this->status) {
            'active'      => 'Hoạt động',
            'maintenance' => 'Bảo trì',
            'closed'      => 'Đóng cửa',
            default       => 'Không xác định',
        };
    }

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
       return $this->hasMany(\App\Models\CourtSlot::class);
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