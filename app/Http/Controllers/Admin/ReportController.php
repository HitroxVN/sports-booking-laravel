<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Payment;
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

        // Doanh thu theo phương thức thanh toán (giống Dashboard)
        $paymentQuery = Payment::query()
            ->where('payments.status', 'success')
            ->whereBetween('payments.created_at', [$fromDate->copy()->startOfDay(), $toDate->copy()->endOfDay()]);

        $gatewayGroups = $paymentQuery->get()->groupBy('gateway');
        $gatewayConfigs = [
            'sepay' => ['label' => 'SePay (QR Chuyển khoản)', 'color' => '#6366f1'],
            'vnpay' => ['label' => 'VNPay Cổng thẻ/QR',       'color' => '#3b82f6'],
            'momo'  => ['label' => 'Ví MoMo',                 'color' => '#ec4899'],
            'cash'  => ['label' => 'Tiền mặt tại sân',         'color' => '#10b981'],
        ];
        $paymentBreakdown = ['total' => 0, 'items' => []];
        $labels = [];
        $amounts = [];
        $colors = [];
        foreach ($gatewayConfigs as $key => $cfg) {
            $sum = (float) ($gatewayGroups->get($key)?->sum('amount') ?? 0);
            $count = (int) ($gatewayGroups->get($key)?->count() ?? 0);
            if ($sum <= 0) {
                continue;
            }
            $paymentBreakdown['total'] += $sum;
            $labels[] = $cfg['label'];
            $amounts[] = $sum;
            $colors[] = $cfg['color'];
            $paymentBreakdown['items'][] = [
                'gateway'    => $key,
                'label'      => $cfg['label'],
                'amount'     => $sum,
                'count'      => $count,
                'percentage' => 0, // tính sau khi có tổng
                'color'      => $cfg['color'],
            ];
        }
        foreach ($paymentBreakdown['items'] as &$item) {
            $item['percentage'] = $paymentBreakdown['total'] > 0 ? round($item['amount'] / $paymentBreakdown['total'] * 100, 1) : 0;
        }
        unset($item);

        // Biểu đồ doanh thu theo từng ngày trong kỳ
        $dailyData = (clone $paymentQuery)
            ->selectRaw('DATE(created_at) as date, SUM(amount) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('total', 'date')
            ->toArray();

        $chartLabels = [];
        $chartValues = [];
        foreach (Carbon::parse($fromDate)->daysUntil(Carbon::parse($toDate)) as $dt) {
            $chartLabels[] = $dt->format('d/m');
            $chartValues[] = (float) ($dailyData[$dt->format('Y-m-d')] ?? 0);
        }

        return view('admin.reports.index', compact(
            'totalRevenue', 'totalBookings', 'totalDeposit', 'topVenues', 'bookings', 'fromDate', 'toDate',
            'paymentBreakdown', 'chartLabels', 'chartValues'
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
