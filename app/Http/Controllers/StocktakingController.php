<?php

namespace App\Http\Controllers;

use App\Models\InventoryItem;
use App\Models\InventoryTransaction;
use App\Models\StocktakingSession;
use App\Models\StocktakingItem;
use App\Services\MpdfService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class StocktakingController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:manage store'),
        ];
    }

    public function index()
    {
        $activeSession = StocktakingSession::getActiveSession();
        $sessions = StocktakingSession::with(['user', 'adjustedByUser'])
            ->withCount('items')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('store.stocktaking.index', compact('activeSession', 'sessions'));
    }

    public function start(Request $request)
    {
        if (StocktakingSession::getActiveSession()) {
            return redirect()->route('store.stocktaking.index')->with('error', __('messages.active_stocktaking_exists'));
        }

        $request->validate([
            'notes' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            $session = StocktakingSession::create([
                'user_id' => Auth::id(),
                'status' => 'in_progress',
                'notes' => $request->notes,
                'started_at' => now(),
            ]);

            // Snapshot all existing inventory items
            $items = InventoryItem::all();
            foreach ($items as $item) {
                StocktakingItem::create([
                    'stocktaking_session_id' => $session->id,
                    'inventory_item_id' => $item->id,
                    'system_store_qty' => $item->store_quantity,
                    'system_lit_qty' => $item->lit_quantity,
                    'counted_store_qty' => null,
                    'counted_lit_qty' => null,
                    'store_variance' => 0,
                    'lit_variance' => 0,
                    'unit_price' => $item->selling_price,
                    'variance_value' => 0,
                ]);
            }

            DB::commit();
            return redirect()->route('store.stocktaking.count', $session->id)
                ->with('success', __('messages.stocktaking_started_success'));
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('store.stocktaking.index')
                ->with('error', $e->getMessage());
        }
    }

    public function count(StocktakingSession $session)
    {
        if (!$session->isInProgress()) {
            return redirect()->route('store.stocktaking.show', $session->id);
        }

        $items = StocktakingItem::with('inventoryItem')
            ->where('stocktaking_session_id', $session->id)
            ->get();

        return view('store.stocktaking.count', compact('session', 'items'));
    }

    public function updateCount(Request $request, StocktakingSession $session)
    {
        if (!$session->isInProgress()) {
            return redirect()->route('store.stocktaking.show', $session->id)->with('error', __('messages.session_not_editable'));
        }

        $request->validate([
            'counts' => 'required|array',
            'counts.*.counted_store_qty' => 'nullable|integer|min:0',
            'counts.*.counted_lit_qty' => 'nullable|integer|min:0',
        ]);

        DB::beginTransaction();
        try {
            foreach ($request->counts as $itemId => $countData) {
                $stocktakingItem = StocktakingItem::where('stocktaking_session_id', $session->id)
                    ->where('id', $itemId)
                    ->first();

                if ($stocktakingItem) {
                    if (array_key_exists('counted_store_qty', $countData) && $countData['counted_store_qty'] !== null) {
                        $stocktakingItem->counted_store_qty = (int)$countData['counted_store_qty'];
                    }
                    if (array_key_exists('counted_lit_qty', $countData) && $countData['counted_lit_qty'] !== null) {
                        $stocktakingItem->counted_lit_qty = (int)$countData['counted_lit_qty'];
                    }
                    $stocktakingItem->calculateVariances();
                    $stocktakingItem->save();
                }
            }

            DB::commit();

            if ($request->has('complete_session')) {
                $session->status = 'completed';
                $session->completed_at = now();
                $session->save();

                return redirect()->route('store.stocktaking.show', $session->id)
                    ->with('success', __('messages.stocktaking_completed_success'));
            }

            return redirect()->back()->with('success', __('messages.counts_saved_success'));
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function show(StocktakingSession $session)
    {
        $items = StocktakingItem::with('inventoryItem')
            ->where('stocktaking_session_id', $session->id)
            ->get();

        $totalSystemQty = $items->sum(fn($i) => $i->system_store_qty + $i->system_lit_qty);
        $totalCountedQty = $items->sum(fn($i) => ($i->counted_store_qty ?? $i->system_store_qty) + ($i->counted_lit_qty ?? $i->system_lit_qty));
        $totalVarianceQty = $items->sum(fn($i) => $i->store_variance + $i->lit_variance);
        $totalVarianceValue = $items->sum('variance_value');

        return view('store.stocktaking.show', compact('session', 'items', 'totalSystemQty', 'totalCountedQty', 'totalVarianceQty', 'totalVarianceValue'));
    }

    public function applyAdjustments(StocktakingSession $session)
    {
        if ($session->isAdjusted()) {
            return redirect()->route('store.stocktaking.show', $session->id)->with('error', __('messages.already_adjusted'));
        }

        DB::beginTransaction();
        try {
            $items = StocktakingItem::where('stocktaking_session_id', $session->id)->get();

            foreach ($items as $sItem) {
                $invItem = InventoryItem::find($sItem->inventory_item_id);
                if (!$invItem) continue;

                $newStoreQty = $sItem->counted_store_qty ?? $sItem->system_store_qty;
                $newLitQty = $sItem->counted_lit_qty ?? $sItem->system_lit_qty;

                // Log adjustment transactions if variance exists
                if ($sItem->store_variance != 0) {
                    InventoryTransaction::create([
                        'inventory_item_id' => $invItem->id,
                        'user_id' => Auth::id(),
                        'type' => 'stocktaking_adjustment',
                        'quantity' => abs($sItem->store_variance),
                        'notes' => __('messages.stocktaking_adj_notes', [
                            'session' => $session->id,
                            'location' => __('messages.store_qty'),
                            'diff' => $sItem->store_variance > 0 ? "+{$sItem->store_variance}" : "{$sItem->store_variance}"
                        ]),
                    ]);
                }

                if ($sItem->lit_variance != 0) {
                    InventoryTransaction::create([
                        'inventory_item_id' => $invItem->id,
                        'user_id' => Auth::id(),
                        'type' => 'stocktaking_adjustment',
                        'quantity' => abs($sItem->lit_variance),
                        'notes' => __('messages.stocktaking_adj_notes', [
                            'session' => $session->id,
                            'location' => __('messages.lit_qty'),
                            'diff' => $sItem->lit_variance > 0 ? "+{$sItem->lit_variance}" : "{$sItem->lit_variance}"
                        ]),
                    ]);
                }

                // Update inventory balances
                $invItem->store_quantity = $newStoreQty;
                $invItem->lit_quantity = $newLitQty;
                $invItem->save();
            }

            $session->status = 'adjusted';
            $session->adjusted_at = now();
            $session->adjusted_by = Auth::id();
            $session->save();

            DB::commit();

            return redirect()->route('store.stocktaking.show', $session->id)
                ->with('success', __('messages.adjustments_applied_success'));
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('store.stocktaking.show', $session->id)
                ->with('error', $e->getMessage());
        }
    }

    public function exportPdf(StocktakingSession $session)
    {
        $items = StocktakingItem::with('inventoryItem')
            ->where('stocktaking_session_id', $session->id)
            ->get();

        $totalSystemQty = $items->sum(fn($i) => $i->system_store_qty + $i->system_lit_qty);
        $totalCountedQty = $items->sum(fn($i) => ($i->counted_store_qty ?? $i->system_store_qty) + ($i->counted_lit_qty ?? $i->system_lit_qty));
        $totalVarianceQty = $items->sum(fn($i) => $i->store_variance + $i->lit_variance);
        $totalVarianceValue = $items->sum('variance_value');

        $html = view('store.stocktaking.pdf', compact('session', 'items', 'totalSystemQty', 'totalCountedQty', 'totalVarianceQty', 'totalVarianceValue'))->render();

        $pdfService = new MpdfService();
        return $pdfService->generatePdfResponse($html, "Stocktaking_Report_Session_{$session->id}.pdf");
    }

    public function exportCsv(StocktakingSession $session)
    {
        $items = StocktakingItem::with('inventoryItem')
            ->where('stocktaking_session_id', $session->id)
            ->get();

        $fileName = "Stocktaking_Report_Session_{$session->id}.csv";

        $headers = [
            "Content-type" => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function () use ($items) {
            $file = fopen('php://output', 'w');
            fputs($file, "\xEF\xBB\xBF"); // UTF-8 BOM

            fputcsv($file, [
                __('messages.id'),
                __('messages.item_name'),
                __('messages.Category'),
                __('messages.system_store_qty'),
                __('messages.counted_store_qty'),
                __('messages.store_variance'),
                __('messages.system_lit_qty'),
                __('messages.counted_lit_qty'),
                __('messages.lit_variance'),
                __('messages.unit_price'),
                __('messages.variance_value'),
            ]);

            foreach ($items as $item) {
                fputcsv($file, [
                    $item->inventory_item_id,
                    $item->inventoryItem->store_display_name ?? 'Item #' . $item->inventory_item_id,
                    $item->inventoryItem->category ?? '-',
                    $item->system_store_qty,
                    $item->counted_store_qty ?? $item->system_store_qty,
                    $item->store_variance,
                    $item->system_lit_qty,
                    $item->counted_lit_qty ?? $item->system_lit_qty,
                    $item->lit_variance,
                    number_format($item->unit_price, 2, '.', ''),
                    number_format($item->variance_value, 2, '.', ''),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
