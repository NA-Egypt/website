<?php

namespace App\Mail;

use App\Models\ApkDownloadRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ApkDownloadLinkMail extends Mailable
{
    use Queueable, SerializesModels;

    public ApkDownloadRequest $apkRequest;
    public string $downloadUrl;
    public int $expiryHours;

    /**
     * Create a new message instance.
     */
    public function __construct(ApkDownloadRequest $apkRequest)
    {
        $this->apkRequest = $apkRequest;
        $this->downloadUrl = route('apk.download', ['token' => $apkRequest->token]);
        $this->expiryHours = (int) config('services.apk.token_expiry_hours', 24);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'رابط تحميل تطبيق زمالة المدمنين المجهولين التجريبي | NA Egypt Pre-release APK Download Link',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mail.apk-download-link',
            with: [
                'apkRequest' => $this->apkRequest,
                'downloadUrl' => $this->downloadUrl,
                'expiryHours' => $this->expiryHours,
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
        return [];
    }
}
