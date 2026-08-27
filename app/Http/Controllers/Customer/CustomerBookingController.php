<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Court;
use App\Models\CourtClosure;
use App\Models\CourtSlot;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CustomerBookingController extends Controller
{
    // 1. Hiển thị lịch sử đặt sân của tôi (/my-bookings)
    public function index()
    {
        $bookings = Booking::with(['court.venue'])
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        return view('customer.bookings.index', compact('bookings'));
    }

    // 2. Hiển thị sơ đồ chọn giờ đặt sân
    public function create($courtId)
    {
        $court = $this->findBookableCourt($courtId);

        $dates = [];
        for ($i = 0; $i < 7; $i++) {
            $date = Carbon::today()->addDays($i);
            $dates[] = [
                'full_date'  => $date->format('Y-m-d'),
                'day_name'   => $date->locale('vi')->dayName,
                'formatted'  => $date->format('d/m'),
                'is_today'   => $date->isToday(),
            ];
        }

        $existingBookings = Booking::where('court_id', $courtId)
            ->where('booking_date', '>=', Carbon::today()->toDateString())
            ->where('status', '!=', 'cancelled')
            ->get(['booking_date', 'start_time', 'end_time']);

        // Lịch khóa của sân (7 ngày tới) — để chặn hiển thị/đặt các khung giờ bị khóa
        $closures = CourtClosure::where('court_id', $courtId)
            ->whereDate('date', '>=', Carbon::today()->toDateString())
            ->whereDate('date', '<=', Carbon::today()->addDays(6)->toDateString())
            ->get(['date', 'start_time', 'end_time']);

        return view('customer.bookings.create', compact('court', 'dates', 'existingBookings', 'closures'));
    }

    // 3. Xử lý đặt sân + Tính tiền cộng dồn theo khung giờ
    public function store(Request $request)
    {
        $request->validate([
            'court_id'     => 'required|exists:courts,id',
            'booking_date' => 'required|date|after_or_equal:today',
            'start_time'   => 'required',
            'end_time'     => 'required|after:start_time',
        ]);

        $court = $this->findBookableCourt($request->court_id);

        $isClosed = CourtClosure::where('court_id', $court->id)
            ->whereDate('date', $request->booking_date)
            ->where(function ($query) use ($request) {
                // Khóa cả ngày (start_time null)
                $query->whereNull('start_time')
                    // Khóa theo khung giờ: trùng lặp khoảng
                    ->orWhere(function ($q) use ($request) {
                        $q->whereNotNull('start_time')
                          ->where('start_time', '<', $request->end_time)
                          ->where('end_time', '>', $request->start_time);
                    });
            })
            ->exists();

        if ($isClosed) {
            return back()->with('error', 'Sân đang bị khóa lịch trong khoảng thời gian này, vui lòng chọn thời gian khác!');
        }

        $isBooked = Booking::where('court_id', $court->id)
            ->where('booking_date', $request->booking_date)
            ->where('status', '!=', 'cancelled')
            ->where(function ($query) use ($request) {
                $query->where('start_time', '<', $request->end_time)
                      ->where('end_time', '>', $request->start_time);
            })
            ->exists();

        if ($isBooked) {
            return back()->with('error', 'Khung giờ này đã có người đặt, vui lòng chọn giờ khác!');
        }

        $startTime = Carbon::parse($request->start_time);
        $endTime   = Carbon::parse($request->end_time);
        $duration  = $startTime->diffInMinutes($endTime);

        $courtSlots = CourtSlot::where('court_id', $request->court_id)->get();
        $totalAmount = 0;

        $current = $startTime->copy();
        while ($current < $endTime) {
            $next = $current->copy()->addHour();
            $cStart = $current->format('H:i:s');
            $cEnd   = $next->format('H:i:s');

            $matchedSlot = $courtSlots->first(function ($slot) use ($cStart, $cEnd) {
                return $cStart >= $slot->start_time && $cEnd <= $slot->end_time;
            });

            if ($matchedSlot) {
                $rate = ($matchedSlot->is_peak && $matchedSlot->peak_price) ? $matchedSlot->peak_price : $matchedSlot->price;
            } else {
                $rate = 100000;
            }

            $totalAmount += $rate;
            $current->addHour();
        }

        $avgHourlyRate = ($duration > 0) ? ($totalAmount / ($duration / 60)) : 0;

        Booking::create([
            'code'           => 'BK-' . strtoupper(Str::random(8)),
            'user_id'        => Auth::id(),
            'court_id'       => $court->id,
            'booking_date'   => $request->booking_date,
            'start_time'     => $request->start_time,
            'end_time'       => $request->end_time,
            'duration'       => $duration,
            'price_snapshot' => $avgHourlyRate,
            'total_amount'   => $totalAmount,
            'status'         => 'pending',
        ]);

        return redirect()->route('customer.bookings.index')->with('success', 'Đặt sân thành công!');
    }

    /**
     * Lấy sân có thể đặt: phải tồn tại, đang hoạt động, và thuộc khu sân đã được duyệt.
     */
    private function findBookableCourt($courtId)
    {
        $court = Court::with(['venue', 'slots'])
            ->whereHas('venue', fn ($q) => $q->where('status', 'active'))
            ->findOrFail($courtId);

        abort_if($court->status !== 'active', 403, 'Sân này hiện không nhận đặt (bảo trì/đóng cửa).');

        return $court;
    }
}