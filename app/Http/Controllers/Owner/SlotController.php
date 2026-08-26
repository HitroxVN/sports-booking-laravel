<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Court;
use App\Models\CourtSlot;
use Illuminate\Http\Request;

class SlotController extends Controller
{
    /**
     * Danh sách khung giờ của một sân con.
     */
    public function index(Court $court)
    {
        $this->authorizeCourt($court);

        // Lấy danh sách khung giờ, sắp xếp theo thứ trong tuần và giờ bắt đầu
        $slots = $court->slots()
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get();

        return view('owner.slots.index', compact('court', 'slots'));
    }

    /**
     * Form thêm khung giờ mới.
     */
    public function create(Court $court)
    {
        $this->authorizeCourt($court);

        return view('owner.slots.create', compact('court'));
    }

    /**
     * Lưu khung giờ mới.
     */
    public function store(Request $request, \App\Models\Court $court)
    {
        // Xử lý chuyển đổi checkbox is_peak thành boolean
        $request->merge([
            'is_peak' => $request->boolean('is_peak')
        ]);

        // 1. Validate định dạng cơ bản kèm theo quy tắc Giờ Vàng
        $validated = $request->validate([
            'day_of_week' => 'required|integer|between:0,6',
            'start_time'  => 'required|date_format:H:i',
            'end_time'    => 'required|date_format:H:i|after:start_time',
            'price'       => 'required|numeric|min:0',
            'is_peak'     => 'boolean',
            'peak_price'  => 'nullable|numeric|min:0|required_if:is_peak,1',
        ], [
            'peak_price.required_if' => 'Vui lòng nhập mức giá giờ vàng khi bật tính năng này.',
        ]);

        // 2. THUẬT TOÁN CHẶN CHỒNG GIỜ (OVERLAP)
        $isOverlapping = $court->slots()
            ->where('day_of_week', $validated['day_of_week'])
            ->where(function ($query) use ($validated) {
                $query->where('start_time', '<', $validated['end_time'])
                      ->where('end_time', '>', $validated['start_time']);
            })
            ->exists();

        if ($isOverlapping) {
            return back()
                ->withErrors(['start_time' => 'Khung giờ này bị trùng hoặc lồng ghép với một khung giờ đã tồn tại trong cùng ngày!'])
                ->withInput();
        }

        // 3. Lưu vào Database (bao gồm cả giờ vàng)
        $court->slots()->create($validated);

        return redirect()->route('owner.courts.slots.index', $court)
                         ->with('success', 'Đã thêm khung giờ thành công!');
    }
    /**
     * Xóa khung giờ.
     */
    public function destroy(CourtSlot $slot)
    {
        $court = $slot->court;
        $this->authorizeCourt($court);

        $slot->delete();

        return redirect()->route('owner.courts.slots.index', $court)
            ->with('success', 'Đã xóa khung giờ thành công!');
    }

    /**
     * Kiểm tra quyền sở hữu của chủ sân.
     */
    private function authorizeCourt(Court $court)
    {
        abort_if(
            $court->venue->owner_id !== auth()->id(), 
            403, 
            'Bạn không có quyền thao tác trên sân này.'
        );
    }
}