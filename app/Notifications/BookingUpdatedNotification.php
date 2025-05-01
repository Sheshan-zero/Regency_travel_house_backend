<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class BookingUpdatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $booking;

    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
    }

    public function via($notifiable)
    {
        return ['mail', 'database']; // store in DB and send mail
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Your Booking Has Been Updated')
            ->greeting('Hello ' . $notifiable->full_name . ',')
            ->line('Your booking for the package "' . $this->booking->package->title . '" has been updated.')
            ->line('New status: ' . ucfirst($this->booking->status))
            ->action('View Booking', url('/your-booking-url/' . $this->booking->id))
            ->line('Thank you for using Regency Travel!');
    }

    public function toDatabase($notifiable)
    {
        return [
            'message' => 'Your booking for "' . $this->booking->package->title . '" was updated to ' . $this->booking->status,
            'booking_id' => $this->booking->id,
        ];
    }
}
