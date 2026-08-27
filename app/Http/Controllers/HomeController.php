<?php

namespace App\Http\Controllers;

use App\Models\Sport;
use App\Models\Venue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index()
    {
        // Admin không xem trang khách hàng — về thẳng trang quản trị
        if (Auth::check() && Auth::user()->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        // Lấy danh sách các môn thể thao đang hoạt động
        $sports = Sport::where('is_active', true)->get();

        // Lấy danh sách các khu sân nổi bật (đã được kích hoạt/duyệt)
        $featuredVenues = Venue::whereIn('status', ['active', 'approved'])
                               ->with(['images', 'courts.sport', 'courts.slots', 'reviews'])
                               ->inRandomOrder()
                               ->limit(8)
                               ->get();

        return view('home', compact('sports', 'featuredVenues'));
    }
}
