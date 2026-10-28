<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\InventoryItem;
use App\Models\InventoryTransaction;
use App\Models\InventorySlip;
use App\Models\InventorySlipItem;
use App\Models\StocktakingSession;
use App\Models\StocktakingItem;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use ZipArchive;

class UpdateInventoryFromDataFiles extends Command
{
    protected $signature = 'inventory:update-from-files
                            {--file=storage/app/public/invstart.xlsx : Path to the Excel inventory file}
                            {--dry-run : Run without committing changes}';

    protected $description = 'Update inventory items, prices, and opening balances from storage/app/public/invstart.xlsx';

    public function handle(): int
    {
        $filePath = base_path($this->option('file'));
        if (!file_exists($filePath)) {
            $this->error("Excel file not found at: {$filePath}");
            return 1;
        }

        $parsedItems = $this->parseInvstartXlsx($filePath);
        if (empty($parsedItems)) {
            $this->error("No inventory items could be parsed from {$filePath}");
            return 1;
        }

        $this->info("Parsed " . count($parsedItems) . " inventory items from {$filePath}.");

        $isDryRun = (bool) $this->option('dry-run');
        if ($isDryRun) {
            $this->warn("Running in DRY-RUN mode. No changes will be saved to the database.");
        }

        $adminUser = User::role('super admin')->first() ?? User::first();
        $adminId = $adminUser ? $adminUser->id : 1;

        // Explicit mapping for the 13 near-matches in invstart.xlsx -> existing DB item IDs
        $explicitNameToIdMap = [
            'العيش ممتنعا' => 7,
            'إنة يعمل - كيف ولماذا (جيب)' => 9,
            'أنة يعمل - كيف ولماذا (تذكاري)' => 13,
            'دليل عمل الخطوات (تذكاري)' => 14,
            'المنعزل - البقاء ممتنعاً' => 37,
            'Muiltple Years / Black' => 48,
            'PI and  the NA Member' => 91,
            'The group' => 97,
            'The group booklet' => 98,
            "Treasurer's Hanbook" => 102,
            'Who, what,How & Why' => 104,
            'Mental health in recovery' => 106,
            'كوين برونز انفينيتي' => 184,
        ];

        // Clean display names for new items where Excel had a typo or needed disambiguation
        $cleanNewItemNames = [
            'Name tag Muiltple Years / Black' => 'Name tag Multiple Years / Black',
        ];

        DB::beginTransaction();

        try {
            // 1. Clear old test transactions, slips, and stocktaking sessions
            $deletedSlipItems = InventorySlipItem::query()->delete();
            $deletedSlips = InventorySlip::query()->delete();
            $deletedStocktakingItems = StocktakingItem::query()->delete();
            $deletedStocktakingSessions = StocktakingSession::query()->delete();
            $deletedTransactions = InventoryTransaction::query()->delete();

            $this->info("Cleared old records: {$deletedTransactions} transactions, {$deletedSlips} slips, {$deletedStocktakingSessions} stocktaking sessions.");

            $existingItems = InventoryItem::all();
            $byExactName = $existingItems->keyBy(fn(InventoryItem $it) => trim($it->name));
            $byNormalizedName = $existingItems->keyBy(fn(InventoryItem $it) => $this->normalizeName($it->name));

            $updatedCount = 0;
            $createdCount = 0;
            $openingTxCount = 0;

            foreach ($parsedItems as $row) {
                $rawName = $row['name'];
                $category = $row['category'];
                $prevBalance = $row['prev_balance'];
                $storeQty = $row['store_quantity'];
                $sellingPrice = $row['selling_price'];

                $matchedItem = null;

                $cleanName = $cleanNewItemNames[$rawName] ?? $rawName;

                // Special case: "Sponsorship" appears in both English Books (id 57) and English IP (new item)
                if ($rawName === 'Sponsorship' && $category === 'English IP') {
                    $matchedItem = $existingItems->first(
                        fn(InventoryItem $it) => $it->category === 'English IP' && in_array($it->name, ['Sponsorship', 'Sponsorship (IP)'], true)
                    );
                    $cleanName = 'Sponsorship (IP)';
                } elseif (isset($explicitNameToIdMap[$rawName])) {
                    $matchedItem = InventoryItem::find($explicitNameToIdMap[$rawName]);
                } elseif ($byExactName->has($rawName)) {
                    $matchedItem = $byExactName->get($rawName);
                } elseif ($byExactName->has($cleanName)) {
                    $matchedItem = $byExactName->get($cleanName);
                } elseif ($byNormalizedName->has($this->normalizeName($rawName))) {
                    $matchedItem = $byNormalizedName->get($this->normalizeName($rawName));
                } elseif ($byNormalizedName->has($this->normalizeName($cleanName))) {
                    $matchedItem = $byNormalizedName->get($this->normalizeName($cleanName));
                }

                $isEnglishCategory = in_array($category, ['English Books', 'English IP'], true);

                if ($matchedItem) {
                    // Rename ID 184 ("كوين انفينيتي") to "كوين برونز انفينيتي" to distinguish from "كوين انفينيتي ألوان"
                    if ($matchedItem->id === 184) {
                        $matchedItem->name = 'كوين برونز انفينيتي';
                    }
                    if ($isEnglishCategory && empty($matchedItem->name_en)) {
                        $matchedItem->name_en = $matchedItem->name;
                    }
                    $matchedItem->category = $category;
                    $matchedItem->selling_price = $sellingPrice;
                    $matchedItem->store_quantity = $storeQty;
                    $matchedItem->lit_quantity = 0;
                    $matchedItem->save();
                    $item = $matchedItem;
                    $updatedCount++;
                } else {
                    $item = InventoryItem::create([
                        'name' => $cleanName,
                        'name_en' => $isEnglishCategory ? $cleanName : null,
                        'category' => $category,
                        'selling_price' => $sellingPrice,
                        'store_quantity' => $storeQty,
                        'lit_quantity' => 0,
                    ]);
                    $createdCount++;
                }

                if ($storeQty > 0) {
                    $note = 'رصيد بداية الخدمة أكتوبر 2026';
                    if ($prevBalance !== null && $prevBalance !== $storeQty) {
                        $note .= " (الرصيد السابق: {$prevBalance})";
                    }

                    InventoryTransaction::create([
                        'inventory_item_id' => $item->id,
                        'user_id' => $adminId,
                        'type' => 'receive',
                        'quantity' => $storeQty,
                        'notes' => $note,
                        'created_at' => Carbon::parse('2026-10-01 10:00:00'),
                        'updated_at' => Carbon::parse('2026-10-01 10:00:00'),
                    ]);
                    $openingTxCount++;
                }
            }

            if ($isDryRun) {
                DB::rollBack();
                $this->info("DRY-RUN COMPLETE: Would update {$updatedCount} items, create {$createdCount} new items, and log {$openingTxCount} opening transactions.");
                return 0;
            }

            DB::commit();

            $this->info("SUCCESS! Updated {$updatedCount} existing items, added {$createdCount} new items, and logged {$openingTxCount} opening balance transactions.");
            return 0;
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->error("Error executing inventory update: " . $e->getMessage());
            $this->error($e->getTraceAsString());
            return 1;
        }
    }

    /**
     * Parse all 3 worksheets of storage/app/public/invstart.xlsx using native PHP ZipArchive & SimpleXML
     */
    private function parseInvstartXlsx(string $filePath): array
    {
        $zip = new ZipArchive();
        if ($zip->open($filePath) !== true) {
            return [];
        }

        $sharedStrings = [];
        $sharedXml = $zip->getFromName('xl/sharedStrings.xml');
        if ($sharedXml !== false) {
            $sx = simplexml_load_string($sharedXml);
            if ($sx !== false) {
                foreach ($sx->si as $si) {
                    if (isset($si->t)) {
                        $sharedStrings[] = (string) $si->t;
                    } else {
                        $text = '';
                        foreach ($si->r as $run) {
                            $text .= (string) $run->t;
                        }
                        $sharedStrings[] = $text;
                    }
                }
            }
        }

        $items = [];

        // Sheet 1: ادبيات عربي (Arabic Books, Arabic IP, Chips, Readings)
        $sheet1Rows = $this->readWorksheetRows($zip, 'xl/worksheets/sheet1.xml', $sharedStrings);
        $currentCategory = 'Arabic Books';
        $skipNamesSheet1 = [
            'زمالة المدمنين المجهوليين',
            'اقليم مصــر',
            'مخــزن الأدبيات',
            'النوع',
            'اجمالي المخزون بالجنيه المصري',
        ];

        foreach ($sheet1Rows as $rowIdx => $cells) {
            $name = trim($cells['A'] ?? '');
            if ($name === '' || in_array($name, $skipNamesSheet1, true)) {
                continue;
            }

            if ($name === 'الكتيبات (IP)') {
                $currentCategory = 'Arabic IP';
                continue;
            }
            if ($name === 'الشيبات Chips' || $name === 'Name Key Tag to be sold at event') {
                $currentCategory = 'Chips';
                continue;
            }
            if ($name === 'Readings القراءات') {
                $currentCategory = 'Readings';
                continue;
            }

            $items[] = [
                'name' => $name,
                'category' => $currentCategory,
                'prev_balance' => isset($cells['B']) && is_numeric($cells['B']) ? (int) round((float) $cells['B']) : null,
                'store_quantity' => isset($cells['C']) && is_numeric($cells['C']) ? (int) round((float) $cells['C']) : 0,
                'selling_price' => isset($cells['D']) && is_numeric($cells['D']) ? (float) $cells['D'] : 0.00,
            ];
        }

        // Sheet 2: ادبيات انجليزي (English Books, English IP)
        $sheet2Rows = $this->readWorksheetRows($zip, 'xl/worksheets/sheet2.xml', $sharedStrings);
        $currentCategory = 'English Books';
        $skipNamesSheet2 = [
            'English Books',
            'English IP (s)',
            'اجمالي المخزون بالجنيه المصري',
        ];

        foreach ($sheet2Rows as $rowIdx => $cells) {
            $name = trim($cells['A'] ?? '');
            if ($name === '') {
                continue;
            }
            if ($name === 'English Books') {
                $currentCategory = 'English Books';
                continue;
            }
            if ($name === 'English IP (s)') {
                $currentCategory = 'English IP';
                continue;
            }
            if (in_array($name, $skipNamesSheet2, true)) {
                continue;
            }

            $items[] = [
                'name' => $name,
                'category' => $currentCategory,
                'prev_balance' => isset($cells['B']) && is_numeric($cells['B']) ? (int) round((float) $cells['B']) : null,
                'store_quantity' => isset($cells['C']) && is_numeric($cells['C']) ? (int) round((float) $cells['C']) : 0,
                'selling_price' => isset($cells['D']) && is_numeric($cells['D']) ? (float) $cells['D'] : 0.00,
            ];
        }

        // Sheet 3: Coins (Coins & Medallions)
        $sheet3Rows = $this->readWorksheetRows($zip, 'xl/worksheets/sheet3.xml', $sharedStrings);
        $skipNamesSheet3 = [
            'Medals & Coins',
            'اجمالي المخزون بالجنيه المصري',
        ];

        foreach ($sheet3Rows as $rowIdx => $cells) {
            $name = trim($cells['A'] ?? '');
            if ($name === '' || in_array($name, $skipNamesSheet3, true)) {
                continue;
            }

            $category = str_starts_with($name, 'ميدالية') ? 'Medallions' : 'Coins';

            $items[] = [
                'name' => $name,
                'category' => $category,
                'prev_balance' => isset($cells['B']) && is_numeric($cells['B']) ? (int) round((float) $cells['B']) : null,
                'store_quantity' => isset($cells['C']) && is_numeric($cells['C']) ? (int) round((float) $cells['C']) : 0,
                'selling_price' => isset($cells['D']) && is_numeric($cells['D']) ? (float) $cells['D'] : 0.00,
            ];
        }

        $zip->close();

        return $items;
    }

    private function readWorksheetRows(ZipArchive $zip, string $sheetPath, array $sharedStrings): array
    {
        $xmlContent = $zip->getFromName($sheetPath);
        if ($xmlContent === false) {
            return [];
        }

        $xml = simplexml_load_string($xmlContent);
        if ($xml === false || !isset($xml->sheetData->row)) {
            return [];
        }

        $rows = [];
        foreach ($xml->sheetData->row as $row) {
            $rowIdx = (int) $row['r'];
            $cells = [];
            foreach ($row->c as $c) {
                $ref = (string) $c['r'];
                $col = preg_replace('/[^A-Z]/', '', $ref);
                $type = (string) $c['t'];
                $val = null;

                if ($type === 's' && isset($c->v)) {
                    $idx = (int) $c->v;
                    $val = $sharedStrings[$idx] ?? '';
                } elseif ($type === 'inlineStr' && isset($c->is->t)) {
                    $val = (string) $c->is->t;
                } elseif (isset($c->v)) {
                    $val = (string) $c->v;
                }

                if ($val !== null && trim($val) !== '') {
                    $cells[$col] = trim($val);
                }
            }
            if (!empty($cells)) {
                $rows[$rowIdx] = $cells;
            }
        }

        return $rows;
    }

    private function normalizeName(string $name): string
    {
        $normalized = preg_replace('/\s+/u', ' ', trim($name));
        $normalized = str_replace(
            ['أ', 'إ', 'آ', 'ة', 'ى', 'ً', 'ٌ', 'ٍ', 'َ', 'ُ', 'ِ', 'ّ', 'ْ'],
            ['ا', 'ا', 'ا', 'ه', 'ي', '', '', '', '', '', '', '', ''],
            $normalized
        );
        return mb_strtolower($normalized, 'UTF-8');
    }
}
