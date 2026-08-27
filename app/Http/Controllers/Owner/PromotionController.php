<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Promotion;
use App\Models\Venue;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PromotionController extends Controller
{
    public function index(Venue $venue)
    {
        $this->authorizeVenue($venue);
        $promotions = $venue->promotions()->latest()->paginate(10);
        return view('owner.promotions.index', compact('venue', 'promotions'));
    }

    public function create(Venue $venue)
    {
        $this->authorizeVenue($venue);
        return view('owner.promotions.create', compact('venue'));
    }

    public function store(Request $request, Venue $venue)
    {
        $this->authorizeVenue($venue);

        $request->merge(['code' => strtoupper($request->input('code') ?? '')]);

        $validated = $request->validate([
            // Khắc phục lỗi SQL 1062: Đảm bảo mã unique trên toàn bộ bảng
            'code'           => ['required', 'string', 'max:50', 'alpha_dash', Rule::unique('promotions', 'code')],
            'description'    => 'nullable|string|max:255',
            'discount_type'  => 'required|in:percent,fixed',
            // Khắc phục lỗi nhập quá 100%
            'discount_value' => ['required', 'numeric', 'min:0', 
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->discount_type === 'percent' && $value > 100) {
                        $fail('Mức giảm theo phần trăm không được vượt quá 100%.');
                    }
                }
            ],
            'min_amount'     => 'nullable|numeric|min:0',
            'max_uses'       => 'nullable|integer|min:1',
            'starts_at'      => 'required|date',
            'expires_at'     => 'required|date|after:starts_at',
        ]);

        $validated['is_active'] = $request->boolean('is_active');
        $venue->promotions()->create($validated);

        return redirect()->route('owner.venues.promotions.index', $venue)->with('success', 'Đã tạo mã khuyến mãi!');
    }

    public function edit(Promotion $promotion)
    {
        $this->authorizeVenue($promotion->venue);
        return view('owner.promotions.edit', compact('promotion'));
    }

    public function update(Request $request, Promotion $promotion)
    {
        $this->authorizeVenue($promotion->venue);

        $request->merge(['code' => strtoupper($request->input('code') ?? '')]);

        // Cập nhật đầy đủ toàn bộ các trường
        $validated = $request->validate([
            'code'           => ['required', 'string', 'max:50', 'alpha_dash', Rule::unique('promotions', 'code')->ignore($promotion->id)],
            'description'    => 'nullable|string|max:255',
            'discount_type'  => 'required|in:percent,fixed',
            'discount_value' => ['required', 'numeric', 'min:0', 
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->discount_type === 'percent' && $value > 100) {
                        $fail('Mức giảm theo phần trăm không được vượt quá 100%.');
                    }
                }
            ],
            'min_amount'     => 'nullable|numeric|min:0',
            'max_uses'       => 'nullable|integer|min:1',
            'starts_at'      => 'required|date',
            'expires_at'     => 'required|date|after:starts_at',
        ]);

        $validated['is_active'] = $request->boolean('is_active');
        $promotion->update($validated);

        return redirect()->route('owner.venues.promotions.index', $promotion->venue)->with('success', 'Đã cập nhật mã khuyến mãi!');
    }

    public function destroy(Promotion $promotion)
    {
        $venue = $promotion->venue;
        $this->authorizeVenue($venue);
        $promotion->delete();

        return redirect()->route('owner.venues.promotions.index', $venue)->with('success', 'Đã xóa mã khuyến mãi!');
    }

    private function authorizeVenue(Venue $venue)
    {
        abort_if($venue->owner_id !== auth()->id(), 403, 'Bạn không có quyền thao tác trên khu sân này.');
    }
}