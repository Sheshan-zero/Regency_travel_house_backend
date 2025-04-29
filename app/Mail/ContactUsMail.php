<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactUsMail extends Mailable
{
    use Queueable, SerializesModels;

    public $firstName;
    public $full_name;
    public $lastName;
    public $email;
    public $subjectText;
    public $messageText;

    /**
     * Create a new message instance.
     */
    public function __construct($full_name, $lastName, $email, $subjectText, $messageText)
    {
        $this->full_name = $full_name;
        $this->lastName = $lastName;
        $this->email = $email;
        $this->subjectText = $subjectText;
        $this->messageText = $messageText;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('New Contact Us Message')
                    ->view('emails.contact_us') // Blade template path
                    ->with([
                        'full_name' => $this->full_name,
                        'lastName' => $this->lastName,
                        'email' => $this->email,
                        'subjectText' => $this->subjectText,
                        'messageText' => $this->messageText,  // <- changed key here
                    ]);
                    
    }
}
