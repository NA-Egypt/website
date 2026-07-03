<?php

namespace App\Services;

use Mpdf\Mpdf;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;

class MpdfService
{
    /**
     * Create and return a pre-configured Mpdf instance.
     *
     * @param array $customOptions Options to merge or override default ones.
     * @return Mpdf
     */
    public static function create(array $customOptions = []): Mpdf
    {
        $defaultConfig = (new ConfigVariables())->getDefaults();
        $fontDirs = $defaultConfig['fontDir'];

        $defaultFontConfig = (new FontVariables())->getDefaults();
        $fontData = $defaultFontConfig['fontdata'];

        $locale = app()->getLocale();
        $direction = $locale == 'ar' ? 'rtl' : 'ltr';

        // Base options
        $options = [
            'mode' => 'utf-8',
            'format' => 'A4',
            'directionality' => $direction,
            'fontDir' => array_merge($fontDirs, [resource_path('fonts')]),
            'fontdata' => $fontData + [
                'amiri' => [
                    'R' => 'Amiri-Regular.ttf',
                    'useOTL' => 0xFF,
                    'useKashida' => 75,
                ],
                'cairo' => [
                    'R' => 'Cairo-Regular.ttf',
                    'useOTL' => 0xFF,
                    'useKashida' => 75,
                ],
            ],
            'default_font' => 'cairo',
        ];

        // Merge custom options
        $mergedOptions = array_merge($options, $customOptions);
        
        // Merge fontdata specifically if overridden
        if (isset($customOptions['fontdata'])) {
            $mergedOptions['fontdata'] = $options['fontdata'] + $customOptions['fontdata'];
        }

        $mpdf = new Mpdf($mergedOptions);

        $mpdf->autoArabic = true;
        $mpdf->autoScriptToLang = true;
        $mpdf->autoLangToFont = true;

        return $mpdf;
    }
}
