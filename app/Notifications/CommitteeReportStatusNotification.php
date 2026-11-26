<?php

namespace App\Notifications;

use App\Models\CommitteeReport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CommitteeReportStatusNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly CommitteeReport $report,
        public readonly string $event,
    ) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $data = $this->toArray($notifiable);

        return (new MailMessage)
            ->subject($data['title'])
            ->line($data['body'])
            ->action(__('messages.notifications.view_action'), $data['url']);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $committee = $this->report->serviceCommittee;
        $committeeName = $committee ? ($committee->ar_name ?: $committee->en_name) : 'N/A';

        [$titleKey, $bodyKey] = match ($this->event) {
            'submitted' => ['messages.notifications.committee_report_submitted_title', 'messages.notifications.committee_report_submitted_body'],
            'returned' => ['messages.notifications.committee_report_returned_title', 'messages.notifications.committee_report_returned_body'],
            default => ['messages.notifications.committee_report_approved_title', 'messages.notifications.committee_report_approved_body'],
        };

        return [
            'type' => 'committee_report_'.$this->event,
            'report_id' => $this->report->id,
            'status' => $this->report->status,
            'title' => __($titleKey),
            'body' => __($bodyKey, [
                'committee' => $committeeName,
                'notes' => $this->report->review_notes ?? '',
            ]),
            'url' => route('committee-reports.show', $this->report->id),
        ];
    }
}
