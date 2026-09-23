<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Venue extends Model
{
    use HasFactory, SoftDeletes, HasSlug;

    protected $fillable = [
        'owner_id', 'name', 'slug', 'description',
        'address', 'ward', 'district', 'city',
        'latitude', 'longitude', 'phone', 'email',
        'status', 'reject_reason', 'cover_image', 'amenities', 'rating_avg',
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

    /** Nhãn tiếng Việt cho tiện ích — dùng chung form owner + hiển thị khách */
    public const AMENITY_LABELS = [
        'wifi'            => 'Wifi miễn phí',
        'parking'         => 'Bãi đỗ xe',
        'canteen'         => 'Căng tin/Nước',
        'changing_room'   => 'Phòng thay đồ',
        'shower'          => 'Phòng tắm/vệ sinh',
        'air_conditioner' => 'Máy lạnh',
    ];

    /** Dòng địa chỉ hiển thị: số nhà + phường/xã (mới, ưu tiên hơn quận cũ) + tỉnh/TP */
    public function getAddressLineAttribute(): string
    {
        return collect([$this->address, $this->ward ?: $this->district, $this->city])
            ->filter()->implode(', ');
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

    // --- Helpers ---

    /**
     * Số lượt đặt hợp lệ (không hủy) theo từng khu sân — đếm qua sân con.
     * Trả về mảng [venue_id => total]. Dùng chung cho home + bảng xếp hạng.
     * Chú ý soft deletes trên cả bookings lẫn courts.
     */
    public static function bookingCountsByVenue(): array
    {
        return DB::table('bookings')
            ->join('courts', fn ($join) => $join
                ->on('bookings.court_id', '=', 'courts.id')
                ->whereNull('courts.deleted_at'))
            ->whereNull('bookings.deleted_at')
            ->where('bookings.status', '!=', 'cancelled')
            ->groupBy('courts.venue_id')
            ->selectRaw('courts.venue_id as venue_id, count(*) as total')
            ->pluck('total', 'venue_id')
            ->all();
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

    /**
     * Xóa mềm khu sân kèm tự động hủy các đơn chưa diễn ra — trong 1 transaction.
     * Lỗi ở bất kỳ bước nào sẽ rollback toàn bộ, tránh tình trạng
     * đơn đã hủy mà venue còn nguyên hoặc venue bị xóa mà đơn vẫn treo.
     * Trả về số đơn bị hủy tự động.
     */
    public function deleteWithBookings(): int
    {
        return DB::transaction(function () {
            // Đơn chưa diễn ra của khu sân: chưa hủy/completed và ngày đặt từ hôm nay trở đi
            $cancelled = Booking::whereHas('court', fn ($q) => $q->where('venue_id', $this->id))
                ->whereIn('status', ['pending', 'confirmed'])
                ->where('booking_date', '>=', today())
                ->update([
                    'status'        => 'cancelled',
                    'cancel_reason' => 'Khu sân đã bị gỡ khỏi hệ thống. Vui lòng liên hệ hỗ trợ để được xử lý hoàn tiền.',
                    'cancelled_at'  => now(),
                ]);

            // Soft delete — ném exception ở đây sẽ rollback cả phần hủy đơn phía trên
            $this->delete();

            return $cancelled;
        });
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
