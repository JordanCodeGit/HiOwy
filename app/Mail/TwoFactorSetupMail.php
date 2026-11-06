<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TwoFactorSetupMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public string $activationUrl,
        public string $otpauthUri
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '[HiOwy] Tautan Aktivasi Google Authenticator Staf Sekolah',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.two-factor-setup',
        );
    }
}
