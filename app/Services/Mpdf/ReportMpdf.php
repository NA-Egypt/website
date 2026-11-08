<?php

namespace App\Services\Mpdf;

use Mpdf\Mpdf;
use ReflectionProperty;

class ReportMpdf extends Mpdf
{
    protected ?array $ktSnapshot = null;

    protected bool $closingKeepBlock = false;

    public function __construct(array $config = [], $container = null)
    {
        parent::__construct($config, $container);

        $this->shrink_tables_to_fit = 1;
        $this->tableMinSizePriority = true;
        $this->use_kwt = true;

        $this->installPageBreakAwareTag();
    }

    protected function installPageBreakAwareTag(): void
    {
        $getPrivate = function (string $propName) {
            $prop = new ReflectionProperty(Mpdf::class, $propName);
            $prop->setAccessible(true);
            return $prop->getValue($this);
        };

        $tag = new PageBreakAwareTag(
            $this,
            $getPrivate('cache'),
            $getPrivate('cssManager'),
            $getPrivate('form'),
            $getPrivate('otl'),
            $getPrivate('tableOfContents'),
            $getPrivate('sizeConverter'),
            $getPrivate('colorConverter'),
            $getPrivate('imageProcessor'),
            $getPrivate('languageToFont')
        );

        $tagProp = new ReflectionProperty(Mpdf::class, 'tag');
        $tagProp->setAccessible(true);
        $tagProp->setValue($this, $tag);
    }

    public function captureKeepBlockSnapshot(int $blklvl): void
    {
        $startPage = (int) $this->kt_p00;

        $this->ktSnapshot = [
            'blklvl' => $blklvl,
            'startPage' => $startPage,
            'startY' => (float) $this->kt_y00,
            'pageContent' => ($startPage > 0 && isset($this->pages[$startPage])) ? $this->pages[$startPage] : null,
            'tbctr' => $this->tbctr,
            'table' => $this->table,
            'innermostTableLevel' => $this->innermostTableLevel,
            'tableBackgrounds' => $this->tableBackgrounds,
            'overflowed' => false,
        ];
    }

    public function beginClosingKeepBlock(): void
    {
        $this->closingKeepBlock = true;
    }

    public function finishClosingKeepBlock(): void
    {
        $this->closingKeepBlock = false;

        if ($this->ktSnapshot === null) {
            return;
        }

        $startPage = $this->ktSnapshot['startPage'];
        $startY = $this->ktSnapshot['startY'];
        $overflowed = $this->ktSnapshot['overflowed'];

        $this->ktSnapshot = null;

        // If the block overflowed the current page during the dry run, did not start at the top
        // of the page, and BlockTag::close did not already move it (which happens when the block's
        // total height is >= 1 full page), move it to start cleanly at the top of the next page.
        if ($overflowed && $startY > ($this->tMargin + 0.001) && (int) $this->page === $startPage) {
            $this->AddPage();
        }
    }

    public function Reset()
    {
        parent::Reset();

        // In BlockTag::close (line 1364), Reset() is called right after flushing the block's
        // final textbuffer and right before checking $this->blk[$this->blklvl]['keep_block_together'].
        if ($this->closingKeepBlock && $this->keep_block_together && $this->ktSnapshot !== null) {
            $this->ktSnapshot['overflowed'] = ((int) $this->page > $this->ktSnapshot['startPage']);
        }
    }

    public function ResetMargins()
    {
        parent::ResetMargins();

        // In BlockTag::close (line 1417), ResetMargins() is called right after rewinding
        // $this->page to $this->kt_p00 and setting $this->keep_block_together = 0, and right
        // before `if ($movepage) { $this->mpdf->AddPage(); }`.
        // Restore the start page's stream and table state so dry-run table backgrounds/counters
        // leave no artifacts on the previous page.
        if ($this->closingKeepBlock && !$this->keep_block_together && $this->ktSnapshot !== null) {
            $startPage = $this->ktSnapshot['startPage'];
            if ($startPage > 0 && $this->ktSnapshot['pageContent'] !== null) {
                $this->pages[$startPage] = $this->ktSnapshot['pageContent'];
            }
            $this->tbctr = $this->ktSnapshot['tbctr'];
            $this->table = $this->ktSnapshot['table'];
            $this->innermostTableLevel = $this->ktSnapshot['innermostTableLevel'];
            $this->tableBackgrounds = $this->ktSnapshot['tableBackgrounds'];
        }
    }
}
