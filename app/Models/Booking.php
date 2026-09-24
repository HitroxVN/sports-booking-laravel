<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Booking extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code', 'user_id', 'court_id', 'booking_date',
        'start_time', 'end_time', 'duration',
        'price_snapshot', 'total_amount', 'deposit_amount',
        'promotion_id', 'discount_amount',
        'status', 'payment_method', 'payment_status',
        'cancelled_at', 'cancel_reason', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'booking_date'   => 'date',
            'cancelled_at'   => 'datetime',
            'price_snapshot' => 'decimal:2',
            'total_amount'   => 'decimal:2',
            'deposit_amount' => 'decimal:2',
        ];
    }

    // --- Relationships ---

    // Đơn thuộc về 1 khách hàng
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Đơn thuộc về 1 sân con
    public function court()
    {
        return $this->belongsTo(Court::class);
    }

    // Đơn có nhiều giao dịch thanh toán
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    // Đơn có 1 đánh giá
    public function review()
    {
        return $this->hasOne(Review::class);
    }

    // Mã giảm giá áp dụng cho đơn (nếu có)
    public function promotion()
    {
        return $this->belongsTo(Promotion::class);
    }

    // --- Helpers ---
    // Số phút giữ đơn pending trước khi tự hủy
    public const PAYMENT_EXPIRY_MINUTES = 10;

    public function isPending(): bool    { return $this->status === 'pending'; }
    public function isConfirmed(): bool  { return $this->status === 'confirmed'; }
    public function isCancelled(): bool  { return $this->status === 'cancelled'; }
    public function isCompleted(): bool  { return $this->status === 'completed'; }
    public function isPaid(): bool       { return $this->payment_status === 'fully_paid'; }
    public function hasDeposit(): bool   { return $this->payment_status === 'deposit_paid'; }

    /**
     * Hạn chót thanh toán: đơn pending quá PAYMENT_EXPIRY_MINUTES phút bị hủy tự động
     * (khớp lệnh app:cancel-expired-pending-bookings).
     */
    public function paymentExpiresAt(): \Carbon\CarbonInterface
    {
        return $this->created_at->copy()->addMinutes(self::PAYMENT_EXPIRY_MINUTES);
    }

    public function isPaymentExpired(): bool
    {
        return $this->isPending() && $this->paymentExpiresAt()->isPast();
    }
}
