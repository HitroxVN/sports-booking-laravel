<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Nền chung cho thông báo của hệ thống.
 *
 * Mỗi thông báo đi 2 kênh với cùng tiêu đề/nội dung: `database` (chuông in-app)
 * và `mail` (email). Cố ý KHÔNG implement ShouldQueue — dự án chưa có queue
 * worker chạy nền (xem A5), nếu queue thì thông báo in-app cũng sẽ không bao
 * giờ tới. Việc cô lập lỗi gửi mail khỏi luồng nghiệp vụ nằm ở App\Services\Notifier.
 */
abstract class ArenaNotification extends Notification
{
    public function __construct(
        protected string $title,
        protected string $message,
        protected string $url = '',
        protected string $level = 'info', // info | success | warning | danger
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'title'   => $this->title,
            'message' => $this->message,
            'url'     => $this->url,
            'level'   => $this->level,
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject($this->title)
            ->greeting('Xin chào ' . ($notifiable->name ?? 'bạn') . '!')
            ->line($this->message);

        // Vài thông báo không có trang đích cụ thể (VD thông báo cho admin)
        if ($this->url !== '') {
            $mail->action('Xem chi tiết', $this->url);
        }

        return $mail->salutation('Trân trọng, Arena Sports Booking');
    }
}
