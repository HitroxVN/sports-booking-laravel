<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoyaltyTransaction extends Model
{
    protected $fillable = [
        'user_id',
        'booking_id',
        'reward_id',
        'type',
        'points',
        'balance_after',
        'description',
        'reference',
        'voucher_code',
        'expires_at',
        'used_at',
    ];

    protected function casts(): array
    {
        return [
            'points' => 'integer',
            'balance_after' => 'integer',
            'expires_at' => 'datetime',
            'used_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function reward(): BelongsTo
    {
        return $this->belongsTo(Reward::class);
    }

    public function scopeAvailableVouchers(Builder $query): Builder
    {
        return $query
            ->where('type', 'redeem')
            ->whereNotNull('voucher_code')
            ->whereNull('booking_id')
            ->whereNull('used_at')
            ->where(function (Builder $query) {
                $query->whereNull('expires_at')->orWhere('expires_at', '>', now());
            });
    }

    public function isAvailableVoucher(): bool
    {
        return $this->type === 'redeem'
            && $this->voucher_code !== null
            && $this->booking_id === null
            && $this->used_at === null
            && ($this->expires_at === null || $this->expires_at->isFuture());
    }
}
