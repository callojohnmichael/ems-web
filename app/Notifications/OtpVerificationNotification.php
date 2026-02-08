<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OtpVerificationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $otp
    ) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('Your one-time verification code'))
            ->line('Your verification code is: '.$this->otp)
            ->line(__('This code expires in 60 seconds. Do not share it.'))
            ->line(__('If you did not request this, you can ignore this email.'));
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [];
    }
}
