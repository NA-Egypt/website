<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ChangeRequestMail extends Mailable
{
    use Queueable, SerializesModels;

    public $changeRequest;

    /**
     * Create a new message instance.
     */
    public function __construct(\App\Models\ChangeRequest $changeRequest)
    {
        $this->changeRequest = $changeRequest;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $entityName = 'Unknown';
        if ($this->changeRequest->user && $this->changeRequest->user->serviceBody) {
            $entityName = $this->changeRequest->user->serviceBody->en_name;
        } else if ($this->changeRequest->user) {
            $committee = \App\Models\ServiceCommittee::where('user_id', $this->changeRequest->user->id)->first();
            if ($committee) {
                $entityName = $committee->en_name;
            }
        }
        return new Envelope(
            subject: 'IT Change Request from ' . $entityName . ': ' . $this->changeRequest->subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mail.change-request',
            with: [
                'changeRequest' => $this->changeRequest,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        if ($this->changeRequest->attachment_path) {
            $fullPath = storage_path('app/' . $this->changeRequest->attachment_path);
            if (file_exists($fullPath)) {
                return [
                    \Illuminate\Mail\Mailables\Attachment::fromPath($fullPath)
                        ->as(basename($this->changeRequest->attachment_path))
                ];
            }
        }
        return [];
    }
}
