<?php

namespace App\Notifications;

use App\Models\InventorySlip;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InventorySlipPendingNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly InventorySlip $slip,
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
            'type' => 'inventory_slip_pending',
            'slip_id' => $this->slip->id,
            'slip_number' => $this->slip->slip_number,
            'slip_type' => $this->slip->type,
            'title' => __('messages.notifications.inventory_slip_pending_title'),
            'body' => __('messages.notifications.inventory_slip_pending_body', [
                'slip' => $this->slip->slip_number,
                'type' => $this->slip->type,
            ]),
            'url' => route('slips.show', $this->slip->id),
        ];
    }
}
