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

        // Doanh thu 6 tháng gần nhất cho Chart.js — 1 query groupBy thay vì 6 query riêng
        $startOf6Months = now()->subMonths(5)->startOfMonth();
        $monthlyRevenue = Booking::whereIn('status', ['confirmed', 'completed'])
                        ->where('booking_date', '>=', $startOf6Months)
                        ->get()
                        ->groupBy(fn ($b) => $b->booking_date->format('Y-m'))
                        ->map->sum('total_amount');

        $revenueChart = collect(range(5, 0))->map(fn ($i) => [
            'label'  => now()->subMonths($i)->format('m/Y'),
            'amount' => $monthlyRevenue->get(now()->subMonths($i)->format('Y-m'), 0),
        ]);

        return view('admin.dashboard.index', compact(
            'totalUsers', 'totalOwners', 'totalVenues', 'pendingVenues', 'totalBookings', 'monthRevenue', 'revenueChart'
        ));
    }
}
