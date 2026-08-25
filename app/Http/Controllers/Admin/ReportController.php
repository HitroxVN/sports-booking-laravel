<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $fromDate = $request->filled('from_date') ? Carbon::parse($request->from_date) : now()->startOfMonth();
        $toDate   = $request->filled('to_date')   ? Carbon::parse($request->to_date)   : now()->endOfMonth();

        $baseQuery = Booking::with(['court.venue', 'user'])
            ->whereBetween('booking_date', [$fromDate, $toDate])
            ->whereIn('status', ['confirmed', 'completed']);

        $totalRevenue  = (clone $baseQuery)->sum('total_amount');
        $totalBookings = (clone $baseQuery)->count();
        $totalDeposit  = (clone $baseQuery)->sum('deposit_amount');

        // Top 5 khu sân doanh thu cao nhất trong kỳ — group theo venue_id để tránh trùng tên
        $topVenues = (clone $baseQuery)->get()
            ->groupBy(fn ($b) => $b->court->venue->id ?? 0)
            ->map(fn ($g) => [
                'name'    => $g->first()->court->venue->name ?? 'N/A',
                'revenue' => $g->sum('total_amount'),
            ])
            ->sortByDesc('revenue')->take(5);

        $bookings = (clone $baseQuery)->latest()->paginate(20)->withQueryString();

        return view('admin.reports.index', compact(
            'totalRevenue', 'totalBookings', 'totalDeposit', 'topVenues', 'bookings', 'fromDate', 'toDate'
        ));
    }

    public function export(Request $request)
    {
        $fromDate = $request->filled('from_date') ? Carbon::parse($request->from_date) : now()->startOfMonth();
        $toDate   = $request->filled('to_date')   ? Carbon::parse($request->to_date)   : now()->endOfMonth();

        $bookings = Booking::with(['user', 'court.venue'])
            ->whereBetween('booking_date', [$fromDate, $toDate])
            ->whereIn('status', ['confirmed', 'completed'])
            ->get();

        $filename = 'report_' . $fromDate->format('Ymd') . '_' . $toDate->format('Ymd') . '.csv';

        return response()->streamDownload(function () use ($bookings) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM UTF-8 cho Excel
            fputcsv($handle, ['Mã đơn', 'Khách hàng', 'Khu sân', 'Ngày đặt', 'Tổng tiền', 'Trạng thái']);
            foreach ($bookings as $b) {
                fputcsv($handle, [
                    $b->code,
                    $b->user->name ?? '',
                    $b->court->venue->name ?? '',
                    $b->booking_date,
                    $b->total_amount,
                    $b->status,
                ]);
            }
            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}
