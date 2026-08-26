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
            'code'           => [
                'required', 'string', 'max:50', 'alpha_dash',
                Rule::unique('promotions')->where(fn ($query) => $query->where('venue_id', $venue->id))
            ],
            'description'    => 'nullable|string|max:255',
            'discount_type'  => 'required|in:percent,fixed',
            'discount_value' => 'required|numeric|min:0',
            'min_amount'     => 'nullable|numeric|min:0',
            'max_uses'       => 'nullable|integer|min:1',
            'starts_at'      => 'required|date',
            'expires_at'     => 'required|date|after:starts_at',
            'is_active'      => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $venue->promotions()->create($validated);

        return redirect()->route('owner.venues.promotions.index', $venue)
            ->with('success', 'Đã tạo mã khuyến mãi thành công!');
    }

    public function destroy(Promotion $promotion)
    {
        $venue = $promotion->venue;
        $this->authorizeVenue($venue);

        $promotion->delete();

        return redirect()->route('owner.venues.promotions.index', $venue)
            ->with('success', 'Đã xóa mã khuyến mãi!');
    }

    private function authorizeVenue(Venue $venue)
    {
        abort_if(
            $venue->owner_id !== auth()->id(), 
            403, 
            'Bạn không có quyền thao tác trên khu sân này.'
        );
    }
}