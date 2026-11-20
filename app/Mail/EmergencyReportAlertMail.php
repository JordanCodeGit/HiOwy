<?php

namespace App\Mail;

use App\Models\StudentReport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EmergencyReportAlertMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(
        public readonly StudentReport $report,
        public readonly string $schoolName
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "[DARURAT] Laporan Baru #{$this->report->case_label} — {$this->schoolName}",
        );
    }

    /**
     * Only passes non-identifying metadata; strictly omits story and reporter identity (FR-51).
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.emergency-report-alert',
            with: [
                'caseLabel' => $this->report->case_label,
                'category' => $this->report->category,
                'location' => $this->report->location,
                'submittedDate' => $this->report->formatted_time,
                'schoolName' => $this->schoolName,
                'dashboardUrl' => route('bk.reports.show', $this->report->id),
            ],
        );
    }

    /**
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
