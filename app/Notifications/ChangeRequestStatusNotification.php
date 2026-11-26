<?php

namespace App\Notifications;

use App\Models\ChangeRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ChangeRequestStatusNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly ChangeRequest $changeRequest,
        public readonly string $newStatus,
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
        return [
            'type' => 'change_request_status',
            'change_request_id' => $this->changeRequest->id,
            'status' => $this->newStatus,
            'title' => __('messages.notifications.change_request_status_title'),
            'body' => __('messages.notifications.change_request_status_body', [
                'subject' => $this->changeRequest->subject,
                'status' => $this->newStatus,
            ]),
            'url' => route('change-requests.show', $this->changeRequest->id),
        ];
    }
}
