<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

/**
 * Gửi thông báo (in-app + email) mà không bao giờ làm hỏng luồng nghiệp vụ.
 *
 * Lỗi SMTP hay lỗi ghi DB đều bị nuốt và ghi log: khách đặt được sân là việc
 * chính, không thể vì mail server chết mà đơn không tạo được.
 */
class Notifier
{
    public static function send(?User $user, Notification $notification): void
    {
        if (! $user) {
            return;
        }

        try {
            $user->notify($notification);
        } catch (\Throwable $e) {
            Log::warning('Không gửi được thông báo', [
                'user_id'      => $user->id,
                'notification' => $notification::class,
                'error'        => $e->getMessage(),
            ]);
        }
    }

    /**
     * Gửi cho toàn bộ admin.
     *
     * Nhận callable tạo notification thay vì nhận sẵn 1 instance: mỗi notifiable
     * phải có instance riêng, vì Notification giữ 1 uuid dùng làm khóa chính của
     * bảng `notifications` — dùng chung instance sẽ lỗi trùng khóa ở người thứ 2.
     *
     * @param  callable(User): Notification  $make
     */
    public static function toAdmins(callable $make): void
    {
        User::where('role', 'admin')->each(
            fn (User $admin) => static::send($admin, $make($admin))
        );
    }
}
