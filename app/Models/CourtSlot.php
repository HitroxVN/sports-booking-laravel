<?php

namespace App\Models;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourtSlot extends Model
{
    use HasFactory;
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

    /**
     * Cắt danh sách khung giờ của một ngày thành các ô chọn giờ cho khách đặt.
     *
     * - Lọc slot theo thứ của ngày ($date) — slot có day_of_week = null áp dụng mọi ngày.
     *   Slot gắn thứ cụ thể được ưu tiên hơn slot "mọi ngày" khi trùng mốc giờ.
     * - Sắp xếp theo giờ bắt đầu, cắt mỗi khung thành các ô 1 tiếng tính từ mốc bắt đầu,
     *   phần lẻ cuối cùng thành ô nhỏ hơn (VD: 6:30-9:00 → 6:30-7:30, 7:30-8:30, 8:30-9:00).
     * - Giá ô lẻ = giá hiệu dụng × số phút / 60.
     * - Khoảng trống giữa 2 khung vẫn hiện ra như một ô không đặt được (is_open = false)
     *   để khách thấy sân đóng trong khoảng đó.
     *
     * @param  iterable<CourtSlot>  $slots  Danh sách slot (chưa lọc theo ngày)
     * @param  string  $date  Ngày đặt định dạng Y-m-d
     * @return array<int, array{start: string, end: string, price: float, is_full_hour: bool, is_open: bool}>
     */
    public static function buildTimeCells(iterable $slots, string $date): array
    {
        // 0 = Chủ Nhật ... 6 = Thứ Bảy (khớp quy ước day_of_week trong form chủ sân)
        $dayOfWeek = (int) Carbon::parse($date)->dayOfWeek;

        // Slot gắn thứ cụ thể ưu tiên hơn slot "mọi ngày" (day_of_week = null)
        $specific = collect($slots)
            ->filter(fn ($slot) => $slot->day_of_week !== null && (int) $slot->day_of_week === $dayOfWeek)
            ->sortBy('start_time')
            ->values();
        $everyday = collect($slots)
            ->filter(fn ($slot) => $slot->day_of_week === null)
            ->sortBy('start_time')
            ->values();

        if ($specific->isEmpty() && $everyday->isEmpty()) {
            return [];
        }

        // Cắt từng khung thành các ô 1 tiếng tính từ mốc bắt đầu của khung,
        // phần lẻ cuối cùng thành ô nhỏ hơn (VD: 6:30-8:00 → 6:30-7:30 + 7:30-8:00)
        $cells = collect();
        foreach ([[$specific, 1], [$everyday, 0]] as [$tier, $priority]) {
            foreach ($tier as $slot) {
                $slotStart = self::toMinutes($slot->start_time);
                $slotEnd = self::toMinutes($slot->end_time);
                $hourlyPrice = $slot->effective_price;

                $current = $slotStart;
                while ($current < $slotEnd) {
                    $cellEnd = min($current + 60, $slotEnd);
                    $minutes = $cellEnd - $current;

                    $cells->push([
                        'start'        => self::toTimeString($current),
                        'end'          => self::toTimeString($cellEnd),
                        'price'        => round($hourlyPrice * $minutes / 60),
                        'is_full_hour' => $minutes === 60,
                        'is_open'      => true,
                        '_start_min'   => $current,
                        '_end_min'     => $cellEnd,
                        '_priority'    => $priority,
                    ]);

                    $current = $cellEnd;
                }
            }
        }

        // Sắp xếp theo giờ bắt đầu (trùng mốc thì ưu tiên slot theo thứ cụ thể),
        // bỏ ô trùng lặp (khi slot "mọi ngày" và slot theo thứ chạm nhau)
        $cells = $cells->sortBy([['_start_min', 'asc'], ['_priority', 'desc']])->values()
            ->unique(fn ($cell) => $cell['_start_min'])
            ->values();

        // Lấp khoảng trống giữa 2 khung bằng ô đóng (is_open = false) để khách thấy rõ
        $filled = collect();
        foreach ($cells as $cell) {
            $prev = $filled->last();
            if ($prev && $prev['_end_min'] < $cell['_start_min']) {
                $filled->push([
                    'start'        => self::toTimeString($prev['_end_min']),
                    'end'          => self::toTimeString($cell['_start_min']),
                    'price'        => 0,
                    'is_full_hour' => false,
                    'is_open'      => false,
                    '_start_min'   => $prev['_end_min'],
                    '_end_min'     => $cell['_start_min'],
                ]);
            }
            $filled->push($cell);
        }

        return $filled
            ->map(fn ($cell) => collect($cell)->except(['_start_min', '_end_min', '_priority'])->all())
            ->all();
    }

    /**
     * Đổi "H:i[:s]" thành số phút từ 00:00.
     */
    private static function toMinutes($time): int
    {
        $parts = explode(':', substr((string) $time, 0, 5));

        return ((int) $parts[0]) * 60 + (int) $parts[1];
    }

    /**
     * Đổi số phút từ 00:00 thành "H:i".
     */
    private static function toTimeString(int $minutes): string
    {
        return sprintf('%02d:%02d', intdiv($minutes, 60), $minutes % 60);
    }

    // Trả giá áp dụng (giờ vàng hoặc giá thường)
    public function getEffectivePriceAttribute(): float
    {
        return $this->is_peak && $this->peak_price
            ? (float) $this->peak_price
            : (float) $this->price;
    }
}
