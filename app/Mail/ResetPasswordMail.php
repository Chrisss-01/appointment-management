<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class ResetPasswordMail extends Mailable
{
    public function __construct(public string $resetUrl)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'UV Clinic Appointment System – Request to Reset Your Password',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.reset-password',
        );
    }
}
