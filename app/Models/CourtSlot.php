<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourtSlot extends Model
{
    protected $fillable = [
        'court_id', 'day_of_week', 'start_time', 'end_time',
        'price', 'peak_price', 'is_peak',
    ];

    protected function casts(): array
    {
        return [
            'price'      => 'decimal:2',
            'peak_price' => 'decimal:2',
            'is_peak'    => 'boolean',
        ];
    }

    // Thuộc về 1 sân con
    public function court()
    {
        return $this->belongsTo(Court::class);
    }

    // Trả giá áp dụng (giờ vàng hoặc giá thường)
    public function getEffectivePriceAttribute(): float
    {
        return $this->is_peak && $this->peak_price
            ? (float) $this->peak_price
            : (float) $this->price;
    }
}
