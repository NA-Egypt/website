<?php

namespace App\Services;

use App\Models\ServiceBodyAgenda;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Mpdf\Mpdf;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;
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
            $pdfPath = "Archives/service_body_agendas/{$year}/{$monthStr}/" . $pdfFilename;

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

        $agendas = collect([$agenda]);
        $html = view('service-body-agendas.pdf', compact('agendas'))->render();
        $mpdf->WriteHTML($html);

        return $mpdf->Output('', 'S');
    }
}
