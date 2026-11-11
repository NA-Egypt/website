<?php

namespace App\Mail;

use App\Models\CommitteeReport;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CommitteeReportSubmittedMail extends Mailable
{
    use Queueable, SerializesModels;

    public CommitteeReport $report;
    public string $committeeName;
    public string $meetingDate;
    public string $reportUrl;

    /**
     * Create a new message instance.
     */
    public function __construct(CommitteeReport $report)
    {
        $this->report = $report;
        $this->committeeName = $report->serviceCommittee->ar_name ?? $report->serviceCommittee->en_name ?? 'Unknown Committee';
        $this->meetingDate = $report->meeting_date ? $report->meeting_date->format('Y-m-d') : 'N/A';
        $this->reportUrl = route('committee-reports.show', $report->id);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "New Committee Report: {$this->committeeName} ({$this->meetingDate})",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            htmlString: "
                <p>Dear RSC & Alternate RSC,</p>
                <p>A new committee report has been submitted by <strong>{$this->committeeName}</strong> for the meeting date <strong>{$this->meetingDate}</strong>.</p>
                <p><a href=\"{$this->reportUrl}\" style=\"display:inline-block;padding:8px 16px;background:#0d6efd;color:#ffffff;text-decoration:none;border-radius:4px;\">View Report</a></p>
                <p>You can also review this report in the management dashboard or archive.</p>
                <p>Regards,<br>NA Egypt System</p>
            "
        );
    }
}

