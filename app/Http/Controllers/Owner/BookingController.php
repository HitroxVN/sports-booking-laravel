<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Notifications\BookingCancelled;
use App\Notifications\BookingConfirmed;
use App\Services\Notifier;
use App\Models\Court;
use App\Models\CourtClosure;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class BookingController extends Controller
{
    /**
     * Danh sách đơn đặt sân.
     */
    public function index(Request $request)
    {
        // Chỉ lấy những đơn đặt sân thuộc về khu sân của chủ sân đang đăng nhập
        $query = Booking::with(['user', 'court.venue'])
            ->whereHas('court.venue', function ($q) {
                $q->where('owner_id', auth()->id());
            });

        // Hỗ trợ lọc theo trạng thái (nếu có trên url ?status=...)
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $bookings = $query->latest()->paginate(15);

        return view('owner.bookings.index', compact('bookings'));
    }

    /**
     * Xem chi tiết 1 đơn đặt sân.
     */
    public function show(Booking $booking)
    {
        $this->authorizeBooking($booking);

        $booking->load(['user', 'court.venue', 'payments']);

        return view('owner.bookings.show', compact('booking'));
    }

    /**
     * Cập nhật trạng thái đơn đặt sân.
     */
    public function update(Request $request, \App\Models\Booking $booking)
    {
        $this->authorizeBooking($booking);

        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,completed,cancelled',
            // Bắt buộc nhập lý do nếu chọn trạng thái Hủy
            'cancel_reason' => 'required_if:status,cancelled|nullable|string|max:255',
        ], [
            'cancel_reason.required_if' => 'Vui lòng nhập lý do hủy đơn.',
        ]);

        $newStatus = $validated['status'];

        // Chặn các chuyển trạng thái không hợp lệ (chỉ tiến: pending → confirmed → completed, bất kỳ → cancelled)
        $allowedTransitions = [
            'pending'   => ['confirmed', 'cancelled'],
            'confirmed' => ['completed', 'cancelled'],
            'completed' => [],
            'cancelled' => [],
        ];
        if (! in_array($newStatus, $allowedTransitions[$booking->status] ?? [], true)) {
            return back()->with('error', "Không thể chuyển trạng thái từ \"{$booking->status}\" sang \"{$newStatus}\".");
        }

        // Nếu là hủy đơn, tự động lưu thời gian hủy + hoàn tiền nếu khách đã trả
        $refundedAmount = 0.0;
        if ($newStatus === 'cancelled') {
            $validated['cancelled_at'] = now();

            if (\App\Services\BookingRefund::refund($booking, $validated['cancel_reason'] ?? 'Chủ sân hủy đơn')) {
                // Không return sớm: đơn vẫn phải được chuyển sang cancelled ở dưới
                $refundedAmount = (float) $booking->payments()->where('type', 'refund')->sum('amount');
            }
        } else {
            // Nếu chuyển trạng thái khác, xóa lý do hủy cũ đi (nếu có)
            $validated['cancel_reason'] = null;
            $validated['cancelled_at'] = null;
        }

        $booking->update($validated);

        // Thông báo cho khách về trạng thái mới
        if ($newStatus === 'confirmed') {
            Notifier::send($booking->user, new BookingConfirmed($booking));
        } elseif ($newStatus === 'cancelled') {
            Notifier::send($booking->user, new BookingCancelled($booking));
        }

        return back()->with('success', $refundedAmount > 0
            ? 'Đã hủy đơn và ghi nhận hoàn tiền ' . number_format($refundedAmount, 0, ',', '.') . 'đ cho khách.'
            : 'Cập nhật trạng thái đơn đặt sân thành công!');
    }

    /**
     * Tạo nhanh đơn đặt sân cho khách vãng lai / gọi điện.
     */
    public function quickStore(Request $request)
    {
        $validated = $request->validate([
            'court_id'        => 'required|exists:courts,id',
            'booking_date'    => 'required|date',
            'start_time'      => 'required',
            'end_time'        => 'required|after:start_time',
            'customer_name'   => 'nullable|string|max:255',
            'customer_phone'  => 'nullable|string|max:20',
            'total_amount'    => 'nullable|numeric|min:0',
            'status'          => 'nullable|in:pending,confirmed',
            'payment_method'  => 'nullable|in:at_venue,partial_online,full_online',
            'payment_status'  => 'nullable|in:unpaid,deposit_paid,fully_paid',
            'notes'           => 'nullable|string|max:1000',
        ], [
            'court_id.required'     => 'Vui lòng chọn sân.',
            'court_id.exists'       => 'Sân đã chọn không hợp lệ.',
            'booking_date.required' => 'Vui lòng chọn ngày đặt sân.',
            'start_time.required'   => 'Vui lòng chọn giờ bắt đầu.',
            'end_time.required'     => 'Vui lòng chọn giờ kết thúc.',
            'end_time.after'        => 'Giờ kết thúc phải sau giờ bắt đầu.',
        ]);

        $court = Court::with('venue')->findOrFail($validated['court_id']);

        // Kiểm tra quyền sở hữu sân
        abort_if(
            $court->venue->owner_id !== auth()->id(),
            403,
            'Bạn không có quyền tạo đơn cho sân này.'
        );

        $startTime    = Carbon::parse($validated['start_time']);
        $endTime      = Carbon::parse($validated['end_time']);
        $startTimeSql = $startTime->format('H:i:s');
        $endTimeSql   = $endTime->format('H:i:s');
        $duration     = $startTime->diffInMinutes($endTime);

        // Kiểm tra trùng lịch đặt sân (loại trừ các đơn đã hủy)
        $hasConflict = Booking::where('court_id', $court->id)
            ->whereDate('booking_date', $validated['booking_date'])
            ->where('status', '!=', 'cancelled')
            ->where('start_time', '<', $endTimeSql)
            ->where('end_time', '>', $startTimeSql)
            ->exists();

        if ($hasConflict) {
            return back()->with('error', 'Khung giờ này đã có đơn đặt trên sân này, vui lòng chọn giờ khác!');
        }

        // Kiểm tra khóa sân bảo trì
        $isClosed = CourtClosure::where('court_id', $court->id)
            ->whereDate('date', $validated['booking_date'])
            ->where(function ($query) use ($endTimeSql, $startTimeSql) {
                $query->whereNull('start_time')
                    ->orWhere(function ($q) use ($endTimeSql, $startTimeSql) {
                        $q->whereNotNull('start_time')
                          ->where('start_time', '<', $endTimeSql)
                          ->where('end_time', '>', $startTimeSql);
                    });
            })
            ->exists();

        if ($isClosed) {
            return back()->with('error', 'Sân đang có lịch khóa bảo trì trong khung giờ này!');
        }

        // Xử lý thông tin khách hàng (Customer)
        $customerUser = null;
        if (!empty($validated['customer_phone'])) {
            $customerUser = User::where('phone', $validated['customer_phone'])->first();
        }

        if (!$customerUser && !empty($validated['customer_name'])) {
            $cleanPhone = preg_replace('/[^0-9]/', '', $validated['customer_phone'] ?? '');
            $email = $cleanPhone ? "guest_{$cleanPhone}@arena.local" : "guest_" . Str::random(8) . "@arena.local";
            
            $existingWithEmail = User::where('email', $email)->first();
            if ($existingWithEmail) {
                $customerUser = $existingWithEmail;
            } else {
                $customerUser = User::create([
                    'name'     => $validated['customer_name'],
                    'phone'    => $validated['customer_phone'] ?? null,
                    'email'    => $email,
                    'role'     => 'customer',
                    'status'   => 'active',
                    'password' => Hash::make(Str::random(16)),
                ]);
            }
        }

        if (!$customerUser) {
            $customerUser = User::firstOrCreate(
                ['email' => 'guest@arena.local'],
                [
                    'name'     => 'Khách vãng lai',
                    'role'     => 'customer',
                    'status'   => 'active',
                    'password' => Hash::make(Str::random(16)),
                ]
            );
        }

        // Tính tiền nếu không nhập
        $totalAmount = $validated['total_amount'] ?? null;
        if ($totalAmount === null || $totalAmount === '') {
            $slotPrice = $court->slots()->value('price');
            $hourlyPrice = $slotPrice ? (float) $slotPrice : 100000;
            $totalAmount = round(($duration / 60) * $hourlyPrice);
        }

        $status = $validated['status'] ?? 'confirmed';
        $paymentMethod = $validated['payment_method'] ?? 'at_venue';
        $paymentStatus = $validated['payment_status'] ?? 'unpaid';

        $notes = $validated['notes'] ?? null;
        if (!empty($validated['customer_name']) || !empty($validated['customer_phone'])) {
            $customerInfo = 'Khách: ' . ($validated['customer_name'] ?? 'Khách lẻ') . ($validated['customer_phone'] ? ' (' . $validated['customer_phone'] . ')' : '');
            $notes = $notes ? ($customerInfo . ' - ' . $notes) : $customerInfo;
        }

        $booking = Booking::create([
            'code'           => 'BK' . strtoupper(Str::random(8)),
            'user_id'        => $customerUser->id,
            'court_id'       => $court->id,
            'booking_date'   => $validated['booking_date'],
            'start_time'     => $startTimeSql,
            'end_time'       => $endTimeSql,
            'duration'       => $duration,
            'price_snapshot' => $duration > 0 ? ($totalAmount / ($duration / 60)) : 0,
            'total_amount'   => $totalAmount,
            'status'         => $status,
            'payment_method' => $paymentMethod,
            'payment_status' => $paymentStatus,
            'notes'          => $notes,
        ]);

        return back()->with('success', "Tạo đơn đặt sân nhanh thành công! Mã đơn: #{$booking->code}");
    }

    /**
     * Kiểm tra bảo mật: Đơn này có thuộc về chủ sân không?
     */
    private function authorizeBooking(Booking $booking)
    {
        abort_if(
            $booking->court->venue->owner_id !== auth()->id(), 
            403, 
            'Bạn không có quyền truy cập đơn đặt sân này.'
        );
    }
}