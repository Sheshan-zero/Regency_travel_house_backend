<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactUsMail extends Mailable
{
    use Queueable, SerializesModels;

    public $firstName;
    public $lastName;
    public $email;
    public $subject;
    public $message;

    /**
     * Create a new message instance.
     *
     * @param string $firstName
     * @param string $lastName
     * @param string $email
     * @param string $subject
     * @param string $message
     * @return void
     */
    public function __construct($firstName, $lastName, $email, $subject, $message)
    {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->email = $email;
        $this->subject = $subject;
        $this->message = $message;
    }

    /**
     * Build the message.
     *
     * @return \Illuminate\Mail\Mailable
     */
    public function build()
    {
        return $this->view('emails.contact_us')  // You can create a Blade view for the email content.
                    ->with([
                        'firstName' => $this->firstName,
                        'lastName' => $this->lastName,
                        'email' => $this->email,
                        'subject' => $this->subject,
                        'message' => $this->message,
                    ])
                    ->subject('New Contact Us Message');
    }
}
