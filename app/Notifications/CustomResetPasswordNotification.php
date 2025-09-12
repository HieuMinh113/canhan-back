<?php
namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class CustomResetPasswordNotification extends Notification
{
    public $token;

    public function __construct($token)
    {
        $this->token = $token;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $frontendUrl = 'http://localhost:8080/reset';

        return (new MailMessage)
            ->subject('Đặt lại mật khẩu')
            ->line('Bạn đã yêu cầu đặt lại mật khẩu.')
            ->line('Mã token của bạn:')
            ->line($this->token) 
            ->line('Email: ' . $notifiable->email)
            ->action('Nhấn để đặt lại mật khẩu', $frontendUrl . '?token=' . $this->token . '&email=' . urlencode($notifiable->email))
            ->line('Nếu bạn không yêu cầu, hãy bỏ qua email này.');
    }
}
