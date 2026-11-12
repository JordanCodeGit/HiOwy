<?php

namespace App\Mail;

use App\Models\StaffMember;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class StaffInvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public object $staff,
        public string $schoolName,
        public string $invitationUrl
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "[HiOwy] Undangan Bergabung sebagai Staf {$this->schoolName}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.staff-invitation',
        );
    }
}
