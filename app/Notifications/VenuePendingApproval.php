<?php

namespace App\Notifications;

use App\Models\Venue;

/**
 * Gửi cho ADMIN khi chủ sân tạo khu sân mới đang chờ duyệt.
 */
class VenuePendingApproval extends ArenaNotification
{
    public function __construct(Venue $venue)
    {
        $owner = $venue->owner?->name ?? 'Chủ sân';

        parent::__construct(
            title: 'Có khu sân mới chờ duyệt',
            message: "{$owner} vừa thêm khu sân \"{$venue->name}\" ({$venue->district}, {$venue->city}). Vui lòng duyệt để khu sân hiển thị với khách.",
            url: route('admin.venues.index', ['status' => 'pending']),
            level: 'warning',
        );
    }
}
