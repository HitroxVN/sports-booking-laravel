<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Court;
use App\Models\CourtSlot;
use App\Models\Venue;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $ownerId = auth()->id();

        // Lấy danh sách các khu sân thuộc sở hữu của chủ sân
        $venues = Venue::where('owner_id', $ownerId)
            ->with(['courts.sport', 'courts.slots', 'operatingHours'])
            ->get();

        $venueIds = $venues->pluck('id');
        $courts   = $venues->flatMap->courts;
        $courtIds = $courts->pluck('id');

        // Thống kê cơ sở
        $totalVenues = $venueIds->count();
        $totalCourts = $courtIds->count();

        // ── 1. Thẻ Doanh thu hôm nay (kèm nhãn xu hướng so với hôm trước) ──
        $todayRevenue = (float) Booking::whereIn('court_id', $courtIds)
            ->whereIn('status', ['confirmed', 'completed'])
            ->whereDate('booking_date', today())
            ->sum('total_amount');

        $yesterdayRevenue = (float) Booking::whereIn('court_id', $courtIds)
            ->whereIn('status', ['confirmed', 'completed'])
            ->whereDate('booking_date', today()->subDay())
            ->sum('total_amount');

        $revenueDiff = $todayRevenue - $yesterdayRevenue;
        if ($yesterdayRevenue > 0) {
            $revenueTrendPercent = round((($todayRevenue - $yesterdayRevenue) / $yesterdayRevenue) * 100, 1);
        } else {
            $revenueTrendPercent = $todayRevenue > 0 ? 100.0 : 0.0;
        }

        // ── 2. Thẻ Lượt đặt hôm nay (kèm số trận đang diễn ra) ──
        $todayBookingsCount = Booking::whereIn('court_id', $courtIds)
            ->whereDate('booking_date', today())
            ->where('status', '!=', 'cancelled')
            ->count();

        $currentTime = now()->format('H:i:s');
        $ongoingMatchesCount = Booking::whereIn('court_id', $courtIds)
            ->whereDate('booking_date', today())
            ->whereIn('status', ['confirmed', 'completed'])
            ->where('start_time', '<=', $currentTime)
            ->where('end_time', '>=', $currentTime)
            ->count();

        // ── 3. Thẻ Hiệu suất kín sân (Tỷ lệ % thời gian lấp đầy trong ngày) ──
        $dayOfWeek = (int) today()->dayOfWeek;
        $totalCapacityMinutes = 0;

        foreach ($courts as $court) {
            if ($court->slots && $court->slots->isNotEmpty()) {
                $cells = CourtSlot::buildTimeCells($court->slots, today()->toDateString());
                foreach ($cells as $cell) {
                    if (!empty($cell['is_open'])) {
                        $startMin = (int) substr($cell['start'], 0, 2) * 60 + (int) substr($cell['start'], 3, 2);
                        $endMin   = (int) substr($cell['end'], 0, 2) * 60 + (int) substr($cell['end'], 3, 2);
                        $diff     = $endMin - $startMin;
                        $totalCapacityMinutes += ($diff > 0 ? $diff : 60);
                    }
                }
            } else {
                $op = $court->venue->operatingHours->firstWhere('day_of_week', $dayOfWeek);
                if ($op && !$op->is_closed && $op->open_time && $op->close_time) {
                    $open  = Carbon::parse($op->open_time);
                    $close = Carbon::parse($op->close_time);
                    $totalCapacityMinutes += $open->diffInMinutes($close);
                } else {
                    // Mặc định 16 tiếng mở cửa (06:00 - 22:00 = 960 phút)
                    $totalCapacityMinutes += 960;
                }
            }
        }

        $totalBookedMinutes = (int) Booking::whereIn('court_id', $courtIds)
            ->whereDate('booking_date', today())
            ->where('status', '!=', 'cancelled')
            ->sum('duration');

        if ($totalCapacityMinutes > 0) {
            $occupancyRate = min(100.0, round(($totalBookedMinutes / $totalCapacityMinutes) * 100, 1));
        } else {
            $occupancyRate = 0.0;
        }

        $bookedHours        = round($totalBookedMinutes / 60, 1);
        $totalCapacityHours = round($totalCapacityMinutes / 60, 1);

        // ── 4. Thẻ Chờ xác nhận (Pending Bookings) ──
        $pendingBookingsCount = Booking::whereIn('court_id', $courtIds)
            ->where('status', 'pending')
            ->count();

        // ── 5. Cột Trái: Lịch thi đấu trong ngày (Next Up / Upcoming Matches) ──
        $todayMatches = Booking::with(['user', 'court.venue', 'court.sport'])
            ->whereIn('court_id', $courtIds)
            ->whereDate('booking_date', today())
            ->whereIn('status', ['pending', 'confirmed', 'completed'])
            ->orderBy('start_time', 'asc')
            ->get();

        // ── 6. Cột Phải: Biểu đồ doanh thu 7 ngày gần nhất ──
        $last7Days = collect();
        for ($i = 6; $i >= 0; $i--) {
            $d = today()->subDays($i);
            $dKey = $d->toDateString();
            $last7Days->put($dKey, [
                'date'    => $dKey,
                'label'   => $d->isToday() ? 'Hôm nay' : ($d->isYesterday() ? 'Hôm qua' : $d->format('d/m')),
                'dayName' => $d->locale('vi')->minDayName,
                'amount'  => 0,
            ]);
        }

        $dailyRevenues = Booking::whereIn('court_id', $courtIds)
            ->whereIn('status', ['confirmed', 'completed'])
            ->whereBetween('booking_date', [today()->subDays(6)->toDateString(), today()->toDateString()])
            ->select(DB::raw('DATE(booking_date) as b_date'), DB::raw('SUM(total_amount) as total'))
            ->groupBy('b_date')
            ->pluck('total', 'b_date');

        foreach ($dailyRevenues as $bDate => $total) {
            if ($last7Days->has($bDate)) {
                $item = $last7Days->get($bDate);
                $item['amount'] = (float) $total;
                $last7Days->put($bDate, $item);
            }
        }

        $chartLabels      = $last7Days->pluck('label')->toArray();
        $chartValues      = $last7Days->pluck('amount')->toArray();
        $weekTotalRevenue = array_sum($chartValues);
        $weekAvgDaily     = $weekTotalRevenue / 7;

        return view('owner.dashboard.index', compact(
            'venues',
            'courts',
            'totalVenues',
            'totalCourts',
            'todayRevenue',
            'yesterdayRevenue',
            'revenueDiff',
            'revenueTrendPercent',
            'todayBookingsCount',
            'ongoingMatchesCount',
            'occupancyRate',
            'bookedHours',
            'totalCapacityHours',
            'pendingBookingsCount',
            'todayMatches',
            'chartLabels',
            'chartValues',
            'weekTotalRevenue',
            'weekAvgDaily'
        ));
    }
}