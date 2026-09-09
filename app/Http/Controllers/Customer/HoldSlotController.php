<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\CourtHold;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class HoldSlotController extends Controller
{
    // 1. API Lưu trạng thái giữ chỗ (User click chọn giờ)
    public function lockSlot(Request $request)
    {
        $request->validate([
            'court_id'     => 'required|exists:courts,id',
            'booking_date' => 'required|date',
            'start_time'   => 'required',
            'end_time'     => 'required'
        ]);

        // Xóa các khoảng giữ chỗ đã quá 10 phút của tất cả mọi người cho sạch DB
        CourtHold::where('expires_at', '<', now())->delete();

        // Kiểm tra xem giờ này có đang bị người KHÁC giữ không
        $isHeldByOthers = CourtHold::where('court_id', $request->court_id)
            ->where('booking_date', $request->booking_date)
            ->where('user_id', '!=', Auth::id())
            ->where(function ($query) use ($request) {
                $query->where('start_time', '<', $request->end_time)
                      ->where('end_time', '>', $request->start_time);
            })->exists();

        if ($isHeldByOthers) {
            return response()->json([
                'success' => false, 
                'message' => 'Rất tiếc, khung giờ này vừa có người khác chọn. Vui lòng chọn giờ khác!'
            ]);
        }

        // Xóa các chỗ đang giữ CŨ của chính User này (tránh 1 người click giữ nhiều chỗ)
        CourtHold::where('user_id', Auth::id())->delete();

        // Lưu giữ chỗ mới với thời hạn 10 phút
        CourtHold::create([
            'court_id'     => $request->court_id,
            'user_id'      => Auth::id(),
            'booking_date' => $request->booking_date,
            'start_time'   => $request->start_time,
            'end_time'     => $request->end_time,
            'expires_at'   => now()->addMinutes(10),
        ]);

        return response()->json(['success' => true]);
    }

    // 2. API Lấy danh sách sân đang bị giữ (Frontend gọi ngầm liên tục)
    public function getActiveHolds($court_id, Request $request)
    {
        $date = $request->query('date', now()->toDateString());

        $holds = CourtHold::where('court_id', $court_id)
            ->where('booking_date', $date)
            ->where('expires_at', '>', now()) // Chỉ lấy hold còn hạn
            ->where('user_id', '!=', Auth::id()) // Bỏ qua hold của chính mình, để mình vẫn click được
            ->get(['start_time', 'end_time']);

        return response()->json($holds);
    }
}