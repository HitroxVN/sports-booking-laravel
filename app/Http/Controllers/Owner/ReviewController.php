<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index()
    {
        // Lấy tất cả đánh giá thuộc các khu sân của chủ sân này
        $reviews = Review::with(['user', 'venue', 'booking'])
            ->whereHas('venue', function ($q) {
                $q->where('owner_id', auth()->id());
            })
            ->latest()
            ->paginate(10);

        return view('owner.reviews.index', compact('reviews'));
    }

    public function update(Request $request, Review $review)
    {
        // Kiểm tra quyền sở hữu
        abort_if($review->venue->owner_id !== auth()->id(), 403);

        $validated = $request->validate([
            'owner_reply' => 'nullable|string|max:1000',
            'is_visible'  => 'boolean',
        ]);

        $validated['is_visible'] = $request->has('is_visible');
        $review->update($validated);

        return back()->with('success', 'Đã cập nhật phản hồi đánh giá!');
    }
}