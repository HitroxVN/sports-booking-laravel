<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'booking_id', 'gateway', 'gateway_txn_id',
        'amount', 'type', 'status',
        'gateway_response', 'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'amount'           => 'decimal:2',
            'gateway_response' => 'array',
            'paid_at'          => 'datetime',
        ];
    }

    // Thanh toán thuộc về 1 đơn
    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}
