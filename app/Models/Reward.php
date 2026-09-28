<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Reward extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'points_required',
        'discount_type',
        'discount_value',
        'min_amount',
        'max_discount',
        'valid_days',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'points_required' => 'integer',
            'discount_value' => 'decimal:2',
            'min_amount' => 'decimal:2',
            'max_discount' => 'decimal:2',
            'valid_days' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(LoyaltyTransaction::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function discountFor(float $amount): float
    {
        $discount = $this->discount_type === 'percent'
            ? round($amount * (float) $this->discount_value / 100)
            : (float) $this->discount_value;

        if ($this->max_discount !== null) {
            $discount = min($discount, (float) $this->max_discount);
        }

        return min($discount, $amount);
    }
}
