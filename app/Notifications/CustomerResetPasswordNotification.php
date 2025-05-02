<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CustomerResetPasswordNotification extends Notification
{
    use Queueable;

    public string $token;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $token)
    {
        $this->token = $token;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    // public function toMail(object $notifiable): MailMessage
    // {
    //     $url = url("/reset-password/customer/{$this->token}?email=" . urlencode($notifiable->email));

    //     return (new MailMessage)
    //         ->subject('Reset Your Customer Password')
    //         ->line('Click the button below to reset your password.')
    //         ->action('Reset Password', $url)
    //         ->line('If you did not request this, no action is needed.');
    // }

    public function toMail(object $notifiable): MailMessage
    {
        $url = "http://localhost:5173/reset-password?token={$this->token}&email=" . urlencode($notifiable->email);

        return (new MailMessage)
            ->subject('Reset Your Customer Password')
            ->line('Click the button below to reset your password.')
            ->action('Reset Password', $url)
            ->line('If you did not request this, no action is needed.');
    }


    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [];
    }
}
