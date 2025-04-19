<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class BookingConfirmed extends Notification
{
    use Queueable;

    public Booking $booking;

    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
    }

    public function via(object $notifiable): array
    {
        return ['mail']; // You can also add: 'database', 'broadcast'
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your Booking is Confirmed!')
            ->greeting('Hello ' . $notifiable->full_name)
            ->line('Your booking for the package "' . $this->booking->package->title . '" has been confirmed.')
            ->line('Travel Date: ' . $this->booking->travel_date)
            ->line('Number of Travelers: ' . $this->booking->number_of_travelers)
            ->line('Total Price: ' . number_format($this->booking->total_price, 2) . ' LKR')
            ->action('View Booking', url('/customer/bookings/' . $this->booking->id))
            ->line('Thank you for choosing Regency Travel!');
    }
}

