<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Sport;
use App\Models\Venue;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        // Lấy danh sách môn thể thao cho bộ lọc
        $sports = Sport::where('is_active', true)->get();

        // Lấy danh sách thành phố có sân đã được duyệt
        $cities = Venue::whereIn('status', ['active', 'approved'])
                       ->select('city')
                       ->distinct()
                       ->orderBy('city')
                       ->pluck('city');

        // Query cơ bản: chỉ sân đã được duyệt
        $query = Venue::whereIn('status', ['active', 'approved'])
                      ->with(['images', 'courts.sport', 'courts.slots']);

        // ── Bộ lọc: Từ khóa tìm kiếm (tên hoặc địa chỉ) ──
        if ($request->filled('q')) {
            $keyword = $request->q;
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                  ->orWhere('address', 'like', "%{$keyword}%")
                  ->orWhere('district', 'like', "%{$keyword}%")
                  ->orWhere('city', 'like', "%{$keyword}%");
            });
        }

        // ── Bộ lọc: Thành phố ──
        if ($request->filled('city')) {
            $query->where('city', $request->city);
        }

        // ── Bộ lọc: Môn thể thao (lọc theo court có sport_id tương ứng) ──
        if ($request->filled('sport_id')) {
            $query->whereHas('courts', function ($q) use ($request) {
                $q->where('sport_id', $request->sport_id)
                  ->where('status', 'active');
            });
        }

        // ── Sắp xếp ──
        $sortBy = $request->get('sort', 'latest');
        match ($sortBy) {
            'rating'  => $query->orderByDesc('rating_avg'),
            'name'    => $query->orderBy('name'),
            default   => $query->latest(),
        };

        // Phân trang - 9 sân mỗi trang
        $venues = $query->paginate(9)->withQueryString();

        return view('customer.search', compact('venues', 'sports', 'cities'));
    }
}
