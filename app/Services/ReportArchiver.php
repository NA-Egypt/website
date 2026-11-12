<?php

namespace App\Services;

use App\Models\CommitteeReport;
use App\Models\CommitteeReportAttachment;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Services\MpdfService;
use Exception;

class ReportArchiver
{
    protected array $arabicMonths = [
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
        12 => 'ديسمبر',
    ];

    /**
     * Convert Western Arabic numerals (0-9) to Eastern Arabic numerals (٠-٩).
     */
    public function toEasternNumerals(string|int $value): string
    {
        return strtr((string) $value, [
            '0' => '٠',
            '1' => '١',
            '2' => '٢',
            '3' => '٣',
            '4' => '٤',
            '5' => '٥',
            '6' => '٦',
            '7' => '٧',
            '8' => '٨',
            '9' => '٩',
        ]);
    }

    /**
     * Convert Eastern Arabic numerals (٠-٩) to Western Arabic numerals (0-9).
     */
    public function toWesternNumerals(string|int $value): string
    {
        return strtr((string) $value, [
            '٠' => '0',
            '١' => '1',
            '٢' => '2',
            '٣' => '3',
            '٤' => '4',
            '٥' => '5',
            '٦' => '6',
            '٧' => '7',
            '٨' => '8',
            '٩' => '9',
        ]);
    }

    /**
     * Normalize Arabic text for resilient folder name matching (hamzas, taa marbuta, extra whitespace, numerals).
     */
    public function normalizeArabicForMatch(string $text): string
    {
        $normalized = $this->toWesternNumerals($text);
        $normalized = str_replace(['أ', 'إ', 'آ', 'ٱ'], 'ا', $normalized);
        $normalized = str_replace(['ة'], 'ه', $normalized);
        $normalized = str_replace(['ى'], 'ي', $normalized);
        $normalized = preg_replace('/\s+/u', ' ', trim($normalized));

        return mb_strtolower($normalized, 'UTF-8');
    }

    /**
     * Determine whether the storagebox disk root already points inside the "Archives" directory.
     */
    public function diskRootIsArchives(): bool
    {
        try {
            $root = rtrim(str_replace('\\', '/', Storage::disk('storagebox')->path('')), '/');
            return basename($root) === 'Archives';
        } catch (\Throwable $e) {
            $root = rtrim(str_replace('\\', '/', (string) config('filesystems.disks.storagebox.root', '')), '/');
            return basename($root) === 'Archives';
        }
    }

    /**
     * Strip leading "Archives/" prefix when addressing the storagebox disk directly if its root is already "Archives".
     */
    public function toDiskRelativePath(string $path): string
    {
        $clean = ltrim(str_replace('\\', '/', $path), '/');
        if ($this->diskRootIsArchives() && str_starts_with($clean, 'Archives/')) {
            return substr($clean, strlen('Archives/'));
        }
        if (!$this->diskRootIsArchives() && !str_starts_with($clean, 'Archives/')) {
            return 'Archives/' . $clean;
        }

        return $clean;
    }

    /**
     * Ensure path has the "Archives/" root prefix for the UI explorer tree.
     */
    public function toTreePath(string $path): string
    {
        $clean = ltrim(str_replace('\\', '/', $path), '/');
        if ($clean === '' || $clean === 'Archives') {
            return 'Archives';
        }

        return str_starts_with($clean, 'Archives/') ? $clean : 'Archives/' . $clean;
    }

    /**
     * Get the target meeting period info (month, year, arabic_month, cutoff_month, cutoff_arabic_month, eastern_year)
     * based on the cutoff rules:
     * - February meeting: Dec 11 – Feb 10 (Cutoffs: Jan 10 [month 1] & Feb 10 [month 2])
     * - April meeting: Feb 11 – Apr 10 (Cutoffs: Mar 10 [month 3] & Apr 10 [month 4])
     * - June meeting: Apr 11 – Jun 10 (Cutoffs: May 10 [month 5] & Jun 10 [month 6])
     * - August meeting: Jun 11 – Aug 10 (Cutoffs: Jul 10 [month 7] & Aug 10 [month 8])
     * - October/September meeting: Aug 11 – Oct 10 (Cutoffs: Sep 10 [month 9] & Oct 10 [month 10 / 9])
     * - December meeting: Oct 11 – Dec 10 (Cutoffs: Nov 10 [month 11] & Dec 10 [month 12])
     *
     * @param mixed $date
     * @return array
     */
    public function getTargetMeetingPeriod($date): array
    {
        $carbonDate = $date instanceof \Carbon\Carbon ? $date : \Carbon\Carbon::parse($date);
        $m = (int) $carbonDate->format('n');
        $d = (int) $carbonDate->format('j');
        $y = (int) $carbonDate->format('Y');

        if (($m === 12 && $d >= 11) || $m === 1 || ($m === 2 && $d <= 10)) {
            $targetMonth = 2;
            $targetYear = ($m === 12) ? $y + 1 : $y;
            $cutoffMonth = ($m === 12 || ($m === 1 && $d <= 10)) ? 1 : 2;
        } elseif (($m === 2 && $d >= 11) || $m === 3 || ($m === 4 && $d <= 10)) {
            $targetMonth = 4;
            $targetYear = $y;
            $cutoffMonth = ($m === 2 || ($m === 3 && $d <= 10)) ? 3 : 4;
        } elseif (($m === 4 && $d >= 11) || $m === 5 || ($m === 6 && $d <= 10)) {
            $targetMonth = 6;
            $targetYear = $y;
            $cutoffMonth = ($m === 4 || ($m === 5 && $d <= 10)) ? 5 : 6;
        } elseif (($m === 6 && $d >= 11) || $m === 7 || ($m === 8 && $d <= 10)) {
            $targetMonth = 8;
            $targetYear = $y;
            $cutoffMonth = ($m === 6 || ($m === 7 && $d <= 10)) ? 7 : 8;
        } elseif (($m === 8 && $d >= 11) || $m === 9 || ($m === 10 && $d <= 10)) {
            $targetMonth = 9; // September is exception instead of October
            $targetYear = $y;
            $cutoffMonth = ($m === 8 || ($m === 9 && $d <= 10)) ? 9 : 9;
        } else {
            $targetMonth = 12;
            $targetYear = $y;
            $cutoffMonth = ($m === 10 || ($m === 11 && $d <= 10)) ? 11 : 12;
        }

        return [
            'month' => $targetMonth,
            'year' => $targetYear,
            'eastern_year' => $this->toEasternNumerals($targetYear),
            'arabic_month' => $this->arabicMonths[$targetMonth] ?? '',
            'cutoff_month' => $cutoffMonth,
            'cutoff_arabic_month' => $this->arabicMonths[$cutoffMonth] ?? ($this->arabicMonths[$targetMonth] ?? ''),
        ];
    }

    /**
     * Resolve the target base folder for a report, smartly matching existing OneDrive folders if present,
     * and defaulting to the OneDrive Eastern Arabic numeral folder naming convention.
     */
    public function resolveBaseFolder($date, string $committeeName, ?array $indexedPaths = null, bool $forDisk = false): string
    {
        $period = $this->getTargetMeetingPeriod($date);
        $targetYear = (string) $period['year'];
        $easternYear = $period['eastern_year'];
        $arabicMonth = $period['arabic_month'];
        $cutoffArabicMonth = $period['cutoff_arabic_month'];

        $cleanCommittee = str_replace(['/', '\\', "\0"], '', trim($committeeName));
        if ($cleanCommittee === '') {
            $cleanCommittee = 'لجنة غير محددة';
        }

        $rootSection = 'أجندة إجتماع لجنة خدمة الاقليم';
        $defaultAgendaFolder = "أجندة {$arabicMonth} {$easternYear}";
        $defaultCutoffFolder = "التقارير الشهرية حتى ١٠ {$cutoffArabicMonth} {$easternYear}";

        $matchedAgendaFolder = null;
        $matchedCutoffFolder = null;
        $matchedCommitteeFolder = null;

        // Collect candidate relative paths from parameter or cache
        if ($indexedPaths === null) {
            $cachedFiles = Cache::get('storagebox_archive_files_list', []);
            $cachedDirs = Cache::get('storagebox_archive_dirs_list', []);
            $indexedPaths = [];
            foreach ($cachedDirs as $d) {
                $indexedPaths[] = is_array($d) ? ($d['path'] ?? '') : (string) $d;
            }
            foreach ($cachedFiles as $f) {
                $indexedPaths[] = is_array($f) ? ($f['path'] ?? '') : (string) $f;
            }
        }

        if (!empty($indexedPaths)) {
            $normRoot = $this->normalizeArabicForMatch($rootSection);
            $normTargetAgendaMonth = $this->normalizeArabicForMatch($arabicMonth);
            $normCutoffMonth = $this->normalizeArabicForMatch($cutoffArabicMonth);
            $normCommittee = $this->normalizeArabicForMatch($cleanCommittee);

            $agendaCandidates = [];
            $cutoffCandidates = [];
            $committeeCandidates = [];

            foreach ($indexedPaths as $rawPath) {
                $p = ltrim(str_replace('\\', '/', $rawPath), '/');
                if (str_starts_with($p, 'Archives/')) {
                    $p = substr($p, strlen('Archives/'));
                }
                $parts = explode('/', $p);
                if (count($parts) < 3) {
                    continue;
                }
                if ($this->normalizeArabicForMatch($parts[0]) !== $normRoot) {
                    continue;
                }
                if ($this->toWesternNumerals($parts[1]) !== $targetYear) {
                    continue;
                }

                $agendaDir = $parts[2];
                $normAgendaDir = $this->normalizeArabicForMatch($agendaDir);
                if (str_contains($normAgendaDir, $normTargetAgendaMonth)) {
                    $agendaCandidates[$agendaDir] = ($agendaCandidates[$agendaDir] ?? 0) + 1;

                    if (isset($parts[3])) {
                        $cutoffDir = $parts[3];
                        $normCutoffDir = $this->normalizeArabicForMatch($cutoffDir);
                        if (
                            (str_contains($normCutoffDir, 'التقارير') || str_contains($normCutoffDir, 'اللجان'))
                            && str_contains($normCutoffDir, $normCutoffMonth)
                        ) {
                            $cutoffCandidates[$agendaDir][$cutoffDir] = ($cutoffCandidates[$agendaDir][$cutoffDir] ?? 0) + 1;

                            if (isset($parts[4])) {
                                $commDir = $parts[4];
                                if ($this->normalizeArabicForMatch($commDir) === $normCommittee) {
                                    $committeeCandidates[$agendaDir][$cutoffDir][$commDir] = true;
                                }
                            }
                        } elseif (
                            (str_contains($normCutoffDir, 'التقارير') || str_contains($normCutoffDir, 'اللجان'))
                            && str_contains($normCutoffDir, $normTargetAgendaMonth)
                        ) {
                            // Fallback to bi-monthly report folder if specific month folder doesn't exist
                            $cutoffCandidates[$agendaDir][$cutoffDir] = ($cutoffCandidates[$agendaDir][$cutoffDir] ?? 0) + 1;
                        }
                    }
                }
            }

            if (!empty($agendaCandidates)) {
                arsort($agendaCandidates);
                $matchedAgendaFolder = array_key_first($agendaCandidates);

                if (!empty($cutoffCandidates[$matchedAgendaFolder])) {
                    // Prefer exact cutoff month match over fallback agenda month match
                    $bestCutoff = null;
                    foreach ($cutoffCandidates[$matchedAgendaFolder] as $cDir => $count) {
                        if (str_contains($this->normalizeArabicForMatch($cDir), $normCutoffMonth)) {
                            $bestCutoff = $cDir;
                            break;
                        }
                    }
                    $matchedCutoffFolder = $bestCutoff ?? array_key_first($cutoffCandidates[$matchedAgendaFolder]);

                    if (!empty($committeeCandidates[$matchedAgendaFolder][$matchedCutoffFolder])) {
                        $matchedCommitteeFolder = array_key_first($committeeCandidates[$matchedAgendaFolder][$matchedCutoffFolder]);
                    }
                }
            }
        }

        $agendaFolder = $matchedAgendaFolder ?? $defaultAgendaFolder;
        $cutoffFolder = $matchedCutoffFolder ?? $defaultCutoffFolder;
        $committeeFolder = $matchedCommitteeFolder ?? $cleanCommittee;

        $relativePath = "{$rootSection}/{$targetYear}/{$agendaFolder}/{$cutoffFolder}/{$committeeFolder}";

        return $forDisk ? $this->toDiskRelativePath($relativePath) : $this->toTreePath($relativePath);
    }

    /**
     * Build the deterministic archived PDF filename for a CommitteeReport.
     */
    public function getExpectedPdfFilename(CommitteeReport $report, ?array $reportsInMonth = null): string
    {
        $month = $report->meeting_date ? $report->meeting_date->format('m') : $report->created_at->format('m');
        $year = $report->meeting_date ? $report->meeting_date->format('Y') : $report->created_at->format('Y');

        if ($reportsInMonth === null) {
            $reportsInMonth = CommitteeReport::where('service_committee_id', $report->service_committee_id)
                ->where('status', 'approved')
                ->whereYear('meeting_date', $year)
                ->whereMonth('meeting_date', $month)
                ->orderBy('meeting_date', 'asc')
                ->orderBy('id', 'asc')
                ->pluck('id')
                ->toArray();
        }

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

        $committeeName = $report->serviceCommittee ? ($report->serviceCommittee->ar_name ?? $report->serviceCommittee->en_name ?? '') : '';
        $committeeName = str_replace(['/', '\\', "\0"], '', $committeeName);
        $cleanedCommitteeName = str_replace(' ', '_', trim($committeeName));

        return sprintf('تقريرـ%s_%s_%s%s.pdf', $cleanedCommitteeName, $month, $year, $suffix);
    }

    /**
     * Archive the given committee report (PDF and attachments).
     *
     * @param CommitteeReport $report
     * @param bool $triggerOneDriveSync
     * @return bool
     */
    public function archive(CommitteeReport $report, bool $triggerOneDriveSync = false): bool
    {
        // Only archive approved reports
        if ($report->status !== 'approved') {
            Log::warning("ReportArchiver: Attempted to archive report {$report->id} with status {$report->status}. Archiving is restricted to approved reports.");
            return false;
        }

        try {
            $date = $report->meeting_date;
            $pdfContent = $this->generatePdfContent($report);
            $pdfFilename = $this->getExpectedPdfFilename($report);

            $committeeName = $report->serviceCommittee ? ($report->serviceCommittee->ar_name ?? $report->serviceCommittee->en_name ?? '') : '';
            $baseFolder = $this->resolveBaseFolder($date, $committeeName, null, true);
            $pdfPath = "{$baseFolder}/{$pdfFilename}";

            Storage::disk('storagebox')->put($pdfPath, $pdfContent);
            Log::info("ReportArchiver: Archived report PDF to {$pdfPath}");

            // Archive all attachments
            foreach ($report->attachments as $attachment) {
                $this->archiveAttachment($attachment, $baseFolder);
            }

            if ($triggerOneDriveSync) {
                try {
                    \App\Jobs\SyncOneDriveArchivesJob::dispatch($baseFolder);
                } catch (\Throwable $syncErr) {
                    Log::warning("ReportArchiver: Could not dispatch OneDrive sync job: " . $syncErr->getMessage());
                }
            }

            return true;
        } catch (Exception $e) {
            Log::error("ReportArchiver: Failed to archive report {$report->id}. Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Generate PDF content using Mpdf.
     *
     * @param CommitteeReport $report
     * @return string
     */
    public function generatePdfContent(CommitteeReport $report): string
    {
        $mpdf = MpdfService::create();

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
