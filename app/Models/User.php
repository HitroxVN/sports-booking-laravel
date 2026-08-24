<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'phone', 'avatar', 'role', 'status'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    // --- Helpers ---
    public function isAdmin(): bool   { return $this->role === 'admin'; }
    public function isOwner(): bool   { return $this->role === 'owner'; }
    public function isCustomer(): bool { return $this->role === 'customer'; }
    public function isActive(): bool  { return $this->status === 'active'; }

    // --- Relationships ---

    // 1 chủ sân có nhiều khu sân
    public function venues()
    {
        return $this->hasMany(Venue::class, 'owner_id');
    }

    // 1 khách hàng có nhiều đơn đặt sân
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    // 1 người dùng có nhiều đánh giá
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    // Sân yêu thích (many-to-many qua bảng favorites)
    public function favoriteVenues()
    {
        return $this->belongsToMany(Venue::class, 'favorites')->withTimestamps();
    }
}

