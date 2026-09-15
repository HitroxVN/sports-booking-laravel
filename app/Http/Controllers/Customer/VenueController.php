<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Court;
use App\Models\Sport;
use App\Models\Venue;
use Illuminate\Http\Request;

class VenueController extends Controller
{
    /**
     * Trang "Sân nổi bật" (/venues/popular)
     * Xếp hạng khu sân theo lượt đặt thực tế (trừ đơn hủy), phụ trợ bằng
     * điểm đánh giá và số sân con đang mở.
     */
    public function popular(Request $request)
    {
        // 1. Lượt đặt hợp lệ theo từng khu sân (helper chung với trang chủ)
        $bookingCounts = Venue::bookingCountsByVenue();

        // 2. Các khu sân đang mở bán, kèm sân con (active) + môn + giá
        $venues = Venue::whereIn('status', ['active', 'approved'])
            ->with(['courts' => fn ($q) => $q->where('status', 'active')->with(['sport', 'slots'])])
            ->withCount([
                'reviews as reviews_count',
                'courts as courts_count' => fn ($q) => $q->where('status', 'active'),
            ])
            ->when($request->filled('sport_id'), fn ($q) => $q->whereHas('courts', fn ($c) => $c
                ->where('sport_id', $request->sport_id)
                ->where('status', 'active')))
            ->get()
            ->each(fn ($v) => $v->bookings_count = $bookingCounts[$v->id] ?? 0);

        // 3. Xếp hạng nhiều tiêu chí: lượt đặt > điểm đánh giá > số sân con
        $venues = $venues->sortBy([
            fn ($a, $b) => $b->bookings_count <=> $a->bookings_count,
            fn ($a, $b) => (float) $b->rating_avg <=> (float) $a->rating_avg,
            fn ($a, $b) => $b->courts_count <=> $a->courts_count,
        ])->values();

        // 4. Top 3 podium + phần còn lại
        $podium = $venues->take(3)->values();
        $rest   = $venues->skip(3)->values();

        // 5. Bộ lọc môn thể thao: chỉ các môn đang có sân mở bán ở khu sân hợp lệ
        $sports = Sport::where('is_active', true)
            ->whereHas('courts', fn ($q) => $q->where('status', 'active')
                ->whereHas('venue', fn ($v) => $v->whereIn('status', ['active', 'approved'])))
            ->withCount(['courts as courts_count' => fn ($q) => $q->where('status', 'active')])
            ->orderBy('name')
            ->get();

        return view('customer.venues.popular', compact('podium', 'rest', 'sports'));
    }

    /**
     * Hiển thị trang chi tiết khu sân và danh sách sân con có lọc.
     */
    public function show($slug, Request $request)
    {
        // 1. Lấy thông tin khu sân theo slug (yêu cầu trạng thái active hoặc approved)
        $venue = Venue::where('slug', $slug)
                      ->whereIn('status', ['active', 'approved'])
                      ->with([
                          'images',
                          'operatingHours',
                          'reviews.user',
                          'promotions' => fn($q) => $q->where('is_active', true),
                          'owner',
                      ])
                      ->firstOrFail();

        // 2. Lấy danh sách các môn thể thao có sân thuộc khu sân này (cho bộ lọc)
        $sports = Sport::whereHas('courts', function ($q) use ($venue) {
            $q->where('venue_id', $venue->id);
        })->get();

        // 3. Query danh sách sân con của khu sân này
        $courtsQuery = Court::where('venue_id', $venue->id)
                            ->with(['sport', 'slots']);

        // -- Bộ lọc 1: Từ khóa tên sân con --
        if ($request->filled('q')) {
            $keyword = $request->q;
            $courtsQuery->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                  ->orWhere('description', 'like', "%{$keyword}%");
            });
        }

        // -- Bộ lọc 2: Môn thể thao --
        if ($request->filled('sport_id')) {
            $courtsQuery->where('sport_id', $request->sport_id);
        }

        // -- Bộ lọc 3: Loại mặt sân --
        if ($request->filled('surface_type')) {
            $courtsQuery->where('surface_type', $request->surface_type);
        }

        // -- Bộ lọc 4: Trạng thái sân --
        if ($request->filled('status')) {
            $courtsQuery->where('status', $request->status);
        }

        // -- Sắp xếp --
        $sortBy = $request->get('sort', 'name');
        match ($sortBy) {
            'price_asc'  => $courtsQuery->withMin('slots', 'price')->orderBy('slots_min_price', 'asc'),
            'price_desc' => $courtsQuery->withMin('slots', 'price')->orderBy('slots_min_price', 'desc'),
            'latest'     => $courtsQuery->latest(),
            default      => $courtsQuery->orderBy('name'),
        };

        $courts = $courtsQuery->get();

        return view('customer.venues.show', compact('venue', 'courts', 'sports'));
    }
}
