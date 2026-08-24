<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Venue extends Model
{
    use HasFactory, SoftDeletes, HasSlug;

    protected $fillable = [
        'owner_id', 'name', 'slug', 'description',
        'address', 'ward', 'district', 'city',
        'latitude', 'longitude', 'phone', 'email',
        'status', 'cover_image', 'amenities', 'rating_avg',
    ];

    protected function casts(): array
    {
        return [
            'amenities'  => 'array',
            'rating_avg' => 'decimal:2',
            'latitude'   => 'decimal:7',
            'longitude'  => 'decimal:7',
        ];
    }

    // Auto-generate slug từ name
    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    // --- Relationships ---

    // Khu sân thuộc về 1 chủ sân
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    // 1 khu sân có nhiều ảnh
    public function images()
    {
        return $this->hasMany(VenueImage::class)->orderBy('sort_order');
    }

    // 1 khu sân có nhiều sân con
    public function courts()
    {
        return $this->hasMany(Court::class);
    }

    // Giờ hoạt động trong tuần (7 ngày)
    public function operatingHours()
    {
        return $this->hasMany(OperatingHour::class)->orderBy('day_of_week');
    }

    // Đánh giá của khu sân
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    // Mã khuyến mãi
    public function promotions()
    {
        return $this->hasMany(Promotion::class);
    }

    // Người dùng yêu thích (many-to-many)
    public function favoritedByUsers()
    {
        return $this->belongsToMany(User::class, 'favorites')->withTimestamps();
    }
}
