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
