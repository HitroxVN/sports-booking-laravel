<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Court;
use App\Models\Venue;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ScheduleController extends Controller
{
    public function index(Request $request)
    {
        // Lấy danh sách khu sân của chủ sân
        $venues = Venue::where('owner_id', auth()->id())->with('courts')->get();

        // Nếu chủ sân chưa có sân nào
        if ($venues->isEmpty()) {
            return view('owner.schedule.index', [
                'venues' => $venues,
                'courts' => collect(),
                'bookings' => collect(),
                'selectedDate' => today(),
                'selectedVenueId' => null,
                'timeSlots' => collect(),
            ]);
        }

        // Lấy khu sân đang chọn (mặc định lấy khu đầu tiên)
        $selectedVenueId = $request->get('venue_id', $venues->first()->id);
        $selectedVenue = $venues->firstWhere('id', $selectedVenueId) ?? $venues->first();

        // Lấy các sân con thuộc khu sân này
        $courts = $selectedVenue->courts;
        $courtIds = $courts->pluck('id');

        // Lấy ngày cần xem lịch (mặc định là hôm nay)
        $selectedDate = $request->filled('date')
            ? Carbon::parse($request->date)
            : today();

        // Lấy giờ hoạt động thực tế của khu sân cho ngày đang xem
        $dayOfWeek = $selectedDate->dayOfWeek; // 0=Chủ nhật ... 6=Thứ 7
        $operatingHour = $selectedVenue->operatingHours()
            ->where('day_of_week', $dayOfWeek)
            ->first();

        if ($operatingHour && !$operatingHour->is_closed) {
            $openTime = $operatingHour->open_time;  // '06:00:00'
            $closeTime = $operatingHour->close_time; // '22:00:00'
        } else {
            // Fallback: không có operating_hours hoặc ngày nghỉ → dùng giờ mặc định
            $openTime = '06:00:00';
            $closeTime = '22:00:00';
        }

        // Tạo các mốc giờ từ open_time đến close_time (bước 1 tiếng)
        $timeSlots = collect();
        $current = Carbon::parse($openTime);
        $end = Carbon::parse($closeTime);
        while ($current->lt($end)) {
            $timeSlots->push($current->format('H:i:s'));
            $current->addHour();
        }
        $timeSlots->push($current->format('H:i:s')); // đẩy nốt mốc cuối

        // Lấy danh sách đơn đặt sân trong ngày đó của các sân con này
        $bookings = Booking::with(['user', 'court'])
            ->whereIn('court_id', $courtIds)
            ->whereDate('booking_date', $selectedDate)
            ->whereIn('status', ['pending', 'confirmed', 'completed'])
            ->get();

        return view('owner.schedule.index', compact(
            'venues',
            'selectedVenue',
            'courts',
            'bookings',
            'selectedDate',
            'selectedVenueId',
            'timeSlots'
        ));
    }
}