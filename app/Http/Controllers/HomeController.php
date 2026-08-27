<?php

namespace App\Http\Controllers;

use App\Models\Sport;
use App\Models\Venue;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
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
