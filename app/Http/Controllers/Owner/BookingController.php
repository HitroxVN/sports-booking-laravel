<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Notifications\BookingCancelled;
use App\Notifications\BookingConfirmed;
use App\Services\Notifier;
use Illuminate\Http\Request;

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