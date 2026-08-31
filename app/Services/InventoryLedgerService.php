<?php

namespace App\Services;

use App\Models\InventoryItem;
use App\Models\InventoryTransaction;
use App\Models\InventorySlip;
use App\Models\InventorySlipItem;
use App\Models\LiteratureRequest;
use App\Models\LiteratureRequestItem;
use App\Models\StocktakingSession;
use App\Models\StocktakingItem;
use App\Services\DateNumberHelper;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class InventoryLedgerService
{
    /**
     * Create an InventorySlip for Store to Lit transfers
     */
    public function createTransferSlip(array $itemsData, int $userId, ?string $notes = null): ?InventorySlip
    {
        if (empty($itemsData)) {
            return null;
        }

        $slipNumber = InventorySlip::generateSlipNumber('transfer_to_lit');
        $totalItems = 0;
        $totalValue = 0.00;

        $slip = InventorySlip::create([
            'slip_number' => $slipNumber,
            'type' => 'transfer_to_lit',
            'status' => 'transferred',
            'issued_by' => $userId,
            'total_items_count' => 0,
            'total_value' => 0.00,
            'notes' => $notes,
        ]);

        foreach ($itemsData as $row) {
            $qty = (int) ($row['quantity'] ?? 0);
            if ($qty <= 0) continue;

            $item = InventoryItem::find($row['inventory_item_id']);
            if (!$item) continue;

            $unitPrice = (float) ($row['unit_price'] ?? $item->selling_price);
            $itemTotal = $qty * $unitPrice;

            InventorySlipItem::create([
                'inventory_slip_id' => $slip->id,
                'inventory_item_id' => $item->id,
                'quantity' => $qty,
                'unit_price' => $unitPrice,
                'total_price' => $itemTotal,
            ]);

            $totalItems += $qty;
            $totalValue += $itemTotal;
        }

        $slip->update([
            'total_items_count' => $totalItems,
            'total_value' => $totalValue,
        ]);

        return $slip;
    }

    /**
     * Create an InventorySlip for Lit to Store returns
     */
    public function createReturnSlip(array $itemsData, int $userId, ?string $notes = null): ?InventorySlip
    {
        if (empty($itemsData)) {
            return null;
        }

        $slipNumber = InventorySlip::generateSlipNumber('return_to_store');
        $totalItems = 0;
        $totalValue = 0.00;

        $slip = InventorySlip::create([
            'slip_number' => $slipNumber,
            'type' => 'return_to_store',
            'status' => 'completed',
            'issued_by' => $userId,
            'received_by' => $userId,
            'received_at' => now(),
            'total_items_count' => 0,
            'total_value' => 0.00,
            'notes' => $notes,
        ]);

        foreach ($itemsData as $row) {
            $qty = (int) ($row['quantity'] ?? 0);
            if ($qty <= 0) continue;

            $item = InventoryItem::find($row['inventory_item_id']);
            if (!$item) continue;

            $unitPrice = (float) ($row['unit_price'] ?? $item->selling_price);
            $itemTotal = $qty * $unitPrice;

            InventorySlipItem::create([
                'inventory_slip_id' => $slip->id,
                'inventory_item_id' => $item->id,
                'quantity' => $qty,
                'unit_price' => $unitPrice,
                'total_price' => $itemTotal,
            ]);

            $totalItems += $qty;
            $totalValue += $itemTotal;
        }

        $slip->update([
            'total_items_count' => $totalItems,
            'total_value' => $totalValue,
        ]);

        return $slip;
    }

    /**
     * Get monthly sales aggregated per item from fulfilled literature requests
     */
    public function getMonthlySalesByItem(Carbon $month): array
    {
        $startOfMonth = $month->copy()->startOfMonth();
        $endOfMonth = $month->copy()->endOfMonth();

        // 1. Check if there are ServiceBody requests (which aggregate group requests)
        $sbRequests = LiteratureRequest::where('type', 'servicebody')
            ->where('month', $startOfMonth->toDateString())
            ->whereIn('status', ['sent_to_committee', 'returned_by_committee', 'approved'])
            ->with('items')
            ->get();

        $sales = [];

        if ($sbRequests->isNotEmpty()) {
            foreach ($sbRequests as $req) {
                foreach ($req->items as $reqItem) {
                    $sales[$reqItem->inventory_item_id] = ($sales[$reqItem->inventory_item_id] ?? 0) + $reqItem->quantity;
                }
            }
        } else {
            // Fallback to submitted/approved Group requests if no ServiceBody accumulated request exists
            $groupRequests = LiteratureRequest::where('type', 'group')
                ->where('month', $startOfMonth->toDateString())
                ->whereIn('status', ['submitted', 'approved'])
                ->with('items')
                ->get();

            foreach ($groupRequests as $req) {
                foreach ($req->items as $reqItem) {
                    $sales[$reqItem->inventory_item_id] = ($sales[$reqItem->inventory_item_id] ?? 0) + $reqItem->quantity;
                }
            }
        }

        return $sales;
    }

    /**
     * Get Reconciliation Data for Literature Committee
     */
    public function getReconciliationData(Carbon $month): array
    {
        $startOfMonth = $month->copy()->startOfMonth();
        $endOfMonth = $month->copy()->endOfMonth();

        $items = InventoryItem::orderBy('name')->get();
        $salesByItem = $this->getMonthlySalesByItem($month);

        // Fetch latest stocktaking session in or before this month if any
        $stocktakingSession = StocktakingSession::where('created_at', '<=', $endOfMonth)
            ->whereIn('status', ['completed', 'adjusted'])
            ->orderBy('created_at', 'desc')
            ->first();

        $stocktakingItemsMap = [];
        if ($stocktakingSession) {
            $sItems = StocktakingItem::where('stocktaking_session_id', $stocktakingSession->id)->get();
            foreach ($sItems as $sItem) {
                $stocktakingItemsMap[$sItem->inventory_item_id] = $sItem->counted_lit_qty ?? $sItem->system_lit_qty;
            }
        }

        $reconciliationList = [];
        $totalReceived = 0;
        $totalSold = 0;
        $totalReturned = 0;
        $totalCurrentLit = 0;

        foreach ($items as $item) {
            // Received in this month
            $receivedQty = (int) InventoryTransaction::where('inventory_item_id', $item->id)
                ->whereIn('type', ['transfer_to_lit', 'transfer'])
                ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                ->sum('quantity');

            // Returned to store in this month
            $returnedQty = (int) InventoryTransaction::where('inventory_item_id', $item->id)
                ->where('type', 'return_from_lit')
                ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                ->sum('quantity');

            // Sold in this month
            $soldQty = (int) ($salesByItem[$item->id] ?? 0);

            // Latest counted quantity (or current lit_quantity if no stocktaking)
            $countedQty = $stocktakingItemsMap[$item->id] ?? $item->lit_quantity;

            // System expected calculation for active cycle
            // Expected = (Current lit quantity) or (Received - Sold)
            $expectedQty = $item->lit_quantity;
            $suggestedReturn = max(0, $countedQty - $soldQty);

            $reconciliationList[] = [
                'item_id' => $item->id,
                'name' => $item->name,
                'name_en' => $item->name_en,
                'display_name' => $item->store_display_name,
                'category' => $item->category,
                'selling_price' => (float) $item->selling_price,
                'received_qty' => $receivedQty,
                'sold_qty' => $soldQty,
                'returned_qty' => $returnedQty,
                'current_lit_qty' => $item->lit_quantity,
                'counted_qty' => $countedQty,
                'expected_qty' => $expectedQty,
                'suggested_return' => $suggestedReturn,
            ];

            $totalReceived += $receivedQty;
            $totalSold += $soldQty;
            $totalReturned += $returnedQty;
            $totalCurrentLit += $item->lit_quantity;
        }

        return [
            'month' => $month,
            'items' => $reconciliationList,
            'total_received' => $totalReceived,
            'total_sold' => $totalSold,
            'total_returned' => $totalReturned,
            'total_current_lit' => $totalCurrentLit,
            'has_stocktaking' => !empty($stocktakingSession),
            'stocktaking_date' => $stocktakingSession ? $stocktakingSession->created_at->format('Y-m-d') : null,
        ];
    }

    /**
     * Get Complete Monthly Ledger Data
     */
    public function getMonthlyLedger(Carbon $month): array
    {
        $startOfMonth = $month->copy()->startOfMonth();
        $endOfMonth = $month->copy()->endOfMonth();

        $items = InventoryItem::orderBy('name')->get();
        $salesByItem = $this->getMonthlySalesByItem($month);

        // Transactions grouped by item for the target month
        $transactions = InventoryTransaction::whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->get()
            ->groupBy('inventory_item_id');

        $itemDetails = [];
        $categoriesMap = [];

        $storeTotalReceivedQty = 0;
        $storeTotalTransferredQty = 0;
        $storeTotalReturnedQty = 0;
        $storeTotalRemainsQty = 0;
        $storeTotalValuation = 0.00;

        $litTotalReceivedQty = 0;
        $litTotalSoldQty = 0;
        $litTotalReturnedQty = 0;
        $litTotalRemainsQty = 0;
        $litTotalSalesValuation = 0.00;
        $litTotalStockValuation = 0.00;

        foreach ($items as $item) {
            $itemTx = $transactions->get($item->id, collect());

            $storeReceived = (int) $itemTx->where('type', 'receive')->sum('quantity');
            $transferredToLit = (int) $itemTx->whereIn('type', ['transfer_to_lit', 'transfer'])->sum('quantity');
            $returnedFromLit = (int) $itemTx->where('type', 'return_from_lit')->sum('quantity');
            $soldQty = (int) ($salesByItem[$item->id] ?? 0);

            $storeRemains = (int) $item->store_quantity;
            $litRemains = (int) $item->lit_quantity;
            $price = (float) $item->selling_price;

            $storeValuation = $storeRemains * $price;
            $litValuation = $litRemains * $price;
            $salesValue = $soldQty * $price;

            $row = [
                'id' => $item->id,
                'name' => $item->name,
                'name_en' => $item->name_en,
                'display_name' => $item->store_display_name,
                'category' => $item->category,
                'selling_price' => $price,
                // Litstore metrics
                'store_received' => $storeReceived,
                'store_transferred' => $transferredToLit,
                'store_returned' => $returnedFromLit,
                'store_remains' => $storeRemains,
                'store_valuation' => $storeValuation,
                // Lit Comm metrics
                'lit_received' => $transferredToLit,
                'lit_sold' => $soldQty,
                'lit_returned' => $returnedFromLit,
                'lit_remains' => $litRemains,
                'lit_sales_value' => $salesValue,
                'lit_valuation' => $litValuation,
                // Combined
                'total_remains' => $storeRemains + $litRemains,
                'total_valuation' => $storeValuation + $litValuation,
            ];

            $itemDetails[] = $row;

            // Aggregate Category Metrics
            $cat = $item->category ?: 'Others';
            if (!isset($categoriesMap[$cat])) {
                $categoriesMap[$cat] = [
                    'category' => $cat,
                    'items_count' => 0,
                    'store_received' => 0,
                    'store_transferred' => 0,
                    'store_returned' => 0,
                    'store_remains' => 0,
                    'store_valuation' => 0.00,
                    'lit_received' => 0,
                    'lit_sold' => 0,
                    'lit_returned' => 0,
                    'lit_remains' => 0,
                    'lit_sales_value' => 0.00,
                    'lit_valuation' => 0.00,
                    'total_remains' => 0,
                    'total_valuation' => 0.00,
                ];
            }

            $categoriesMap[$cat]['items_count']++;
            $categoriesMap[$cat]['store_received'] += $storeReceived;
            $categoriesMap[$cat]['store_transferred'] += $transferredToLit;
            $categoriesMap[$cat]['store_returned'] += $returnedFromLit;
            $categoriesMap[$cat]['store_remains'] += $storeRemains;
            $categoriesMap[$cat]['store_valuation'] += $storeValuation;
            $categoriesMap[$cat]['lit_received'] += $transferredToLit;
            $categoriesMap[$cat]['lit_sold'] += $soldQty;
            $categoriesMap[$cat]['lit_returned'] += $returnedFromLit;
            $categoriesMap[$cat]['lit_remains'] += $litRemains;
            $categoriesMap[$cat]['lit_sales_value'] += $salesValue;
            $categoriesMap[$cat]['lit_valuation'] += $litValuation;
            $categoriesMap[$cat]['total_remains'] += ($storeRemains + $litRemains);
            $categoriesMap[$cat]['total_valuation'] += ($storeValuation + $litValuation);

            // Global totals
            $storeTotalReceivedQty += $storeReceived;
            $storeTotalTransferredQty += $transferredToLit;
            $storeTotalReturnedQty += $returnedFromLit;
            $storeTotalRemainsQty += $storeRemains;
            $storeTotalValuation += $storeValuation;

            $litTotalReceivedQty += $transferredToLit;
            $litTotalSoldQty += $soldQty;
            $litTotalReturnedQty += $returnedFromLit;
            $litTotalRemainsQty += $litRemains;
            $litTotalSalesValuation += $salesValue;
            $litTotalStockValuation += $litValuation;
        }

        // Available historical months for selector
        $monthsList = [];
        $current = Carbon::now()->startOfMonth();
        for ($i = 0; $i < 12; $i++) {
            $m = $current->copy()->subMonths($i);
            $monthsList[] = [
                'value' => $m->format('Y-m'),
                'label' => DateNumberHelper::translatedFormat($m, 'F Y'),
            ];
        }

        return [
            'month' => $month,
            'months_list' => $monthsList,
            'items' => $itemDetails,
            'categories' => array_values($categoriesMap),
            'store_summary' => [
                'received' => $storeTotalReceivedQty,
                'transferred' => $storeTotalTransferredQty,
                'returned' => $storeTotalReturnedQty,
                'remains' => $storeTotalRemainsQty,
                'valuation' => $storeTotalValuation,
            ],
            'lit_summary' => [
                'received' => $litTotalReceivedQty,
                'sold' => $litTotalSoldQty,
                'returned' => $litTotalReturnedQty,
                'remains' => $litTotalRemainsQty,
                'sales_valuation' => $litTotalSalesValuation,
                'stock_valuation' => $litTotalStockValuation,
            ],
            'grand_totals' => [
                'total_stock' => $storeTotalRemainsQty + $litTotalRemainsQty,
                'total_valuation' => $storeTotalValuation + $litTotalStockValuation,
                'total_sales_value' => $litTotalSalesValuation,
            ],
        ];
    }
}
