<?php

namespace App\Observers;

use App\Models\Review;

class ReviewObserver
{
    /**
     * Handle the Review "created" event.
     */
    public function created(Review $review): void
    {
        $this->updateVenueRating($review->venue_id);
    }

    /**
     * Handle the Review "updated" event.
     */
    public function updated(Review $review): void
    {
        $this->updateVenueRating($review->venue_id);
    }

    /**
     * Handle the Review "deleted" event.
     */
    public function deleted(Review $review): void
    {
        // Khi xóa mềm, Eloquent tự động bỏ qua nếu dùng scope mặc định, 
        // nhưng ta gọi riêng để cập nhật lại chính xác bỏ qua bản ghi bị xóa.
        $this->updateVenueRating($review->venue_id);
    }

    /**
     * Cập nhật điểm trung bình cho Venue
     */
    protected function updateVenueRating($venueId)
    {
        $venue = \App\Models\Venue::find($venueId);
        if ($venue) {
            // Chỉ tính trung bình các review chưa bị xóa mềm và clamp rating từ 1 đến 5
            $avgRating = Review::where('venue_id', $venueId)
                ->whereNull('deleted_at')
                ->avg('rating');

            $venue->update([
                'rating_avg' => round($avgRating ? max(1, min(5, $avgRating)) : 0, 2)
            ]);
        }
    }
}