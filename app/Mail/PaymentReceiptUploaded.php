<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PaymentReceiptUploaded extends Mailable
{
    use Queueable, SerializesModels;

    public $booking;

    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
    }

    public function build()
    {
        return $this->subject('Payment Receipt Uploaded')
                    ->markdown('emails.admin.payment_uploaded')
                    ->attach(
                        storage_path('app/public/' . $this->booking->payment_reference),
                        [
                            'as' => 'Payment_Receipt_' . $this->booking->id . '.' . pathinfo($this->booking->payment_reference, PATHINFO_EXTENSION),
                            'mime' => mime_content_type(storage_path('app/public/' . $this->booking->payment_reference)),
                        ]
                    );
    }
}
