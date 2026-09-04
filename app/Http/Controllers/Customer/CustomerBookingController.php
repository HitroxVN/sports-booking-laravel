<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Court;
use App\Models\CourtClosure;
use App\Models\CourtSlot;
use App\Models\OperatingHour;
use Carbon\Carbon;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

// Báo hiệu khung giờ bị chiếm/khóa — bắt ngoài transaction để trả flash message thay vì 500
class BookingConflictException extends \RuntimeException
{
}

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

        // Cắt ô giờ theo cấu hình khung giờ của chủ sân cho từng ngày (7 ngày tới)
        // Ngày nào chủ sân chưa cài khung giờ sẽ có danh sách ô rỗng → phía khách không hiện ô nào.
        $slotCells = [];
        foreach ($dates as $d) {
            $slotCells[$d['full_date']] = CourtSlot::buildTimeCells($court->slots, $d['full_date']);
        }

        // Chuẩn hóa định dạng booking_date (Y-m-d) và start_time/end_time (H:i)
        // để JS so sánh string không bị lệch do TIME trả về có giây (H:i:s)
        $existingBookings = Booking::where('court_id', $courtId)
            ->where('booking_date', '>=', Carbon::today()->toDateString())
            ->where('status', '!=', 'cancelled')
            ->get(['booking_date', 'start_time', 'end_time'])
            ->map(fn ($b) => [
                'booking_date' => $b->booking_date->toDateString(),
                'start_time'   => Carbon::parse($b->start_time)->format('H:i'),
                'end_time'     => Carbon::parse($b->end_time)->format('H:i'),
            ]);

        // Lịch khóa của sân (7 ngày tới) — để chặn hiển thị/đặt các khung giờ bị khóa
        $closures = CourtClosure::where('court_id', $courtId)
            ->whereDate('date', '>=', Carbon::today()->toDateString())
            ->whereDate('date', '<=', Carbon::today()->addDays(6)->toDateString())
            ->get(['date', 'start_time', 'end_time'])
            ->map(fn ($c) => [
                'date'       => $c->date->toDateString(),
                'start_time' => $c->start_time ? Carbon::parse($c->start_time)->format('H:i') : null,
                'end_time'   => $c->end_time ? Carbon::parse($c->end_time)->format('H:i') : null,
            ]);

        // Giờ hoạt động theo ngày trong tuần của khu sân — dùng để chặn ngày nghỉ khi đặt
        // Map thành mảng gọn (H:i, boolean) để JS so sánh không lệch do TIME trả về có giây
        $operatingHours = $court->venue->operatingHours
            ->map(fn ($h) => [
                'day_of_week' => (int) $h->day_of_week,
                'open_time'   => $h->open_time ? substr($h->open_time, 0, 5) : null,
                'close_time'  => $h->close_time ? substr($h->close_time, 0, 5) : null,
                'is_closed'   => (bool) $h->is_closed,
            ]);

        return view('customer.bookings.create', compact('court', 'dates', 'existingBookings', 'closures', 'slotCells', 'operatingHours'));
    }

    // 3. Xử lý đặt sân + Tính tiền theo các ô giờ được chủ sân cấu hình
    public function store(Request $request)
    {
        $request->validate([
            'court_id'     => 'required|exists:courts,id',
            'booking_date' => 'required|date|after_or_equal:today|before_or_equal:' . Carbon::today()->addDays(6)->toDateString(),
            'start_time'   => 'required',
            'end_time'     => 'required|after:start_time',
        ]);

        $court = $this->findBookableCourt($request->court_id);

        $bookingDate = Carbon::parse($request->booking_date);
        $dow         = $bookingDate->dayOfWeek; // 0 = CN ... 6 = Thứ 7 (đồng bộ operating_hours/court_slots)
        $startTime   = Carbon::parse($request->start_time);
        $endTime     = Carbon::parse($request->end_time);

        // Chặn đặt khung giờ đã qua của hôm nay
        if ($bookingDate->isToday() && $startTime->copy()->setDateFrom($bookingDate)->isPast()) {
            return back()->with('error', 'Không thể đặt khung giờ đã qua!');
        }

        // ─── Giờ hoạt động của khu sân trong ngày này ───
        $operatingHour = OperatingHour::where('venue_id', $court->venue_id)->where('day_of_week', $dow)->first();

        if (!$operatingHour || $operatingHour->is_closed) {
            return back()->with('error', 'Khu sân nghỉ ngày này, vui lòng chọn ngày khác!');
        }

        $openTime  = Carbon::parse($operatingHour->open_time);
        $closeTime = Carbon::parse($operatingHour->close_time);
        if ($startTime->lt($openTime) || $endTime->gt($closeTime)) {
            return back()->with('error', 'Thời gian đặt phải nằm trong giờ hoạt động (' . $openTime->format('H:i') . ' - ' . $closeTime->format('H:i') . ')!');
        }

        // ─── Cắt ô giờ theo cấu hình của chủ sân cho ngày đặt ───
        // (buildTimeCells đã ưu tiên slot gắn thứ cụ thể hơn slot "mọi ngày")
        $cells = CourtSlot::buildTimeCells(
            $court->slots,
            $request->booking_date
        );

        if (empty($cells)) {
            return back()->with('error', 'Sân này chưa mở bán khung giờ nào cho ngày đã chọn!');
        }

        // Khoảng giờ khách gửi phải khớp chính xác một dãy ô mở bán liền kề
        $requestedStart = substr($request->start_time, 0, 5);
        $requestedEnd   = substr($request->end_time, 0, 5);

        $selectedCells = [];
        $matching = true;
        foreach ($cells as $index => $cell) {
            if ($cell['start'] >= $requestedStart && $cell['end'] <= $requestedEnd) {
                if (!$cell['is_open']) {
                    $matching = false;
                    break;
                }
                // Ô đầu phải bắt đầu đúng requestedStart, ô sau liền kề ô trước
                $prevEnd = $selectedCells
                    ? end($selectedCells)['end']
                    : null;
                if ($prevEnd === null) {
                    $matching = ($cell['start'] === $requestedStart);
                } else {
                    $matching = ($cell['start'] === $prevEnd);
                }
                if (!$matching) {
                    break;
                }
                $selectedCells[] = $cell;
            }
        }

        if (
            !$matching || empty($selectedCells)
            || end($selectedCells)['end'] !== $requestedEnd
        ) {
            return back()->with('error', 'Khung giờ này không áp dụng cho sân, vui lòng chọn lại theo các ô giờ hiển thị!');
        }

        // Tổng tiền = cộng giá các ô giờ được chọn
        $totalAmount = array_sum(array_column($selectedCells, 'price'));

        // ─── Tạo đơn trong transaction + khóa dòng sân cha (chống đặt trùng khi 2 request song song) ───
        // Chuẩn hóa H:i:s để so khớp TIME khi so chuỗi (sqlite lưu verbatim, MySQL cast TIME)
        $startTimeSql = $startTime->format('H:i:s');
        $endTimeSql   = $endTime->format('H:i:s');

        try {
            $booking = DB::transaction(function () use ($court, $request, $startTime, $endTime, $totalAmount, $startTimeSql, $endTimeSql) {
                // Khóa dòng court: mọi request đặt sân này phải xếp hàng chờ nhau tại đây
                Court::whereKey($court->id)->lockForUpdate()->first();

                $isBlocked = CourtClosure::where('court_id', $court->id)
                    ->whereDate('date', $request->booking_date)
                    ->where(function ($query) use ($endTimeSql, $startTimeSql) {
                        // Khóa cả ngày (start_time null)
                        $query->whereNull('start_time')
                            // Khóa theo khung giờ: trùng lặp khoảng
                            ->orWhere(function ($q) use ($endTimeSql, $startTimeSql) {
                                $q->whereNotNull('start_time')
                                  ->where('start_time', '<', $endTimeSql)
                                  ->where('end_time', '>', $startTimeSql);
                            });
                    })
                    ->exists();

                if ($isBlocked) {
                    throw new BookingConflictException('Sân đang bị khóa lịch trong khoảng thời gian này, vui lòng chọn thời gian khác!');
                }

                $isBooked = Booking::where('court_id', $court->id)
                    ->whereDate('booking_date', $request->booking_date)
                    ->where('status', '!=', 'cancelled')
                    ->where('start_time', '<', $endTimeSql)
                    ->where('end_time', '>', $startTimeSql)
                    ->exists();

                if ($isBooked) {
                    throw new BookingConflictException('Khung giờ này đã có người đặt, vui lòng chọn giờ khác!');
                }

                $duration = $startTime->diffInMinutes($endTime);

                return Booking::create([
                    'code'           => 'BK' . strtoupper(Str::random(8)), // không có "-" vì nhiều ngân hàng xóa ký tự đặc biệt trong nội dung CK
                    'user_id'        => Auth::id(),
                    'court_id'       => $court->id,
                    'booking_date'   => $request->booking_date,
                    'start_time'     => $startTimeSql,
                    'end_time'       => $endTimeSql,
                    'duration'       => $duration,
                    'price_snapshot' => ($duration > 0) ? ($totalAmount / ($duration / 60)) : 0,
                    'total_amount'   => $totalAmount,
                    'payment_method' => 'full_online',
                    'status'         => 'pending',
                ]);
            });
        } catch (BookingConflictException $e) {
            return back()->with('error', $e->getMessage());
        } catch (UniqueConstraintViolationException) {
            // Trùng mã đơn (xác suất cực thấp) — cho khách đặt lại
            return back()->with('error', 'Đã xảy ra lỗi khi tạo đơn, vui lòng thử lại!');
        }

        return redirect()->route('customer.bookings.pay', $booking)
            ->with('success', 'Đặt sân thành công! Vui lòng chuyển khoản để hoàn tất.');
    }

    // 4. Trang thanh toán chuyển khoản (QR VietQR)
    public function pay(Booking $booking)
    {
        abort_unless($booking->user_id === Auth::id(), 403);
        $booking->load('court.venue');

        // Nếu đơn yêu cầu cọc, QR mặc định sinh ra cho số tiền cọc (khách vẫn có thể chuyển đủ tổng)
        $amount = $booking->deposit_amount ?? $booking->total_amount;

        $qrUrl = 'https://img.vietqr.io/image/'
            . config('services.vietqr.bank_id') . '-' . config('services.vietqr.account_no') . '-' . config('services.vietqr.template')
            . '.png?amount=' . (int) $amount
            . '&addInfo=' . urlencode($booking->code)
            . '&accountName=' . urlencode(config('services.vietqr.account_name'));

        return view('customer.bookings.pay', compact('booking', 'qrUrl', 'amount'));
    }

    // 5. API nhỏ cho polling trạng thái thanh toán trên trang pay
    public function status(Booking $booking)
    {
        abort_unless($booking->user_id === Auth::id(), 403);

        return response()->json([
            'payment_status' => $booking->payment_status,
            'status'         => $booking->status,
        ]);
    }

    /**
     * Lấy sân có thể đặt: phải tồn tại, đang hoạt động, và thuộc khu sân đã được duyệt.
     */
    private function findBookableCourt($courtId)
    {
        $court = Court::with(['venue', 'sport', 'slots'])
            ->whereHas('venue', fn ($q) => $q->where('status', 'active'))
            ->findOrFail($courtId);

        abort_if($court->status !== 'active', 403, 'Sân này hiện không nhận đặt (bảo trì/đóng cửa).');

        return $court;
    }
}