<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    protected $fillable = [
        'venue_id', 'code', 'description',
        'discount_type', 'discount_value', 'min_amount',
        'max_uses', 'used_count',
        'starts_at', 'expires_at', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'discount_value' => 'decimal:2',
            'min_amount'     => 'decimal:2',
            'starts_at'      => 'datetime',
            'expires_at'     => 'datetime',
            'is_active'      => 'boolean',
        ];
    }

    // Thuộc về 1 khu sân
    public function venue()
    {
        return $this->belongsTo(Venue::class);
    }

    // Kiểm tra mã còn hiệu lực không
    public function isValid(): bool
    {
        return $this->is_active
            && now()->between($this->starts_at, $this->expires_at)
            && ($this->max_uses === null || $this->used_count < $this->max_uses);
    }
}
