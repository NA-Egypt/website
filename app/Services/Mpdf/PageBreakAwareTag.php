<?php

namespace App\Services\Mpdf;

use Mpdf\Cache;
use Mpdf\Color\ColorConverter;
use Mpdf\CssManager;
use Mpdf\Form;
use Mpdf\Image\ImageProcessor;
use Mpdf\Language\LanguageToFontInterface;
use Mpdf\Otl;
use Mpdf\SizeConverter;
use Mpdf\TableOfContents;
use Mpdf\Tag;

class PageBreakAwareTag extends Tag
{
    protected ReportMpdf $mpdfInstance;

    public function __construct(
        ReportMpdf $mpdf,
        Cache $cache,
        CssManager $cssManager,
        Form $form,
        Otl $otl,
        TableOfContents $tableOfContents,
        SizeConverter $sizeConverter,
        ColorConverter $colorConverter,
        ImageProcessor $imageProcessor,
        LanguageToFontInterface $languageToFont
    ) {
        $this->mpdfInstance = $mpdf;

        parent::__construct(
            $mpdf,
            $cache,
            $cssManager,
            $form,
            $otl,
            $tableOfContents,
            $sizeConverter,
            $colorConverter,
            $imageProcessor,
            $languageToFont
        );
    }

    public function OpenTag($tag, $attr, &$ahtml, &$ihtml)
    {
        $wasKeeping = (bool) $this->mpdfInstance->keep_block_together;

        $result = parent::OpenTag($tag, $attr, $ahtml, $ihtml);

        if (!$wasKeeping && $this->mpdfInstance->keep_block_together) {
            $this->mpdfInstance->captureKeepBlockSnapshot((int) $this->mpdfInstance->blklvl);
        }

        return $result;
    }

    public function CloseTag($tag, &$ahtml, &$ihtml)
    {
        $blklvl = (int) $this->mpdfInstance->blklvl;
        $currBlk = $this->mpdfInstance->blk[$blklvl] ?? null;
        $isClosingKeepBlock = !empty($currBlk['keep_block_together']) && ($currBlk['tag'] ?? '') === $tag;

        if ($isClosingKeepBlock) {
            $this->mpdfInstance->beginClosingKeepBlock();
        }

        $result = parent::CloseTag($tag, $ahtml, $ihtml);

        if ($isClosingKeepBlock) {
            $this->mpdfInstance->finishClosingKeepBlock();
        }

        return $result;
    }
}

