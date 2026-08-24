<?php

namespace App\Observers;

use App\Models\Review;

class ReviewObserver
{
    // Cập nhật rating_avg của venue sau khi có review mới/sửa/xóa
    public function created(Review $review): void
    {
        $this->recalculate($review);
    }

    public function updated(Review $review): void
    {
        $this->recalculate($review);
    }

    public function deleted(Review $review): void
    {
        $this->recalculate($review);
    }

    public function restored(Review $review): void
    {
        $this->recalculate($review);
    }

    private function recalculate(Review $review): void
    {
        $venue = $review->venue;
        $avg = $venue->reviews()
            ->where('is_visible', true)
            ->avg('rating') ?? 0;

        $venue->updateQuietly(['rating_avg' => round($avg, 2)]);
    }
}
