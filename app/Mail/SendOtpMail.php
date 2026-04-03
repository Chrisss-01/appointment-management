<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class SendOtpMail extends Mailable
{
    public function __construct(public string $otp)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'UV Clinic Appointment System – Email Verification',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.otp',
        );
    }
}
