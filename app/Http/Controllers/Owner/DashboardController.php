<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Court;
use App\Models\Venue;

class DashboardController extends Controller
{
    public function index()
    {
        $ownerId = auth()->id();

        // Lấy danh sách ID các khu sân của chủ sân này
        $venueIds = Venue::where('owner_id', $ownerId)->pluck('id');
        
        // Lấy danh sách ID các sân con thuộc các khu sân trên
        $courtIds = Court::whereIn('venue_id', $venueIds)->pluck('id');

        // Thống kê số liệu tổng quan
        $totalVenues = $venueIds->count();
        $totalCourts = $courtIds->count();

        // Đơn đặt sân trong ngày hôm nay
        $todayBookingsCount = Booking::whereIn('court_id', $courtIds)
            ->whereDate('booking_date', today())
            ->count();

        // Tổng doanh thu tháng này (từ các đơn confirmed hoặc completed)
        $monthlyRevenue = Booking::whereIn('court_id', $courtIds)
            ->whereIn('status', ['confirmed', 'completed'])
            ->whereMonth('booking_date', now()->month)
            ->whereYear('booking_date', now()->year)
            ->sum('total_amount');

        // Danh sách 5 đơn đặt sân gần đây nhất cần chú ý
        $recentBookings = Booking::with(['user', 'court.venue'])
            ->whereIn('court_id', $courtIds)
            ->latest()
            ->take(5)
            ->get();

        return view('owner.dashboard.index', compact(
            'totalVenues', 
            'totalCourts', 
            'todayBookingsCount', 
            'monthlyRevenue', 
            'recentBookings'
        ));
    }
}