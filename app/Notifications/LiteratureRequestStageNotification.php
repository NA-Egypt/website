<?php

namespace App\Notifications;

use App\Models\LiteratureRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LiteratureRequestStageNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly LiteratureRequest $literatureRequest,
        public readonly string $stage,
        public readonly ?string $slipNumber = null,
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
            ->line($data['body']);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $groupName = $this->literatureRequest->group
            ? ($this->literatureRequest->group->ar_name ?: $this->literatureRequest->group->en_name)
            : 'N/A';
        $sbName = $this->literatureRequest->serviceBody
            ? ($this->literatureRequest->serviceBody->ar_name ?: $this->literatureRequest->serviceBody->en_name)
            : 'N/A';

        [$titleKey, $bodyKey] = match ($this->stage) {
            'submitted' => ['messages.notifications.literature_request_submitted_title', 'messages.notifications.literature_request_submitted_body'],
            'approved' => ['messages.notifications.literature_request_approved_title', 'messages.notifications.literature_request_approved_body'],
            'dispatched' => ['messages.notifications.literature_request_dispatched_title', 'messages.notifications.literature_request_dispatched_body'],
            default => ['messages.notifications.literature_request_returned_title', 'messages.notifications.literature_request_returned_body'],
        };

        return [
            'type' => 'literature_request_'.$this->stage,
            'literature_request_id' => $this->literatureRequest->id,
            'stage' => $this->stage,
            'title' => __($titleKey),
            'body' => __($bodyKey, [
                'id' => $this->literatureRequest->id,
                'group' => $groupName,
                'service_body' => $sbName,
                'slip' => $this->slipNumber ?? '',
            ]),
        ];
    }
}
