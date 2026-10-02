<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reward;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class RewardController extends Controller
{
    /**
     * Danh sách phần thưởng đổi điểm.
     */
    public function index(Request $request): View
    {
        $status = $request->query('status');

        $query = Reward::withCount('transactions')->latest();

        if ($status === 'active') {
            $query->where('is_active', true);
        } elseif ($status === 'inactive') {
            $query->where('is_active', false);
        }

        $rewards = $query->paginate(10)->withQueryString();

        $stats = [
            'total' => Reward::count(),
            'active' => Reward::where('is_active', true)->count(),
            'inactive' => Reward::where('is_active', false)->count(),
            'total_redeemed' => \App\Models\LoyaltyTransaction::where('type', 'redeem')->count(),
        ];

        return view('admin.rewards.index', compact('rewards', 'stats', 'status'));
    }

    /**
     * Tạo mới phần thưởng đổi điểm.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:255'],
            'points_required' => ['required', 'integer', 'min:1'],
            'discount_type' => ['required', 'in:fixed,percent'],
            'discount_value' => [
                'required',
                'numeric',
                'min:1',
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->discount_type === 'percent' && $value > 100) {
                        $fail('Mức giảm theo phần trăm không được vượt quá 100%.');
                    }
                },
            ],
            'min_amount' => ['nullable', 'numeric', 'min:0'],
            'max_discount' => ['nullable', 'numeric', 'min:0'],
            'valid_days' => ['required', 'integer', 'min:1', 'max:365'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $baseSlug = Str::slug($validated['name']);
        $slug = $baseSlug;
        $counter = 1;
        while (Reward::where('slug', $slug)->exists()) {
            $slug = "{$baseSlug}-{$counter}";
            $counter++;
        }

        $validated['slug'] = $slug;
        $validated['is_active'] = $request->boolean('is_active', true);

        Reward::create($validated);

        return back()->with('success', 'Đã tạo phần thưởng đổi điểm thành công.');
    }

    /**
     * Cập nhật thông tin phần thưởng.
     */
    public function update(Request $request, Reward $reward): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:255'],
            'points_required' => ['required', 'integer', 'min:1'],
            'discount_type' => ['required', 'in:fixed,percent'],
            'discount_value' => [
                'required',
                'numeric',
                'min:1',
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->discount_type === 'percent' && $value > 100) {
                        $fail('Mức giảm theo phần trăm không được vượt quá 100%.');
                    }
                },
            ],
            'min_amount' => ['nullable', 'numeric', 'min:0'],
            'max_discount' => ['nullable', 'numeric', 'min:0'],
            'valid_days' => ['required', 'integer', 'min:1', 'max:365'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $reward->update($validated);

        return back()->with('success', 'Đã cập nhật phần thưởng thành công.');
    }

    /**
     * Xóa hoặc tắt kích hoạt phần thưởng.
     */
    public function destroy(Reward $reward): RedirectResponse
    {
        if ($reward->transactions()->exists()) {
            $reward->update(['is_active' => false]);
            return back()->with('warning', 'Phần thưởng này đã có khách hàng đổi voucher nên đã được chuyển sang trạng thái "Tắt hoạt động" để đảm bảo toàn vẹn dữ liệu.');
        }

        $reward->delete();

        return back()->with('success', 'Đã xóa phần thưởng thành công.');
    }
}
