<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\User;
use App\Models\Venue;

class DashboardController extends Controller
{
    public function index()
    {
        $totalUsers    = User::where('role', 'customer')->count();
        $totalOwners   = User::where('role', 'owner')->count();
        $totalVenues   = Venue::count();
        $pendingVenues = Venue::where('status', 'pending')->count();
        $totalBookings = Booking::count();
        $monthRevenue  = Booking::whereIn('status', ['confirmed', 'completed'])
                        ->whereMonth('booking_date', now()->month)
                        ->whereYear('booking_date', now()->year)
                        ->sum('total_amount');

        // Doanh thu 6 tháng gần nhất cho Chart.js
        $revenueChart = collect(range(5, 0))->map(fn ($i) => [
            'label'  => now()->subMonths($i)->format('m/Y'),
            'amount' => Booking::whereIn('status', ['confirmed', 'completed'])
                        ->whereYear('booking_date',  now()->subMonths($i)->year)
                        ->whereMonth('booking_date', now()->subMonths($i)->month)
                        ->sum('total_amount'),
        ]);

        return view('admin.dashboard.index', compact(
            'totalUsers', 'totalOwners', 'totalVenues', 'pendingVenues', 'totalBookings', 'monthRevenue', 'revenueChart'
        ));
    }
}
