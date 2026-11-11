<?php

namespace App\Jobs;

use App\Models\CommitteeReport;
use App\Services\WhatsApp\WhatsAppClient;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendCommitteeReportSubmissionNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $reportId;

    /**
     * Create a new job instance.
     */
    public function __construct(int $reportId)
    {
        $this->reportId = $reportId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $report = CommitteeReport::with('serviceCommittee')->find($this->reportId);

        if (!$report) {
            Log::warning("[SendCommitteeReportSubmissionNotification] Report #{$this->reportId} not found.");
            return;
        }

        $committeeName = $report->serviceCommittee->ar_name ?? $report->serviceCommittee->en_name ?? 'Unknown Committee';
        $meetingDate = $report->meeting_date ? $report->meeting_date->format('Y-m-d') : 'N/A';
        $reportUrl = route('committee-reports.show', $report->id);

        // 1. Deliver Email Notification to RSC and Alternate RSC officers
        $this->sendEmail($report, $committeeName, $meetingDate, $reportUrl);

        // 2. Deliver WhatsApp Alert if configured & available (bypass if not configured)
        $this->sendWhatsAppAlert($committeeName, $meetingDate, $reportUrl);
    }

    /**
     * Send email alert to RSC and Alt RSC officers.
     */
    protected function sendEmail(CommitteeReport $report, string $committeeName, string $meetingDate, string $reportUrl): void
    {
        try {
            $recipients = [
                'rsc@naegypt.org',
                'altrsc@naegypt.org',
                'arsc@naegypt.org',
            ];

            Mail::to($recipients)->send(new \App\Mail\CommitteeReportSubmittedMail($report));

            Log::info("[SendCommitteeReportSubmissionNotification] Email sent for report #{$report->id} to " . implode(', ', $recipients));
        } catch (\Throwable $e) {
            Log::error("[SendCommitteeReportSubmissionNotification] Failed to send email alert for report #{$report->id}: " . $e->getMessage());
        }
    }

    /**
     * Send WhatsApp alert if numbers are configured and service is reachable.
     * Bypasses WhatsApp cleanly if no numbers are provided or if an error occurs.
     */
    protected function sendWhatsAppAlert(string $committeeName, string $meetingDate, string $reportUrl): void
    {
        $numbers = config('whatsapp.rsc_notification_numbers', []);

        if (empty($numbers) || !is_array($numbers)) {
            Log::info("[SendCommitteeReportSubmissionNotification] WhatsApp notification bypassed (no RSC recipient numbers configured).");
            return;
        }

        $message = "🔔 *إشعار تقديم تقرير لجنة*\n\n"
                 . "تم تقديم تقرير جديد من لجنة: *{$committeeName}*\n"
                 . "تاريخ الاجتماع: *{$meetingDate}*\n\n"
                 . "رابط الاطلاع على التقرير:\n{$reportUrl}";

        try {
            $client = app(WhatsAppClient::class);

            foreach ($numbers as $phone) {
                $trimmed = trim($phone);
                if (empty($trimmed)) {
                    continue;
                }

                $res = $client->sendTextMessage($trimmed, $message);
                if (!empty($res['success'])) {
                    Log::info("[SendCommitteeReportSubmissionNotification] WhatsApp alert delivered to {$trimmed} for report #{$this->reportId}");
                } else {
                    Log::warning("[SendCommitteeReportSubmissionNotification] WhatsApp alert failed for {$trimmed}: " . json_encode($res));
                }
            }
        } catch (\Throwable $e) {
            // Bypass failure cleanly without breaking the job execution
            Log::warning("[SendCommitteeReportSubmissionNotification] WhatsApp notification bypassed due to service exception: " . $e->getMessage());
        }
    }
}
