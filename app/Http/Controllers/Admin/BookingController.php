<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    // index — read-only, toàn platform
    public function index(Request $request)
    {
        $query = Booking::with(['user', 'court.venue'])->latest();
        if ($request->filled('status')) $query->where('status', $request->status);
        if ($request->filled('search')) $query->where('code', 'like', "%{$request->search}%");
        $bookings = $query->paginate(20)->withQueryString();
        return view('admin.bookings.index', compact('bookings'));
    }
}
