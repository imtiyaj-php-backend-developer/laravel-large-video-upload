<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class UploadCompleted extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct()
    {
        //
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Video Upload Completed',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.upload_completed',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}