<?php

namespace App\Mail;

use App\Models\StaffNotice;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class StaffNoticeMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public StaffNotice $staffNotice
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->staffNotice->title,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.staff-notice-html',
        );
    }
}
