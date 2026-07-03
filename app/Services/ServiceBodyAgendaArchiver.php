<?php

namespace App\Services;

use App\Models\ServiceBodyAgenda;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Services\MpdfService;
use Exception;

class ServiceBodyAgendaArchiver
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
     * Archive the given ServiceBody agenda.
     *
     * @param ServiceBodyAgenda $agenda
     * @return bool
     */
    public function archive(ServiceBodyAgenda $agenda): bool
    {
        if ($agenda->status !== 'approved') {
            Log::warning("ServiceBodyAgendaArchiver: Attempted to archive agenda {$agenda->id} with status {$agenda->status}. Archiving is restricted to approved agendas.");
            return false;
        }

        try {
            $date = $agenda->meeting_date;
            $period = $this->getTargetMeetingPeriod($date);
            $targetMonth = $period['month'];
            $targetYear = $period['year'];
            $arabicMonth = $period['arabic_month'];

            $year = $agenda->meeting_date->format('Y');
            $monthNum = (int)$agenda->meeting_date->format('m');
            $monthStr = $agenda->meeting_date->format('m');

            $sbArabicName = $agenda->serviceBody ? $agenda->serviceBody->ar_name : 'خدمة';
            $sbArabicName = str_replace(['/', '\\', "\0"], '', $sbArabicName);
            $cleanedSbName = str_replace(' ', '_', $sbArabicName);

            $monthArabicName = $this->arabicMonths[$monthNum] ?? $monthStr;

            // Header is {ServiceBody Arabic Name} {Month Arabic Name} {year}
            // Filename: {ServiceBody Arabic Name}_{Month Arabic Name}_{year}.pdf (add _EX if exceptional)
            $suffix = $agenda->is_exceptional ? '_EX' : '';
            $pdfFilename = sprintf('%s_%s_%s%s.pdf', $cleanedSbName, $monthArabicName, $year, $suffix);
            
            // Put it under a folder structure in storagebox (prefixed with Archives/ so it merges into the main archive)
            $pdfPath = "Archives/أجندة إجتماع لجنة خدمة الاقليم/{$targetYear}/أجندة {$arabicMonth} {$targetYear}/التقارير الشهرية حتى 10 {$arabicMonth} {$targetYear}/أجندات المناطق و المنتديات/{$sbArabicName}/" . $pdfFilename;

            $pdfContent = $this->generatePdfContent($agenda);

            Storage::disk('storagebox')->put($pdfPath, $pdfContent);
            Log::info("ServiceBodyAgendaArchiver: Archived agenda PDF to {$pdfPath}");

            return true;
        } catch (Exception $e) {
            Log::error("ServiceBodyAgendaArchiver: Failed to archive agenda {$agenda->id}. Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Generate PDF content using Mpdf.
     *
     * @param ServiceBodyAgenda $agenda
     * @return string
     */
    protected function generatePdfContent(ServiceBodyAgenda $agenda): string
    {
        $mpdf = MpdfService::create();

        $agendas = collect([$agenda]);
        $html = view('service-body-agendas.pdf', compact('agendas'))->render();
        $mpdf->WriteHTML($html);

        return $mpdf->Output('', 'S');
    }
}
