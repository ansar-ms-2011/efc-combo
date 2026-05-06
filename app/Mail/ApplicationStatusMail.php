<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ApplicationStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    public $application;
    public $actionBy;
    public $status;

    public function __construct($application, $actionBy, $status)
    {
        $this->application = $application;
        $this->actionBy = $actionBy;
        $this->status = $status;
    }

    public function build()
    {
        return $this->subject('Application Status Updated')
            ->view('email.application-status');
    }
}
