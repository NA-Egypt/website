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
    protected $arabicMonths = [
        1 => 'يناير',
        2 => 'فبراير',
        3 => 'مارس',
        4 => 'أبريل',
        5 => 'مايو',
        6 => 'يونيو',
        7 => 'يوليو',
        8 => 'أغسطس',
        9 => 'سبتمبر',
        10 => 'أكتوبر',
        11 => 'نوفمبر',
        12 => 'ديسمبر'
    ];

    /**
     * Get the target meeting period info (month, year, arabic_month) based on the cutoff rules:
     * - February meeting: Dec 11 – Feb 10
     * - April meeting: Feb 11 – Apr 10
     * - June meeting: Apr 11 – Jun 10
     * - August meeting: Jun 11 – Aug 10
     * - October meeting is September (9) (exception): Aug 11 – Oct 10
     * - December meeting: Oct 11 – Dec 10
     *
     * @param mixed $date
     * @return array
     */
    public function getTargetMeetingPeriod($date): array
    {
        $carbonDate = $date instanceof \Carbon\Carbon ? $date : \Carbon\Carbon::parse($date);
        $m = (int)$carbonDate->format('n');
        $d = (int)$carbonDate->format('j');
        $y = (int)$carbonDate->format('Y');

        if (($m == 12 && $d >= 11) || $m == 1 || ($m == 2 && $d <= 10)) {
            $targetMonth = 2;
            $targetYear = ($m == 12) ? $y + 1 : $y;
        } elseif (($m == 2 && $d >= 11) || $m == 3 || ($m == 4 && $d <= 10)) {
            $targetMonth = 4;
            $targetYear = $y;
        } elseif (($m == 4 && $d >= 11) || $m == 5 || ($m == 6 && $d <= 10)) {
            $targetMonth = 6;
            $targetYear = $y;
        } elseif (($m == 6 && $d >= 11) || $m == 7 || ($m == 8 && $d <= 10)) {
            $targetMonth = 8;
            $targetYear = $y;
        } elseif (($m == 8 && $d >= 11) || $m == 9 || ($m == 10 && $d <= 10)) {
            $targetMonth = 9; // September is exception instead of October
            $targetYear = $y;
        } else {
            $targetMonth = 12;
            $targetYear = $y;
        }

        return [
            'month' => $targetMonth,
            'year' => $targetYear,
            'arabic_month' => $this->arabicMonths[$targetMonth] ?? ''
        ];
    }

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
            $date = $report->meeting_date;
            $period = $this->getTargetMeetingPeriod($date);
            $targetMonth = $period['month'];
            $targetYear = $period['year'];
            $arabicMonth = $period['arabic_month'];

            // Get month and year of meeting_date for the filename
            $month = $report->meeting_date->format('m');
            $year = $report->meeting_date->format('Y');

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
            
            $baseFolder = "Archives/أجندة إجتماع لجنة خدمة الاقليم/{$targetYear}/أجندة {$arabicMonth} {$targetYear}/التقارير الشهرية حتى 10 {$arabicMonth} {$targetYear}/{$committeeName}";
            $pdfPath = "{$baseFolder}/{$pdfFilename}";

            Storage::disk('storagebox')->put($pdfPath, $pdfContent);
            Log::info("ReportArchiver: Archived report PDF to {$pdfPath}");

            // 2. Archive all attachments
            foreach ($report->attachments as $attachment) {
                $this->archiveAttachment($attachment, $baseFolder);
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
     * Archive a single attachment to the storagebox disk under the given base folder.
     *
     * @param CommitteeReportAttachment $attachment
     * @param string $baseFolder
     * @return void
     */
    protected function archiveAttachment(CommitteeReportAttachment $attachment, string $baseFolder): void
    {
        if (!Storage::exists($attachment->file_path)) {
            Log::warning("ReportArchiver: Attachment file {$attachment->file_path} for attachment {$attachment->id} does not exist locally.");
            return;
        }

        // Sanitize the original filename to prevent path traversal
        $originalName = basename($attachment->original_name);
        $archiveFilename = sprintf('attachment_%d_%s', $attachment->id, $originalName);
        $archivePath = "{$baseFolder}/المرفقات/{$archiveFilename}";

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
