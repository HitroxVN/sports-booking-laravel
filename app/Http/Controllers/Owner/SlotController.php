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
    public function store(Request $request, Court $court)
    {
        $this->authorizeCourt($court);

        $validated = $request->validate([
            'day_of_week' => 'required|integer|between:0,6', // 0 = Chủ nhật, 1-6 = Thứ 2 đến Thứ 7
            'start_time'  => 'required|date_format:H:i',
            'end_time'    => 'required|date_format:H:i|after:start_time',
            'price'       => 'required|numeric|min:0',
            'is_peak'     => 'boolean',
            'peak_price'  => 'nullable|numeric|min:0|required_if:is_peak,1',
        ]);

        $court->slots()->create($validated);

        return redirect()->route('owner.courts.slots.index', $court)
            ->with('success', 'Đã thêm khung giờ mới thành công!');
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