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

        // 1. Validate (giờ chẵn) + 2. chặn trùng giờ
        $validated = $this->validateSlot($request);

        if ($this->overlapExists($validated, $court)) {
            return back()
                ->withErrors(['start_time' => 'Khung giờ này bị trùng hoặc lồng ghép với một khung giờ đã tồn tại trong cùng ngày/áp dụng chung!'])
                ->withInput();
        }

        // 3. Lưu vào Database
        $court->slots()->create($validated);

        return redirect()->route('owner.courts.slots.index', $court)
                         ->with('success', 'Đã thêm khung giờ thành công!');
    }

    /**
     * Form sửa khung giờ.
     */
    public function edit(CourtSlot $slot)
    {
        $this->authorizeCourt($slot->court);

        return view('owner.slots.edit', ['slot' => $slot, 'court' => $slot->court]);
    }

    /**
     * Cập nhật khung giờ — dùng chung validate + chặn trùng giờ với store().
     */
    public function update(Request $request, CourtSlot $slot)
    {
        $this->authorizeCourt($slot->court);

        $validated = $this->validateSlot($request);

        // Chặn trùng giờ: bỏ qua chính slot đang sửa khi so khớp
        $isOverlapping = $this->overlapExists($validated, $slot->court, $slot->id);

        if ($isOverlapping) {
            return back()
                ->withErrors(['start_time' => 'Khung giờ này bị trùng hoặc lồng ghép với một khung giờ đã tồn tại trong cùng ngày/áp dụng chung!'])
                ->withInput();
        }

        $slot->update($validated);

        return redirect()->route('owner.courts.slots.index', $slot->court)
                         ->with('success', 'Đã cập nhật khung giờ thành công!');
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
     * Validate chung cho store/update: giờ phải chẵn giờ (phút = 00).
     */
    private function validateSlot(Request $request): array
    {
        // Xử lý chuyển đổi checkbox is_peak thành boolean
        $request->merge([
            'is_peak' => $request->boolean('is_peak')
        ]);

        $validated = $request->validate([
            'day_of_week' => 'nullable|integer|between:0,6',
            // Ép giờ chẵn: phút phải = 00 (lưới đặt sân cắt ô theo giờ chẵn)
            'start_time'  => ['required', 'date_format:H:i', fn ($attr, $value, $fail) => self::wholeHour($value, $fail)],
            'end_time'    => ['required', 'date_format:H:i', 'after:start_time', fn ($attr, $value, $fail) => self::wholeHour($value, $fail)],
            'price'       => 'required|numeric|min:0',
            'is_peak'     => 'boolean',
            'peak_price'  => 'nullable|numeric|min:0|required_if:is_peak,1',
        ], [
            'peak_price.required_if' => 'Vui lòng nhập mức giá giờ vàng khi bật tính năng này.',
            'start_time.date_format' => 'Giờ bắt đầu không hợp lệ.',
            'end_time.date_format'   => 'Giờ kết thúc không hợp lệ.',
        ]);

        return $validated;
    }

    // Rule giờ chẵn (phút = 00)
    private static function wholeHour(string $value, $fail): void
    {
        if (substr($value, 3) !== '00') {
            $fail('Giờ phải là giờ chẵn (VD: 06:00, 19:00) — không nhận phút lẻ.');
        }
    }

    /**
     * Khoảng giờ mới có trùng slot nào của sân không (trừ chính slot khi đang sửa).
     */
    private function overlapExists(array $validated, Court $court, ?int $ignoreId = null): bool
    {
        $overlapQuery = $court->slots()
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->where(function ($query) use ($validated) {
                $query->where('start_time', '<', $validated['end_time'])
                      ->where('end_time', '>', $validated['start_time']);
            });

        // Nếu có chọn thứ cụ thể thì kiểm tra trùng theo thứ đó; nếu áp dụng chung (null) thì kiểm tra các slot chung
        if (is_null($validated['day_of_week'])) {
            $overlapQuery->whereNull('day_of_week');
        } else {
            $overlapQuery->where('day_of_week', $validated['day_of_week']);
        }

        return $overlapQuery->exists();
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