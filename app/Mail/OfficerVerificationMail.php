<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class OfficerVerificationMail extends Mailable
{
    public function __construct(public string $code) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Verify your MSWDO officer email');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.officer-verification');
    }
}
