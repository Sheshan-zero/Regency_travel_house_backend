<?php

namespace App\Mail;

use App\Models\Quote;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class QuoteResponseMail extends Mailable
{
    use Queueable, SerializesModels;

    public $quote;

    public function __construct(Quote $quote)
    {
        $this->quote = $quote;
    }

    public function build()
    {
        return $this->subject('Your Quote is Ready')
                ->markdown('emails.quote_response')
                ->with([
                    'customerName' => $this->quote->customer->full_name,
                    'quoteId' => $this->quote->id,
                ]);
    }
}
