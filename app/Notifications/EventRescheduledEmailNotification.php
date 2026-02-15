<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EventRescheduledEmailNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly string $subject,
        private readonly string $message,
        private readonly string $url,
        private readonly string $actionText = 'View event',
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
            ->subject($this->subject)
            ->line($this->message)
            ->action($this->actionText, $this->url);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [];
    }
}
