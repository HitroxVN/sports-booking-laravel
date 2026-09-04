<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Venue;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $ownerId = auth()->id();
        $venues = Venue::where('owner_id', $ownerId)->get();

        // Bộ lọc ngày (mặc định 30 ngày gần nhất)
        $startDate = $request->input('start_date', Carbon::now()->subDays(29)->format('Y-m-d'));
        $endDate   = $request->input('end_date', Carbon::now()->format('Y-m-d'));
        $venueId   = $request->input('venue_id');

        // Base Query lọc theo chủ sân và khoảng thời gian
        $basePaymentQuery = Payment::query()
            ->where('payments.status', 'success')
            ->whereBetween('payments.created_at', [
                Carbon::parse($startDate)->startOfDay(),
                Carbon::parse($endDate)->endOfDay()
            ])
            ->whereHas('booking.court.venue', function ($q) use ($ownerId, $venueId) {
                $q->where('owner_id', $ownerId);
                if (!empty($venueId)) {
                    $q->where('id', $venueId);
                }
            });

        // 1. Thống kê tổng quan (KPIs)
        $totalRevenue = (clone $basePaymentQuery)->sum('amount');
        $successfulTransactions = (clone $basePaymentQuery)->count();
        $totalBookings = (clone $basePaymentQuery)->distinct('booking_id')->count('booking_id');

        // 2. Doanh thu theo phương thức thanh toán (gateway)
        $revenueByGateway = (clone $basePaymentQuery)
            ->select('gateway', DB::raw('SUM(amount) as total'), DB::raw('COUNT(*) as count'))
            ->groupBy('gateway')
            ->get();

        // 3. Biểu đồ doanh thu từng ngày
        $dailyData = (clone $basePaymentQuery)
            ->select(DB::raw('DATE(payments.created_at) as date'), DB::raw('SUM(amount) as total'))
            ->groupBy('date')
            ->orderBy('date', 'ASC')
            ->pluck('total', 'date')
            ->toArray();

        // Lấp đầy các ngày không có doanh thu bằng 0
        $chartLabels = [];
        $chartValues = [];
        $period = Carbon::parse($startDate)->daysUntil(Carbon::parse($endDate));

        foreach ($period as $dt) {
            $formattedDate = $dt->format('Y-m-d');
            $chartLabels[] = $dt->format('d/m');
            $chartValues[] = $dailyData[$formattedDate] ?? 0;
        }

        // 4. Lịch sử giao dịch gần nhất
        $recentPayments = (clone $basePaymentQuery)
            ->with(['booking.court.venue', 'booking.user'])
            ->latest('payments.created_at')
            ->paginate(10)
            ->withQueryString();

        return view('owner.reports.index', compact(
            'venues',
            'startDate',
            'endDate',
            'venueId',
            'totalRevenue',
            'successfulTransactions',
            'totalBookings',
            'revenueByGateway',
            'chartLabels',
            'chartValues',
            'recentPayments'
        ));
    }
}