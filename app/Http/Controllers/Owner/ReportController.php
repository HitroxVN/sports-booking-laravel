<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Venue;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $ownerId = auth()->id();
        $venues = Venue::where('owner_id', $ownerId)->get();
        $venueIds = $venues->pluck('id');

        // Lọc theo khoảng thời gian (Mặc định tháng này)
        $fromDate = $request->filled('from_date') ? Carbon::parse($request->from_date) : now()->startOfMonth();
        $toDate = $request->filled('to_date') ? Carbon::parse($request->to_date) : now()->endOfMonth();

        $query = Booking::with(['court.venue'])
            ->whereHas('court.venue', fn($q) => $q->where('owner_id', $ownerId))
            ->whereBetween('booking_date', [$fromDate, $toDate])
            ->whereIn('status', ['confirmed', 'completed']);

        // Tổng quan thống kê
        $totalRevenue = $query->sum('total_amount');
        $totalBookings = $query->count();
        $totalDeposit = $query->sum('deposit_amount');

        // Lấy danh sách chi tiết các đơn trong kỳ báo cáo để xem
        $bookings = $query->latest()->paginate(15);

        return view('owner.reports.index', compact(
            'venues', 'totalRevenue', 'totalBookings', 'totalDeposit', 
            'bookings', 'fromDate', 'toDate'
        ));
    }
}