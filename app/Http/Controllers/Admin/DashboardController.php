<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\User;
use App\Models\Venue;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->input('period', 'month'); // 'day', 'month', 'year'

        // 1. Chỉ số tổng thể hệ thống
        $totalUsers    = User::where('role', 'customer')->count();
        $totalOwners   = User::where('role', 'owner')->count();
        $totalVenues   = Venue::count();
        $pendingVenues = Venue::where('status', 'pending')->count();
        $totalBookings = Booking::count();
        $monthRevenue  = Booking::whereIn('status', ['confirmed', 'completed'])
            ->whereMonth('booking_date', now()->month)
            ->whereYear('booking_date', now()->year)
            ->sum('total_amount');

        // 2. Thống kê Doanh thu theo Kỳ (Lọc Ngày / Tháng / Năm)
        $revenueChart = $this->buildRevenueChart($period);

        // 3. Tỷ trọng theo Phương thức / Cổng thanh toán
        $paymentBreakdown = $this->buildPaymentBreakdown($period);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'period'            => $period,
                'revenueChart'      => $revenueChart,
                'paymentBreakdown'  => $paymentBreakdown,
            ]);
        }

        return view('admin.dashboard.index', compact(
            'totalUsers',
            'totalOwners',
            'totalVenues',
            'pendingVenues',
            'totalBookings',
            'monthRevenue',
            'period',
            'revenueChart',
            'paymentBreakdown'
        ));
    }

    /**
     * Xây dựng dữ liệu biểu đồ doanh thu theo Ngày, Tháng hoặc Năm
     */
    protected function buildRevenueChart(string $period): array
    {
        $baseQuery = Booking::whereIn('status', ['confirmed', 'completed']);

        if ($period === 'day') {
            // 30 ngày gần nhất
            $startDate = now()->subDays(29)->startOfDay();
            $bookings = (clone $baseQuery)
                ->where('booking_date', '>=', $startDate)
                ->get()
                ->groupBy(fn ($b) => $b->booking_date->format('Y-m-d'))
                ->map->sum('total_amount');

            $chartData = collect(range(29, 0))->map(function ($i) use ($bookings) {
                $date = now()->subDays($i);
                $key = $date->format('Y-m-d');
                return [
                    'label'  => $date->format('d/m'),
                    'full'   => $date->format('d/m/Y'),
                    'amount' => (float) ($bookings->get($key, 0)),
                ];
            });

            return [
                'type'        => 'day',
                'title'       => 'Doanh thu 30 ngày gần nhất',
                'labels'      => $chartData->pluck('label')->toArray(),
                'amounts'     => $chartData->pluck('amount')->toArray(),
                'totalPeriod' => $chartData->sum('amount'),
                'series'      => $chartData->toArray(),
            ];
        }

        if ($period === 'year') {
            // 5 năm gần nhất
            $currentYear = (int) now()->year;
            $startYear = $currentYear - 4;
            $bookings = (clone $baseQuery)
                ->whereYear('booking_date', '>=', $startYear)
                ->get()
                ->groupBy(fn ($b) => $b->booking_date->format('Y'))
                ->map->sum('total_amount');

            $chartData = collect(range(4, 0))->map(function ($i) use ($currentYear, $bookings) {
                $year = (string) ($currentYear - $i);
                return [
                    'label'  => 'Năm ' . $year,
                    'full'   => 'Năm ' . $year,
                    'amount' => (float) ($bookings->get($year, 0)),
                ];
            });

            return [
                'type'        => 'year',
                'title'       => 'Doanh thu 5 năm gần nhất',
                'labels'      => $chartData->pluck('label')->toArray(),
                'amounts'     => $chartData->pluck('amount')->toArray(),
                'totalPeriod' => $chartData->sum('amount'),
                'series'      => $chartData->toArray(),
            ];
        }

        // Mặc định: 12 tháng gần nhất
        $startMonth = now()->subMonths(11)->startOfMonth();
        $bookings = (clone $baseQuery)
            ->where('booking_date', '>=', $startMonth)
            ->get()
            ->groupBy(fn ($b) => $b->booking_date->format('Y-m'))
            ->map->sum('total_amount');

        $chartData = collect(range(11, 0))->map(function ($i) use ($bookings) {
            $monthDate = now()->subMonths($i);
            $key = $monthDate->format('Y-m');
            return [
                'label'  => $monthDate->format('m/Y'),
                'full'   => 'Tháng ' . $monthDate->format('m/Y'),
                'amount' => (float) ($bookings->get($key, 0)),
            ];
        });

        return [
            'type'        => 'month',
            'title'       => 'Doanh thu 12 tháng gần nhất',
            'labels'      => $chartData->pluck('label')->toArray(),
            'amounts'     => $chartData->pluck('amount')->toArray(),
            'totalPeriod' => $chartData->sum('amount'),
            'series'      => $chartData->toArray(),
        ];
    }

    /**
     * Xây dựng dữ liệu tỷ trọng theo phương thức thanh toán
     */
    protected function buildPaymentBreakdown(string $period): array
    {
        // Phân tích từ các giao dịch thanh toán thành công trong bảng payments
        $paymentQuery = Payment::where('status', 'success')->where('type', '!=', 'refund');

        if ($period === 'day') {
            $paymentQuery->where('created_at', '>=', now()->subDays(29)->startOfDay());
        } elseif ($period === 'year') {
            $paymentQuery->where('created_at', '>=', now()->subYears(4)->startOfYear());
        } else {
            $paymentQuery->where('created_at', '>=', now()->subMonths(11)->startOfMonth());
        }

        $gatewayGroups = $paymentQuery->get()->groupBy('gateway');

        $gatewayConfigs = [
            'sepay' => ['label' => 'SePay (QR Chuyển khoản)', 'color' => '#6366f1'],
            'vnpay' => ['label' => 'VNPay Cổng thẻ/QR',       'color' => '#3b82f6'],
            'momo'  => ['label' => 'Ví MoMo',                 'color' => '#ec4899'],
            'cash'  => ['label' => 'Tiền mặt tại sân',         'color' => '#10b981'],
        ];

        $labels = [];
        $amounts = [];
        $colors = [];
        $items = [];
        $totalAmount = 0;

        foreach ($gatewayConfigs as $key => $cfg) {
            $sum = (float) ($gatewayGroups->get($key)?->sum('amount') ?? 0);
            $count = (int) ($gatewayGroups->get($key)?->count() ?? 0);
            $totalAmount += $sum;

            $items[] = [
                'gateway' => $key,
                'label'   => $cfg['label'],
                'amount'  => $sum,
                'count'   => $count,
                'color'   => $cfg['color'],
            ];
        }

        // Nếu bảng payments chưa đủ dữ liệu do dùng booking payment_method
        if ($totalAmount === 0) {
            // Lấy trực tiếp từ Bookings hoàn tất
            $bookings = Booking::whereIn('status', ['confirmed', 'completed'])->get();
            $methodConfigs = [
                'full_online'    => ['label' => 'Thanh toán trực tuyến 100%', 'color' => '#6366f1'],
                'partial_online' => ['label' => 'Đặt cọc trực tuyến',          'color' => '#3b82f6'],
                'at_venue'       => ['label' => 'Thanh toán tại sân',        'color' => '#10b981'],
            ];

            $methodGroups = $bookings->groupBy('payment_method');
            $items = [];
            foreach ($methodConfigs as $mKey => $mCfg) {
                $mSum = (float) ($methodGroups->get($mKey)?->sum('total_amount') ?? 0);
                $mCount = (int) ($methodGroups->get($mKey)?->count() ?? 0);
                $totalAmount += $mSum;

                $items[] = [
                    'gateway' => $mKey,
                    'label'   => $mCfg['label'],
                    'amount'  => $mSum,
                    'count'   => $mCount,
                    'color'   => $mCfg['color'],
                ];
            }
        }

        // Tính tỷ lệ phần trăm
        foreach ($items as &$item) {
            $item['percentage'] = $totalAmount > 0 ? round(($item['amount'] / $totalAmount) * 100, 1) : 0;
            $labels[] = $item['label'];
            $amounts[] = $item['amount'];
            $colors[] = $item['color'];
        }

        return [
            'total'   => $totalAmount,
            'labels'  => $labels,
            'amounts' => $amounts,
            'colors'  => $colors,
            'items'   => $items,
        ];
    }
}
