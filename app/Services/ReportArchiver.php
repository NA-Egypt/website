<?php

namespace App\Services;

use App\Models\CommitteeReport;
use App\Models\CommitteeReportAttachment;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Mpdf\Mpdf;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;
use Exception;

class ReportArchiver
{
    /**
     * Archive the given committee report (PDF and attachments).
     *
     * @param CommitteeReport $report
     * @return bool
     */
    public function archive(CommitteeReport $report): bool
    {
        // Only archive approved reports
        if ($report->status !== 'approved') {
            Log::warning("ReportArchiver: Attempted to archive report {$report->id} with status {$report->status}. Archiving is restricted to approved reports.");
            return false;
        }

        try {
            $year = $report->meeting_date->format('Y');
            $month = $report->meeting_date->format('m');
            $dateStr = $report->meeting_date->format('Y-m-d');

            // 1. Archive the PDF version of the report
            $pdfContent = $this->generatePdfContent($report);
            
            // Get all approved reports for the same committee in the same month and year
            $reportsInMonth = CommitteeReport::where('service_committee_id', $report->service_committee_id)
                ->where('status', 'approved')
                ->whereYear('meeting_date', $year)
                ->whereMonth('meeting_date', $month)
                ->orderBy('meeting_date', 'asc')
                ->orderBy('id', 'asc')
                ->pluck('id')
                ->toArray();

            // Ensure the current report is in the array if it is approved but not found (e.g. in transaction)
            if (!in_array($report->id, $reportsInMonth) && $report->status === 'approved') {
                $reportsInMonth[] = $report->id;
            }

            $totalReports = count($reportsInMonth);
            $suffix = '';
            if ($totalReports > 1) {
                $index = array_search($report->id, $reportsInMonth);
                $indexNumber = $index !== false ? $index + 1 : $totalReports;
                $suffix = '_' . $indexNumber;
            }

            $committeeName = $report->serviceCommittee ? $report->serviceCommittee->ar_name : '';
            // Remove path traversal characters
            $committeeName = str_replace(['/', '\\', "\0"], '', $committeeName);
            // Replace spaces with underscores
            $cleanedCommitteeName = str_replace(' ', '_', $committeeName);

            $pdfFilename = sprintf('تقريرـ%s_%s_%s%s.pdf', $cleanedCommitteeName, $month, $year, $suffix);
            $pdfPath = "{$year}/{$month}/" . $pdfFilename;

            Storage::disk('storagebox')->put($pdfPath, $pdfContent);
            Log::info("ReportArchiver: Archived report PDF to {$pdfPath}");

            // 2. Archive all attachments
            foreach ($report->attachments as $attachment) {
                $this->archiveAttachment($attachment, $year, $month);
            }

            return true;
        } catch (Exception $e) {
            Log::error("ReportArchiver: Failed to archive report {$report->id}. Error: " . $e->getMessage());
            // TODO(security): Log detailed exception while keeping user messages generic.
            return false;
        }
    }

    /**
     * Generate PDF content using Mpdf.
     *
     * @param CommitteeReport $report
     * @return string
     */
    protected function generatePdfContent(CommitteeReport $report): string
    {
        $defaultConfig = (new ConfigVariables())->getDefaults();
        $fontDirs = $defaultConfig['fontDir'];

        $defaultFontConfig = (new FontVariables())->getDefaults();
        $fontData = $defaultFontConfig['fontdata'];

        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'directionality' => app()->getLocale() == 'ar' ? 'rtl' : 'ltr',
            'fontDir' => array_merge($fontDirs, [resource_path('fonts')]),
            'fontdata' => $fontData + [
                'amiri' => [
                    'R' => 'Amiri-Regular.ttf',
                ],
                'cairo' => [
                    'R' => 'Cairo-Regular.ttf',
                ],
            ],
            'default_font' => 'xbriyaz',
        ]);

        $mpdf->autoArabic = true;
        $mpdf->autoScriptToLang = true;
        $mpdf->autoLangToFont = true;

        $reports = collect([$report]);
        $html = view('reports.pdf', compact('reports'))->render();
        $mpdf->WriteHTML($html);

        return $mpdf->Output('', 'S');
    }

    /**
     * Archive a single attachment to the storagebox disk.
     *
     * @param CommitteeReportAttachment $attachment
     * @param string $year
     * @param string $month
     * @return void
     */
    protected function archiveAttachment(CommitteeReportAttachment $attachment, string $year, string $month): void
    {
        if (!Storage::exists($attachment->file_path)) {
            Log::warning("ReportArchiver: Attachment file {$attachment->file_path} for attachment {$attachment->id} does not exist locally.");
            return;
        }

        // Sanitize the original filename to prevent path traversal
        $originalName = basename($attachment->original_name);
        $archiveFilename = sprintf('attachment_%d_%s', $attachment->id, $originalName);
        $archivePath = "attachments/{$year}/{$month}/" . $archiveFilename;

        try {
            // Read from default disk and write to storagebox disk
            $fileStream = Storage::readStream($attachment->file_path);
            if ($fileStream) {
                Storage::disk('storagebox')->writeStream($archivePath, $fileStream);
                if (is_resource($fileStream)) {
                    fclose($fileStream);
                }
                Log::info("ReportArchiver: Archived attachment {$attachment->id} to {$archivePath}");
            } else {
                Log::error("ReportArchiver: Failed to open read stream for local attachment file {$attachment->file_path}");
            }
        } catch (Exception $e) {
            Log::error("ReportArchiver: Failed to archive attachment {$attachment->id} to {$archivePath}. Error: " . $e->getMessage());
        }
    }
}
