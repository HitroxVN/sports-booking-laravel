<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Booking;
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
     * Cập nhật trạng thái đơn (Xác nhận, Hủy, Hoàn thành).
     */
    public function update(Request $request, Booking $booking)
    {
        $this->authorizeBooking($booking);

        $validated = $request->validate([
            'status'        => 'required|in:pending,confirmed,completed,cancelled',
            'cancel_reason' => 'nullable|string|required_if:status,cancelled',
        ]);

        $booking->status = $validated['status'];
        
        // Nếu hủy thì lưu thời gian hủy và lý do
        if ($validated['status'] === 'cancelled') {
            $booking->cancelled_at = now();
            $booking->cancel_reason = $request->cancel_reason;
        }

        $booking->save();

        return back()->with('success', 'Đã cập nhật trạng thái đơn đặt sân thành công!');
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