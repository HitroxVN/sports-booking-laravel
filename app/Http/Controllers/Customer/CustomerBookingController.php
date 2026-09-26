<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BookingSeries;
use App\Models\Court;
use App\Models\CourtClosure;
use App\Models\CourtSlot;
use App\Models\OperatingHour;
use App\Models\Promotion;
use App\Models\Review;
use App\Notifications\BookingCreated;
use App\Notifications\NewBookingForOwner;
use App\Services\Notifier;
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
    // Số tuần tối đa của một lịch cố định
    public const MAX_REPEAT_WEEKS = 12;

    // 1. Hiển thị lịch sử đặt sân của tôi (/my-bookings)
    public function index()
    {
        $bookings = Booking::with(['court.venue', 'review'])
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        return view('customer.bookings.index', compact('bookings'));
    }

    // 1.1. Chi tiết đơn của khách (click mã đơn ở lịch sử)
    public function show(Booking $booking)
    {
        abort_unless($booking->user_id === Auth::id(), 403);
        $booking->load(['court.venue', 'review', 'promotion', 'payments']);

        return view('customer.bookings.show', compact('booking'));
    }

    // 1.2. Lưu đánh giá sau khi chơi xong (đơn đã hoàn tất)
    public function storeReview(Request $request, Booking $booking)
    {
        abort_unless($booking->user_id === Auth::id(), 403);

        // Chỉ đơn completed mới được đánh giá, mỗi đơn 1 review duy nhất
        if (! $booking->isCompleted()) {
            return back()->with('error', 'Chỉ đơn đã hoàn tất mới có thể đánh giá!');
        }
        if ($booking->review) {
            return back()->with('error', 'Bạn đã đánh giá đơn này rồi!');
        }

        $validated = $request->validate([
            'rating'  => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ], [
            'rating.required' => 'Vui lòng chọn số sao đánh giá!',
        ]);

        Review::create([
            'booking_id' => $booking->id,
            'user_id'    => Auth::id(),
            'venue_id'   => $booking->court->venue_id,
            'rating'     => $validated['rating'],
            'comment'    => $validated['comment'] ?? null,
        ]);

        return back()->with('success', 'Cảm ơn bạn đã đánh giá khu sân!');
    }

    // 2. Hiển thị sơ đồ chọn giờ đặt sân
    public function create($courtId)
    {
        $court = $this->findBookableCourt($courtId);

        // Cửa sổ đặt sân: từ hôm nay đến hết tháng sau (tháng này + tháng sau)
        $windowEnd = Carbon::today()->addMonthNoOverflow()->endOfMonth();
        $maxDate   = $windowEnd->toDateString();

        $dates = [];
        for ($date = Carbon::today(); $date->lte($windowEnd); $date->addDay()) {
            $dates[] = [
                'full_date'  => $date->format('Y-m-d'),
                'day_name'   => $date->locale('vi')->dayName,
                'formatted'  => $date->format('d/m'),
            ];
        }

        // Cắt ô giờ theo cấu hình khung giờ của chủ sân cho từng ngày (từ hôm nay đến hết tháng sau)
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

        // Lịch khóa của sân (cùng cửa sổ đặt: đến hết tháng sau) — để chặn hiển thị/đặt các khung giờ bị khóa
        $closures = CourtClosure::where('court_id', $courtId)
            ->whereDate('date', '>=', Carbon::today()->toDateString())
            ->whereDate('date', '<=', $windowEnd->toDateString())
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

        // Khu sân có cài giờ hoạt động không — chưa cài thì UI không chặn theo giờ hoạt động
        $venueHasOperatingHours = $operatingHours->isNotEmpty();

        // Mã giảm giá đang chạy của khu sân — JS dùng để ước lượng giá sau giảm
        $promotions = Promotion::where('venue_id', $court->venue_id)
            ->where('is_active', true)
            ->where('starts_at', '<=', now())
            ->where('expires_at', '>=', now())
            ->get(['code', 'discount_type', 'discount_value', 'min_amount'])
            ->keyBy(fn ($p) => strtoupper($p->code));

        return view('customer.bookings.create', compact('court', 'dates', 'maxDate', 'existingBookings', 'closures', 'slotCells', 'operatingHours', 'venueHasOperatingHours', 'promotions'));
    }

    // 3. Xử lý đặt sân + Tính tiền theo các ô giờ được chủ sân cấu hình
    public function store(Request $request)
    {
        // Cửa sổ đặt sân: từ hôm nay đến hết tháng sau
        $windowEnd = Carbon::today()->addMonthNoOverflow()->endOfMonth();

        $request->validate([
            'court_id'     => 'required|exists:courts,id',
            'booking_date' => 'required|date|after_or_equal:today|before_or_equal:' . $windowEnd->toDateString(),
            'start_time'   => 'required',
            'end_time'     => 'required|after:start_time',
            // Số tuần lặp của lịch cố định (1 hoặc rỗng = đặt lẻ 1 buổi)
            'repeat_weeks' => 'nullable|integer|min:1|max:' . self::MAX_REPEAT_WEEKS,
        ], [
            'repeat_weeks.max' => 'Chỉ đặt lịch cố định tối đa ' . self::MAX_REPEAT_WEEKS . ' tuần một lần.',
        ]);

        $court = $this->findBookableCourt($request->court_id);

        $bookingDate = Carbon::parse($request->booking_date);
        $dow         = $bookingDate->dayOfWeek; // 0 = CN ... 6 = Thứ 7 (đồng bộ operating_hours/court_slots)
        $startTime   = Carbon::parse($request->start_time);
        $endTime     = Carbon::parse($request->end_time);

        // ─── Lịch cố định: danh sách ngày của chuỗi (mỗi tuần 1 buổi, cùng thứ) ───
        $repeatWeeks = (int) ($request->input('repeat_weeks') ?: 1);
        $isSeries    = $repeatWeeks > 1;

        $dates = [];
        for ($i = 0; $i < $repeatWeeks; $i++) {
            $dates[] = $bookingDate->copy()->addWeeks($i);
        }

        // Buổi cuối của chuỗi phải còn nằm trong cửa sổ đặt sân
        if (end($dates)->gt($windowEnd)) {
            return back()->with('error', 'Lịch cố định vượt quá thời gian cho phép đặt (hết ngày ' . $windowEnd->format('d/m/Y') . '). Vui lòng giảm số tuần!');
        }

        // Chặn đặt khung giờ đã qua của hôm nay
        if ($bookingDate->isToday() && $startTime->copy()->setDateFrom($bookingDate)->isPast()) {
            return back()->with('error', 'Không thể đặt khung giờ đã qua!');
        }

        // ─── Giờ hoạt động của khu sân trong ngày này ───
        // Khu sân chưa cài giờ hoạt động nào ≠ "nghỉ" — chỉ chặn khi ĐÃ cài mà ngày đó nghỉ/ngoài giờ
        $hasOperatingHours = OperatingHour::where('venue_id', $court->venue_id)->exists();

        if ($hasOperatingHours) {
            $operatingHour = OperatingHour::where('venue_id', $court->venue_id)->where('day_of_week', $dow)->first();

            if (!$operatingHour || $operatingHour->is_closed) {
                return back()->with('error', 'Khu sân nghỉ ngày này, vui lòng chọn ngày khác!');
            }

            $openTime  = Carbon::parse($operatingHour->open_time);
            $closeTime = Carbon::parse($operatingHour->close_time);
            if ($startTime->lt($openTime) || $endTime->gt($closeTime)) {
                return back()->with('error', 'Thời gian đặt phải nằm trong giờ hoạt động (' . $openTime->format('H:i') . ' - ' . $closeTime->format('H:i') . ')!');
            }
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

        // Tổng tiền trước giảm = cộng giá các ô giờ được chọn
        $totalAmount = array_sum(array_column($selectedCells, 'price'));

        // ─── Mã giảm giá (nếu có) ───
        $promotion   = null;
        $discountAmount = 0;

        if ($request->filled('promotion_code')) {
            $promotion = Promotion::where('code', strtoupper(trim($request->input('promotion_code'))))
                ->where('venue_id', $court->venue_id)
                ->first();

            if (!$promotion || !$promotion->isValid()
                || ($promotion->min_amount !== null && $totalAmount < $promotion->min_amount)) {
                return back()->with('error', 'Mã giảm giá không hợp lệ hoặc không áp dụng cho đơn này!');
            }
            $discountAmount = $promotion->discount_type === 'percent'
                ? round($totalAmount * (float) $promotion->discount_value / 100)
                : min((float) $promotion->discount_value, $totalAmount);

            // Mã phải còn đủ lượt cho toàn bộ số buổi sắp tạo
            if ($promotion->max_uses !== null && $promotion->used_count + $repeatWeeks > $promotion->max_uses) {
                return back()->with('error', 'Mã giảm giá không còn đủ lượt cho ' . $repeatWeeks . ' buổi!');
            }
        }
        // Chuẩn hóa H:i:s để so khớp TIME khi so chuỗi (sqlite lưu verbatim, MySQL cast TIME)
        $startTimeSql = $startTime->format('H:i:s');
        $endTimeSql   = $endTime->format('H:i:s');

        $duration = $startTime->diffInMinutes($endTime);

        // Đơn lẻ giữ luồng thanh toán online như cũ; lịch cố định chốt sân luôn và thanh toán tại sân mỗi buổi
        $paymentMethod = $isSeries ? 'at_venue' : 'full_online';
        $initialStatus = $isSeries ? 'confirmed' : 'pending';

        try {
            $result = DB::transaction(function () use ($court, $dates, $isSeries, $repeatWeeks, $dow, $duration, $totalAmount, $startTimeSql, $endTimeSql, $promotion, $discountAmount, $paymentMethod, $initialStatus) {
                // Khóa dòng court: mọi request đặt sân này phải xếp hàng chờ nhau tại đây
                Court::whereKey($court->id)->lockForUpdate()->first();

                $series = null;
                if ($isSeries) {
                    $series = BookingSeries::create([
                        'user_id'        => Auth::id(),
                        'court_id'       => $court->id,
                        'weekday'        => $dow,
                        'start_time'     => $startTimeSql,
                        'end_time'       => $endTimeSql,
                        'duration'       => $duration,
                        'price_snapshot' => ($duration > 0) ? ($totalAmount / ($duration / 60)) : 0,
                        'weeks'          => $repeatWeeks,
                        'starts_on'      => $dates[0]->toDateString(),
                    ]);
                }

                $bookings = [];
                foreach ($dates as $date) {
                    $dateStr = $date->toDateString();
                    // Lỗi ở buổi nào thì nói rõ buổi đó (chỉ khi là chuỗi)
                    $prefix = $isSeries ? 'Buổi ' . $date->format('d/m') . ': ' : '';

                    $isBlocked = CourtClosure::where('court_id', $court->id)
                        ->whereDate('date', $dateStr)
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
                        throw new BookingConflictException($prefix . 'Sân đang bị khóa lịch trong khoảng thời gian này, vui lòng chọn thời gian khác!');
                    }

                    // Đơn pending quá hạn coi như hết hạn (command app:cancel-expired-pending-bookings
                    // sẽ hủy) — không tính là chiếm slot để người khác đặt được ngay.
                    // Đơn cancelled không chiếm slot.
                    $isBooked = Booking::where('court_id', $court->id)
                        ->whereDate('booking_date', $dateStr)
                        ->where(function ($q) {
                            $q->whereIn('status', ['confirmed', 'completed'])
                                ->orWhere(function ($q2) {
                                    $q2->where('status', 'pending')
                                        ->where('created_at', '>=', now()->subMinutes(Booking::PAYMENT_EXPIRY_MINUTES));
                                });
                        })
                        ->where('start_time', '<', $endTimeSql)
                        ->where('end_time', '>', $startTimeSql)
                        ->exists();

                    if ($isBooked) {
                        throw new BookingConflictException($prefix . 'Khung giờ này đã có người đặt, vui lòng chọn giờ khác!');
                    }

                    $bookings[] = Booking::create([
                        'code'           => 'BK' . strtoupper(Str::random(8)), // không có "-" vì nhiều ngân hàng xóa ký tự đặc biệt trong nội dung CK
                        'user_id'        => Auth::id(),
                        'court_id'       => $court->id,
                        'series_id'      => $series?->id,
                        'booking_date'   => $dateStr,
                        'start_time'     => $startTimeSql,
                        'end_time'       => $endTimeSql,
                        'duration'       => $duration,
                        'price_snapshot' => ($duration > 0) ? ($totalAmount / ($duration / 60)) : 0,
                        'total_amount'   => $totalAmount - $discountAmount,
                        'promotion_id'   => $promotion?->id,
                        'discount_amount'=> $discountAmount,
                        'payment_method' => $paymentMethod,
                        'status'         => $initialStatus,
                    ]);

                    // Mã giảm giá trừ 1 lượt cho MỖI buổi tạo thành công (increment atomic trong transaction)
                    if ($promotion) {
                        $promotion->increment('used_count');
                    }
                }

                return ['series' => $series, 'bookings' => $bookings];
            });
        } catch (BookingConflictException $e) {
            return back()->with('error', $e->getMessage());
        } catch (UniqueConstraintViolationException) {
            // Trùng mã đơn (xác suất cực thấp) — cho khách đặt lại
            return back()->with('error', 'Đã xảy ra lỗi khi tạo đơn, vui lòng thử lại!');
        }

        // Thông báo SAU khi transaction đã commit — tránh báo cho đơn bị rollback.
        // Lịch cố định chỉ gửi 1 thông báo tóm tắt, không phải mỗi buổi một cái.
        $first    = $result['bookings'][0];
        $sessions = count($result['bookings']);
        $first->load(['user', 'court.venue.owner']);

        Notifier::send($first->user, new BookingCreated($first, $sessions));
        Notifier::send($first->court?->venue?->owner, new NewBookingForOwner($first, $sessions));

        // Lịch cố định: cả chuỗi đã tạo xong → về danh sách đơn của khách
        if ($isSeries) {
            return redirect()->route('customer.bookings.index')
                ->with('success', 'Đã đặt lịch cố định ' . $sessions . ' buổi hàng tuần. Vui lòng thanh toán tại sân mỗi buổi.');
        }

        return redirect()->route('customer.bookings.pay', $first)
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