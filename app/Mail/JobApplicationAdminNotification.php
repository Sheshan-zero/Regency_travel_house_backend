<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class JobApplicationAdminNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $application;

    /**
     * Create a new message instance.
     */
    public function __construct($application)
    {
        $this->application = $application;
    }

    public function build()
    {
        return $this->subject('New Job Application Received')
            ->markdown('emails.admiNotify')
            ->attach(
                storage_path('app/public/' . $this->application->cv_path), // ✅ Correct cv_path used
                [
                    'as' => 'CV_' . $this->application->id . '.' . pathinfo($this->application->cv_path, PATHINFO_EXTENSION),
                    'mime' => mime_content_type(storage_path('app/public/' . $this->application->cv_path)),
                ]
            );
    }    
}
