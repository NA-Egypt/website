<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\InventoryItem;
use App\Models\InventoryTransaction;
use App\Models\StocktakingSession;
use App\Models\StocktakingItem;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class UpdateInventoryFromDataFiles extends Command
{
    protected $signature = 'inventory:update-from-files {--dry-run : Run without committing changes}';
    protected $description = 'Update litstore and literature committee inventory and prices from data files and PDF price list';

    public function handle()
    {
        $jsonPath = '/home/hani/.gemini/antigravity-ide/brain/17cac11a-add0-469f-9f04-d45a7a0028d3/scratch/review_final.json';
        if (!file_exists($jsonPath)) {
            $this->error("JSON data file not found at: {$jsonPath}");
            return 1;
        }

        $data = json_decode(file_get_contents($jsonPath), true);
        $items = $data['items'] ?? [];
        $canonicalNew = $data['canonical_new'] ?? [];

        $isDryRun = $this->option('dry-run');

        $this->info("Found " . count($items) . " existing items and " . count($canonicalNew) . " new items.");

        if ($isDryRun) {
            $this->warn("Running in DRY-RUN mode. No changes will be saved to the database.");
            return 0;
        }

        $adminUser = User::role('super admin')->first() ?? User::first();
        $adminId = $adminUser ? $adminUser->id : 1;

        DB::beginTransaction();

        try {
            // 1. Insert canonical new items if not already present
            $createdCount = 0;
            $updatedCount = 0;

            foreach ($canonicalNew as $newIt) {
                $item = InventoryItem::firstOrNew(['name' => $newIt['name']]);
                $item->category = $newIt['category'];
                $item->selling_price = $newIt['selling_price'];
                $item->store_quantity = $newIt['final_store_stock'];
                $item->lit_quantity = $newIt['sept_lit_count'];
                $isNew = !$item->exists;
                $item->save();

                if ($isNew) {
                    $createdCount++;
                } else {
                    $updatedCount++;
                }

                // Initial store stock transaction
                if ($newIt['final_store_stock'] > 0) {
                    InventoryTransaction::create([
                        'inventory_item_id' => $item->id,
                        'user_id' => $adminId,
                        'type' => 'receive',
                        'quantity' => $newIt['final_store_stock'],
                        'notes' => 'الرصيد الافتتاحي لجرد 12 يونيو 2026 للمخزن',
                        'created_at' => Carbon::parse('2026-06-12 12:00:00'),
                        'updated_at' => Carbon::parse('2026-06-12 12:00:00'),
                    ]);
                }
            }

            // 2. Update existing items
            foreach ($items as $itData) {
                $item = InventoryItem::find($itData['id']);
                if (!$item) continue;

                $item->selling_price = $itData['new_price'];
                $item->store_quantity = $itData['final_store_stock'];
                $item->lit_quantity = $itData['sept_lit_count'];
                $item->save();
                $updatedCount++;

                // Initial stock on 12 June
                if ($itData['start_store'] > 0) {
                    InventoryTransaction::create([
                        'inventory_item_id' => $item->id,
                        'user_id' => $adminId,
                        'type' => 'receive',
                        'quantity' => $itData['start_store'],
                        'notes' => 'رصيد بداية الجرد 12 يونيو 2026',
                        'created_at' => Carbon::parse('2026-06-12 10:00:00'),
                        'updated_at' => Carbon::parse('2026-06-12 10:00:00'),
                    ]);
                }

                // Store receipts after June 12
                if ($itData['received_store'] > 0) {
                    InventoryTransaction::create([
                        'inventory_item_id' => $item->id,
                        'user_id' => $adminId,
                        'type' => 'receive',
                        'quantity' => $itData['received_store'],
                        'notes' => 'الوارد بعد الجرد لمخزن الأدبيات',
                        'created_at' => Carbon::parse('2026-07-01 10:00:00'),
                        'updated_at' => Carbon::parse('2026-07-01 10:00:00'),
                    ]);
                }

                // Store transfers out to Lit Committee
                if ($itData['issued_store'] > 0) {
                    InventoryTransaction::create([
                        'inventory_item_id' => $item->id,
                        'user_id' => $adminId,
                        'type' => 'transfer_to_lit',
                        'quantity' => $itData['issued_store'],
                        'notes' => 'إجمالي الصادر من المخزن إلى لجنة الأدبيات (يونيو - أغسطس 2026)',
                        'created_at' => Carbon::parse('2026-08-28 15:00:00'),
                        'updated_at' => Carbon::parse('2026-08-28 15:00:00'),
                    ]);
                }
            }

            // 3. Create StocktakingSession for September 2026 inventory count
            $session = StocktakingSession::create([
                'user_id' => $adminId,
                'status' => 'adjusted',
                'notes' => 'جرد لجنة الأدبيات لشهر سبتمبر 2026 ومطابقة عهدة المخزن والأدبيات',
                'started_at' => Carbon::parse('2026-09-01 09:00:00'),
                'completed_at' => Carbon::parse('2026-09-05 18:00:00'),
                'adjusted_at' => Carbon::parse('2026-09-05 18:30:00'),
                'adjusted_by' => $adminId,
                'created_at' => Carbon::parse('2026-09-05 18:30:00'),
                'updated_at' => Carbon::parse('2026-09-05 18:30:00'),
            ]);

            // Add StocktakingItems
            $allItems = InventoryItem::all();
            $itemsMap = collect($items)->keyBy('id');

            foreach ($allItems as $invItem) {
                $calcData = $itemsMap->get($invItem->id);

                $systemStore = $invItem->store_quantity;
                $countedStore = $invItem->store_quantity;
                $storeVar = 0;

                $systemLit = $calcData ? $calcData['aug_calc_lit'] : $invItem->lit_quantity;
                $countedLit = $invItem->lit_quantity;
                $litVar = $countedLit - $systemLit;

                $unitPrice = $invItem->selling_price;
                $varianceValue = $litVar * $unitPrice;

                StocktakingItem::create([
                    'stocktaking_session_id' => $session->id,
                    'inventory_item_id' => $invItem->id,
                    'system_store_qty' => $systemStore,
                    'system_lit_qty' => $systemLit,
                    'counted_store_qty' => $countedStore,
                    'counted_lit_qty' => $countedLit,
                    'store_variance' => $storeVar,
                    'lit_variance' => $litVar,
                    'unit_price' => $unitPrice,
                    'variance_value' => $varianceValue,
                    'created_at' => Carbon::parse('2026-09-05 18:30:00'),
                    'updated_at' => Carbon::parse('2026-09-05 18:30:00'),
                ]);
            }

            DB::commit();

            $this->info("SUCCESS! Updated {$updatedCount} items, inserted {$createdCount} new items.");
            $this->info("Created StocktakingSession ID: {$session->id} with " . $allItems->count() . " stocktaking items.");
            return 0;

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("Error executing update: " . $e->getMessage());
            $this->error($e->getTraceAsString());
            return 1;
        }
    }
}
