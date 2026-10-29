<?php

namespace App\Http\Controllers;

use App\Models\InventoryItem;
use App\Models\InventoryTransaction;
use App\Services\InventoryLedgerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\MpdfService;

use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class StoreController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:manage store'),
        ];
    }

    public function index(Request $request)
    {
        $query = InventoryItem::with(['damagedTransactions.user', 'lastActiveReceiveTransaction.user']);
        
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('name_en', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->boolean('low_stock')) {
            $query->where('store_quantity', '<=', 5);
        }

        $sortColumn = in_array($request->input('sort'), ['name', 'selling_price', 'store_quantity', 'lit_quantity', 'damaged_quantity']) ? $request->input('sort') : 'name';
        $sortDirection = strtolower($request->input('direction')) === 'desc' ? 'desc' : 'asc';

        $items = $query->orderBy($sortColumn, $sortDirection)->paginate(15)->withQueryString();
        $activeStocktaking = \App\Models\StocktakingSession::getActiveSession();

        $ledgerService = app(InventoryLedgerService::class);
        $allItemsQuery = InventoryItem::with(['damagedTransactions.user', 'lastActiveReceiveTransaction.user'])->orderBy('name');
        if ($request->filled('category')) {
            $allItemsQuery->where('category', $request->category);
        }

        $allItems = $allItemsQuery->get()->map(function ($item) use ($ledgerService) {
            return $ledgerService->formatItemForLiveState($item);
        });

        $lowStockCount = InventoryItem::where('store_quantity', '<=', 5)->count();
        $totalStoreStock = (int) InventoryItem::sum('store_quantity');
        $totalLitStock = (int) InventoryItem::sum('lit_quantity');
        $totalDamagedStock = (int) InventoryItem::sum('damaged_quantity');

        return view('store.index', compact(
            'items',
            'activeStocktaking',
            'allItems',
            'lowStockCount',
            'totalStoreStock',
            'totalLitStock',
            'totalDamagedStock'
        ));
    }

    public function liveState()
    {
        return response()->json([
            'success' => true,
            'live_state' => app(InventoryLedgerService::class)->getLiveInventoryState(),
        ]);
    }

    public function store(Request $request)
    {
        $fields = $request->validate([
            'name' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'selling_price' => 'required|numeric|min:0',
            'initial_store_quantity' => 'nullable|integer|min:0',
            'category' => 'required|string|in:' . implode(',', InventoryItem::CATEGORIES),
        ]);

        $item = InventoryItem::create([
            'name' => $fields['name'],
            'name_en' => $fields['name_en'] ?? null,
            'description' => $fields['description'] ?? null,
            'selling_price' => $fields['selling_price'],
            'store_quantity' => $fields['initial_store_quantity'] ?? 0,
            'damaged_quantity' => 0,
            'category' => $fields['category'],
        ]);

        if (($fields['initial_store_quantity'] ?? 0) > 0) {
            InventoryTransaction::create([
                'inventory_item_id' => $item->id,
                'user_id' => Auth::id(),
                'type' => 'receive',
                'source_location' => 'store',
                'quantity' => $fields['initial_store_quantity'],
                'notes' => __('messages.initial_stock_creation'),
                'status' => 'active',
            ]);
        }

        if ($request->wantsJson() || $request->ajax()) {
            $ledgerService = app(InventoryLedgerService::class);
            return response()->json([
                'success' => true,
                'message' => __('messages.item_created_success'),
                'item' => $ledgerService->formatItemForLiveState($item->fresh()),
                'live_state' => $ledgerService->getLiveInventoryState(),
            ], 201);
        }

        return redirect()->route('store.index')->with('success', __('messages.item_created_success'));
    }

    public function update(Request $request, InventoryItem $item)
    {
        $fields = $request->validate([
            'name' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'selling_price' => 'required|numeric|min:0',
            'category' => 'required|string|in:' . implode(',', InventoryItem::CATEGORIES),
        ]);

        $item->update($fields);

        if ($request->wantsJson() || $request->ajax()) {
            $ledgerService = app(InventoryLedgerService::class);
            return response()->json([
                'success' => true,
                'message' => __('messages.item_updated_success'),
                'item' => $ledgerService->formatItemForLiveState($item->fresh()),
                'live_state' => $ledgerService->getLiveInventoryState(),
            ]);
        }

        return redirect()->route('store.index')->with('success', __('messages.item_updated_success'));
    }

    public function inlineUpdate(Request $request, InventoryItem $item)
    {
        $user = Auth::user();
        $canEditPrice = $user->hasRole('super admin') || $user->hasRole('Lit User') || $user->hasRole('Committees') || $user->can('view lit inventory');

        $request->validate([
            'selling_price' => 'nullable|numeric|min:0',
            'receive_qty' => 'nullable|integer|min:0',
            'transfer_qty' => 'nullable|integer|min:0',
        ]);

        $messages = [];

        // 1. Update selling_price if provided and allowed
        if ($request->filled('selling_price')) {
            $newPrice = (float) $request->input('selling_price');
            if ($newPrice != (float) $item->selling_price) {
                if (!$canEditPrice) {
                    return response()->json([
                        'success' => false,
                        'message' => __('messages.unauthorized_price_edit')
                    ], 403);
                }
                $item->selling_price = $newPrice;
                $item->save();
                $messages[] = __('messages.price_updated');
            }
        }

        // 2. Process Receive stock
        $receiveQty = (int) $request->input('receive_qty', 0);
        if ($receiveQty > 0) {
            $item->increment('store_quantity', $receiveQty);
            InventoryTransaction::create([
                'inventory_item_id' => $item->id,
                'user_id' => Auth::id(),
                'type' => 'receive',
                'source_location' => 'store',
                'quantity' => $receiveQty,
                'notes' => __('messages.inline_receive'),
                'status' => 'active',
            ]);
            $messages[] = __('messages.stock_received');
        }

        // 3. Process Transfer stock to Lit
        $transferQty = (int) $request->input('transfer_qty', 0);
        if ($transferQty > 0) {
            $item->refresh();
            if ($item->store_quantity < $transferQty) {
                return response()->json([
                    'success' => false,
                    'message' => __('messages.insufficient_store_stock')
                ], 422);
            }
            $item->decrement('store_quantity', $transferQty);
            $item->increment('lit_quantity', $transferQty);
            InventoryTransaction::create([
                'inventory_item_id' => $item->id,
                'user_id' => Auth::id(),
                'type' => 'transfer_to_lit',
                'source_location' => 'store',
                'quantity' => $transferQty,
                'notes' => __('messages.inline_transfer'),
                'status' => 'active',
            ]);
            app(InventoryLedgerService::class)->createTransferSlip([
                [
                    'inventory_item_id' => $item->id,
                    'quantity' => $transferQty,
                    'unit_price' => $item->selling_price,
                ]
            ], Auth::id(), __('messages.inline_transfer'));
            $messages[] = __('messages.stock_transferred_to_lit');
        }

        $item->refresh();
        $ledgerService = app(InventoryLedgerService::class);
        $liveState = $ledgerService->getLiveInventoryState();

        return response()->json([
            'success' => true,
            'message' => count($messages) > 0 ? implode(', ', $messages) : __('messages.no_changes_made'),
            'item' => $ledgerService->formatItemForLiveState($item),
            'total_store_stock' => $liveState['totals']['store_stock'],
            'total_lit_stock' => $liveState['totals']['lit_stock'],
            'total_damaged_stock' => $liveState['totals']['damaged_stock'],
            'low_stock_count' => $liveState['totals']['low_stock_count'],
            'live_state' => $liveState,
        ]);
    }

    public function bulkInlineUpdate(Request $request)
    {
        $user = Auth::user();
        $canEditPrice = $user->hasRole('super admin') || $user->hasRole('Lit User') || $user->hasRole('Committees') || $user->can('view lit inventory');

        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|exists:inventory_items,id',
            'items.*.selling_price' => 'nullable|numeric|min:0',
            'items.*.receive_qty' => 'nullable|integer|min:0',
            'items.*.transfer_qty' => 'nullable|integer|min:0',
        ]);

        $updatedItems = [];
        $ledgerService = app(InventoryLedgerService::class);
        $slipItems = [];

        \DB::beginTransaction();
        try {
            foreach ($request->items as $row) {
                $item = InventoryItem::find($row['id']);
                if (!$item) continue;

                // Price update
                if (isset($row['selling_price']) && $row['selling_price'] !== null && (float)$row['selling_price'] != (float)$item->selling_price) {
                    if (!$canEditPrice) {
                        \DB::rollBack();
                        return response()->json([
                            'success' => false,
                            'message' => __('messages.unauthorized_price_edit_item', ['name' => $item->name])
                        ], 403);
                    }
                    $item->selling_price = (float)$row['selling_price'];
                    $item->save();
                }

                // Receive stock
                $receiveQty = (int)($row['receive_qty'] ?? 0);
                if ($receiveQty > 0) {
                    $item->increment('store_quantity', $receiveQty);
                    InventoryTransaction::create([
                        'inventory_item_id' => $item->id,
                        'user_id' => Auth::id(),
                        'type' => 'receive',
                        'source_location' => 'store',
                        'quantity' => $receiveQty,
                        'notes' => __('messages.bulk_inline_receive'),
                        'status' => 'active',
                    ]);
                }

                // Transfer stock
                $transferQty = (int)($row['transfer_qty'] ?? 0);
                if ($transferQty > 0) {
                    $item->refresh();
                    if ($item->store_quantity < $transferQty) {
                        \DB::rollBack();
                        return response()->json([
                            'success' => false,
                            'message' => __('messages.insufficient_store_stock_item', ['name' => $item->store_display_name])
                        ], 422);
                    }
                    $item->decrement('store_quantity', $transferQty);
                    $item->increment('lit_quantity', $transferQty);
                    InventoryTransaction::create([
                        'inventory_item_id' => $item->id,
                        'user_id' => Auth::id(),
                        'type' => 'transfer_to_lit',
                        'source_location' => 'store',
                        'quantity' => $transferQty,
                        'notes' => __('messages.bulk_inline_transfer'),
                        'status' => 'active',
                    ]);
                    $slipItems[] = [
                        'inventory_item_id' => $item->id,
                        'quantity' => $transferQty,
                        'unit_price' => $item->selling_price,
                    ];
                }

                $item->refresh();
                $updatedItems[] = $ledgerService->formatItemForLiveState($item);
            }

            if (!empty($slipItems)) {
                $ledgerService->createTransferSlip($slipItems, Auth::id(), __('messages.bulk_inline_transfer'));
            }

            \DB::commit();
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }

        $liveState = $ledgerService->getLiveInventoryState();

        return response()->json([
            'success' => true,
            'message' => __('messages.bulk_update_success'),
            'updated_items' => $updatedItems,
            'total_store_stock' => $liveState['totals']['store_stock'],
            'total_lit_stock' => $liveState['totals']['lit_stock'],
            'total_damaged_stock' => $liveState['totals']['damaged_stock'],
            'low_stock_count' => $liveState['totals']['low_stock_count'],
            'live_state' => $liveState,
        ]);
    }

    public function destroy(Request $request, InventoryItem $item)
    {
        $deletedId = $item->id;
        $item->delete();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('messages.item_deleted_success'),
                'deleted_id' => $deletedId,
                'live_state' => app(InventoryLedgerService::class)->getLiveInventoryState(),
            ]);
        }

        return redirect()->route('store.index')->with('success', __('messages.item_deleted_success'));
    }

    public function receive(Request $request, InventoryItem $item)
    {
        $fields = $request->validate([
            'quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string|max:255',
        ]);

        $item->increment('store_quantity', $fields['quantity']);

        $tx = InventoryTransaction::create([
            'inventory_item_id' => $item->id,
            'user_id' => Auth::id(),
            'type' => 'receive',
            'source_location' => 'store',
            'quantity' => $fields['quantity'],
            'notes' => $fields['notes'] ?? null,
            'status' => 'active',
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            $ledgerService = app(InventoryLedgerService::class);
            return response()->json([
                'success' => true,
                'message' => __('messages.stock_received_success'),
                'transaction_id' => $tx->id,
                'item' => $ledgerService->formatItemForLiveState($item->fresh()),
                'live_state' => $ledgerService->getLiveInventoryState(),
            ]);
        }

        return redirect()->route('store.index')->with('success', __('messages.stock_received_success'));
    }

    public function transfer(Request $request, InventoryItem $item)
    {
        $fields = $request->validate([
            'quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string|max:255',
        ]);

        if ($item->store_quantity < $fields['quantity']) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => __('messages.insufficient_store_stock'),
                ], 422);
            }
            return redirect()->route('store.index')->with('error', __('messages.insufficient_store_stock'));
        }

        $item->decrement('store_quantity', $fields['quantity']);
        $item->increment('lit_quantity', $fields['quantity']);

        InventoryTransaction::create([
            'inventory_item_id' => $item->id,
            'user_id' => Auth::id(),
            'type' => 'transfer_to_lit',
            'source_location' => 'store',
            'quantity' => $fields['quantity'],
            'notes' => $fields['notes'] ?? null,
            'status' => 'active',
        ]);

        $ledgerService = app(InventoryLedgerService::class);
        $ledgerService->createTransferSlip([
            [
                'inventory_item_id' => $item->id,
                'quantity' => $fields['quantity'],
                'unit_price' => $item->selling_price,
            ]
        ], Auth::id(), $fields['notes'] ?? null);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('messages.transfer_success'),
                'item' => $ledgerService->formatItemForLiveState($item->fresh()),
                'live_state' => $ledgerService->getLiveInventoryState(),
            ]);
        }

        return redirect()->route('store.index')->with('success', __('messages.transfer_success'));
    }

    public function returnStock(Request $request, InventoryItem $item)
    {
        $fields = $request->validate([
            'quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string|max:255',
        ]);

        if ($item->lit_quantity < $fields['quantity']) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => __('messages.insufficient_lit_stock'),
                ], 422);
            }
            return redirect()->route('store.index')->with('error', __('messages.insufficient_lit_stock'));
        }

        $item->decrement('lit_quantity', $fields['quantity']);
        $item->increment('store_quantity', $fields['quantity']);

        InventoryTransaction::create([
            'inventory_item_id' => $item->id,
            'user_id' => Auth::id(),
            'type' => 'return_from_lit',
            'source_location' => 'lit',
            'quantity' => $fields['quantity'],
            'notes' => $fields['notes'] ?? null,
            'status' => 'active',
        ]);

        $ledgerService = app(InventoryLedgerService::class);
        $ledgerService->createReturnSlip([
            [
                'inventory_item_id' => $item->id,
                'quantity' => $fields['quantity'],
                'unit_price' => $item->selling_price,
            ]
        ], Auth::id(), $fields['notes'] ?? null);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('messages.return_success'),
                'item' => $ledgerService->formatItemForLiveState($item->fresh()),
                'live_state' => $ledgerService->getLiveInventoryState(),
            ]);
        }

        return redirect()->route('store.index')->with('success', __('messages.return_success'));
    }

    public function markDamaged(Request $request, InventoryItem $item)
    {
        $fields = $request->validate([
            'quantity' => 'required|integer|min:1',
            'source' => 'nullable|string|in:store,lit',
            'notes' => 'required|string|max:500',
        ]);

        $source = $fields['source'] ?? 'store';
        $qty = (int) $fields['quantity'];

        if ($source === 'lit') {
            if ($item->lit_quantity < $qty) {
                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => __('messages.insufficient_lit_stock'),
                    ], 422);
                }
                return redirect()->route('store.index')->with('error', __('messages.insufficient_lit_stock'));
            }
            $item->decrement('lit_quantity', $qty);
        } else {
            if ($item->store_quantity < $qty) {
                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => __('messages.insufficient_store_stock'),
                    ], 422);
                }
                return redirect()->route('store.index')->with('error', __('messages.insufficient_store_stock'));
            }
            $item->decrement('store_quantity', $qty);
        }

        $item->increment('damaged_quantity', $qty);

        InventoryTransaction::create([
            'inventory_item_id' => $item->id,
            'user_id' => Auth::id(),
            'type' => 'damaged',
            'source_location' => $source,
            'quantity' => $qty,
            'notes' => $fields['notes'],
            'status' => 'active',
        ]);

        $ledgerService = app(InventoryLedgerService::class);
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('messages.damaged_recorded_success'),
                'item' => $ledgerService->formatItemForLiveState($item->fresh()),
                'live_state' => $ledgerService->getLiveInventoryState(),
            ]);
        }

        return redirect()->route('store.index')->with('success', __('messages.damaged_recorded_success'));
    }

    public function cancelWrongReceive(Request $request, InventoryItem $item)
    {
        $fields = $request->validate([
            'transaction_id' => 'nullable|integer|exists:inventory_transactions,id',
            'notes' => 'nullable|string|max:500',
        ]);

        if (!empty($fields['transaction_id'])) {
            $tx = InventoryTransaction::where('id', $fields['transaction_id'])
                ->where('inventory_item_id', $item->id)
                ->where('type', 'receive')
                ->first();
        } else {
            $tx = $item->lastActiveReceiveTransaction()->first();
        }

        if (!$tx || $tx->status === 'cancelled') {
            $errMsg = __('messages.no_active_receive_to_cancel');
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $errMsg,
                ], 422);
            }
            return redirect()->back()->with('error', $errMsg);
        }

        if ($item->store_quantity < $tx->quantity) {
            $errMsg = __('messages.cannot_cancel_receive_insufficient_store', ['qty' => $tx->quantity, 'available' => $item->store_quantity]);
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $errMsg,
                ], 422);
            }
            return redirect()->back()->with('error', $errMsg);
        }

        $reason = !empty($fields['notes']) ? $fields['notes'] : __('messages.wrong_receive_entry_default_note');

        \DB::beginTransaction();
        try {
            $item->decrement('store_quantity', $tx->quantity);

            $tx->update([
                'status' => 'cancelled',
                'cancelled_by' => Auth::id(),
                'cancelled_at' => now(),
            ]);

            $cancelLog = InventoryTransaction::create([
                'inventory_item_id' => $item->id,
                'user_id' => Auth::id(),
                'type' => 'receive_cancelled',
                'source_location' => 'store',
                'quantity' => $tx->quantity,
                'notes' => __('messages.wrong_receive_cancelled_log', [
                    'id' => $tx->id,
                    'qty' => $tx->quantity,
                    'notes' => $reason,
                ]),
                'status' => 'active',
                'reference_transaction_id' => $tx->id,
            ]);

            \DB::commit();
        } catch (\Exception $e) {
            \DB::rollBack();
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', $e->getMessage());
        }

        $ledgerService = app(InventoryLedgerService::class);
        $msg = __('messages.wrong_receive_cancelled_success', ['qty' => $tx->quantity]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $msg,
                'cancelled_transaction_id' => $tx->id,
                'cancellation_log_id' => $cancelLog->id,
                'reversed_quantity' => $tx->quantity,
                'start_new_receive_item_id' => $item->id,
                'item' => $ledgerService->formatItemForLiveState($item->fresh()),
                'live_state' => $ledgerService->getLiveInventoryState(),
            ]);
        }

        return redirect()->route('store.index')
            ->with('success', $msg)
            ->with('start_receive_item_id', $item->id);
    }

    public function reports(Request $request)
    {
        $query = InventoryTransaction::with(['item', 'user']);

        if ($request->filled('type')) {
            if ($request->type === 'transfer_to_lit') {
                $query->whereIn('type', ['transfer_to_lit', 'transfer']);
            } else {
                $query->where('type', $request->type);
            }
        }

        if ($request->filled('item_id')) {
            $query->where('inventory_item_id', $request->item_id);
        }

        if ($request->filled('category')) {
            $query->whereHas('item', function ($q) use ($request) {
                $q->where('category', $request->category);
            });
        }

        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $transactions = $query->orderBy('created_at', 'desc')->get();
        
        $itemsQuery = InventoryItem::query();
        if ($request->filled('category')) {
            $itemsQuery->where('category', $request->category);
        }
        if ($request->filled('item_id')) {
            $itemsQuery->where('id', $request->item_id);
        }
        $items = $itemsQuery->orderBy('name')->get();

        // Calculate transaction report summary metrics
        $totalTransactions = $transactions->count();
        $totalReceivedQty = $transactions
            ->where('type', 'receive')
            ->filter(fn ($t) => ($t->status ?? 'active') !== 'cancelled')
            ->sum('quantity');
        $totalTransferredQty = $transactions->whereIn('type', ['transfer_to_lit', 'transfer'])->sum('quantity');
        $totalDamagedQty = $transactions->where('type', 'damaged')->sum('quantity');
        $totalTransactionValuation = $transactions
            ->filter(fn ($t) => ($t->status ?? 'active') !== 'cancelled')
            ->sum(function ($t) {
                return $t->quantity * ($t->item->selling_price ?? 0);
            });

        // Item stock metrics
        $totalItems = $items->count();
        $totalStoreStock = $items->sum('store_quantity');
        $totalLitStock = $items->sum('lit_quantity');
        $totalDamagedStock = $items->sum('damaged_quantity');
        $totalValuation = $items->sum(function ($item) {
            return ($item->store_quantity + $item->lit_quantity) * $item->selling_price;
        });

        $totalReturnedQty = $transactions->where('type', 'return_from_lit')->sum('quantity');

        // Stock distribution by category
        $categoryBreakdown = $items->groupBy('category')->map(function ($catItems, $catName) {
            $store = $catItems->sum('store_quantity');
            $lit = $catItems->sum('lit_quantity');
            $total = $store + $lit;
            $valuation = $catItems->sum(function($item) {
                return ($item->store_quantity + $item->lit_quantity) * $item->selling_price;
            });
            return [
                'name' => $catName,
                'count' => $catItems->count(),
                'store_quantity' => $store,
                'lit_quantity' => $lit,
                'total_quantity' => $total,
                'valuation' => $valuation,
            ];
        });

        return view('store.reports', compact(
            'transactions', 'items', 'totalItems', 'totalStoreStock', 'totalLitStock', 'totalDamagedStock', 'totalValuation',
            'totalTransactions', 'totalReceivedQty', 'totalTransferredQty', 'totalReturnedQty', 'totalDamagedQty', 'totalTransactionValuation',
            'categoryBreakdown'
        ));
    }

    public function exportPdf(Request $request)
    {
        $query = InventoryTransaction::with(['item', 'user']);

        if ($request->filled('type')) {
            if ($request->type === 'transfer_to_lit') {
                $query->whereIn('type', ['transfer_to_lit', 'transfer']);
            } else {
                $query->where('type', $request->type);
            }
        }

        if ($request->filled('item_id')) {
            $query->where('inventory_item_id', $request->item_id);
        }

        if ($request->filled('category')) {
            $query->whereHas('item', function ($q) use ($request) {
                $q->where('category', $request->category);
            });
        }

        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $transactions = $query->orderBy('created_at', 'desc')->get();
        
        $itemsQuery = InventoryItem::query();
        if ($request->filled('category')) {
            $itemsQuery->where('category', $request->category);
        }
        if ($request->filled('item_id')) {
            $itemsQuery->where('id', $request->item_id);
        }
        $items = $itemsQuery->orderBy('name')->get();
        
        $totalValuation = $items->sum(function ($item) {
            return ($item->store_quantity + $item->lit_quantity) * $item->selling_price;
        });

        $totalQty = $transactions->sum('quantity');
        $totalValue = $transactions->sum(function ($t) {
            return $t->quantity * ($t->item->selling_price ?? 0);
        });

        $mpdf = MpdfService::create();
        $html = view('store.pdf', compact('transactions', 'items', 'totalValuation', 'totalQty', 'totalValue'))->render();
        $mpdf->WriteHTML($html);

        $filename = 'inventory_report_' . date('Ymd_His') . '.pdf';
        return response($mpdf->Output($filename, 'S'), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    public function exportCsv(Request $request)
    {
        $query = InventoryTransaction::with(['item', 'user']);

        if ($request->filled('type')) {
            if ($request->type === 'transfer_to_lit') {
                $query->whereIn('type', ['transfer_to_lit', 'transfer']);
            } else {
                $query->where('type', $request->type);
            }
        }

        if ($request->filled('item_id')) {
            $query->where('inventory_item_id', $request->item_id);
        }

        if ($request->filled('category')) {
            $query->whereHas('item', function ($q) use ($request) {
                $q->where('category', $request->category);
            });
        }

        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $transactions = $query->orderBy('created_at', 'desc')->get();

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=inventory_transactions_" . date('Ymd_His') . ".csv",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['ID', 'Date', 'Item Name', 'Transaction Type', 'Quantity', 'Unit Price (EGP)', 'Total Value (EGP)', 'User', 'Notes'];

        $totalQty = $transactions->sum('quantity');
        $totalValue = $transactions->sum(function ($t) {
            return $t->quantity * ($t->item->selling_price ?? 0);
        });

        $callback = function() use($transactions, $columns, $totalQty, $totalValue) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($transactions as $t) {
                fputcsv($file, [
                    $t->id,
                    $t->created_at->format('Y-m-d H:i:s'),
                    $t->item->store_display_name ?? 'Deleted Item',
                    ucwords(str_replace('_', ' ', $t->type)),
                    $t->quantity,
                    $t->item->selling_price ?? 0,
                    $t->quantity * ($t->item->selling_price ?? 0),
                    $t->user->name ?? 'System',
                    $t->notes ?? ''
                ]);
            }

            fputcsv($file, [
                'Total',
                '',
                '',
                '',
                $totalQty,
                '',
                $totalValue,
                '',
                ''
            ]);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function bulkReceive(Request $request)
    {
        $fields = $request->validate([
            'quantities' => 'required|array',
            'quantities.*' => 'nullable|integer|min:0',
            'notes' => 'nullable|string|max:255',
        ]);

        $processedCount = 0;
        foreach ($fields['quantities'] as $itemId => $qty) {
            if ($qty > 0) {
                $item = InventoryItem::find($itemId);
                if ($item) {
                    $item->increment('store_quantity', $qty);
                    InventoryTransaction::create([
                        'inventory_item_id' => $item->id,
                        'user_id' => Auth::id(),
                        'type' => 'receive',
                        'source_location' => 'store',
                        'quantity' => $qty,
                        'notes' => $fields['notes'] ?? null,
                        'status' => 'active',
                    ]);
                    $processedCount++;
                }
            }
        }

        if ($processedCount === 0) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => __('messages.no_items_selected')], 422);
            }
            return redirect()->route('store.index')->with('error', __('messages.no_items_selected'));
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('messages.bulk_operation_success'),
                'live_state' => app(InventoryLedgerService::class)->getLiveInventoryState(),
            ]);
        }

        return redirect()->route('store.index')->with('success', __('messages.bulk_operation_success'));
    }

    public function bulkTransfer(Request $request)
    {
        $fields = $request->validate([
            'quantities' => 'required|array',
            'quantities.*' => 'nullable|integer|min:0',
            'notes' => 'nullable|string|max:255',
        ]);

        // First validate store balance for all items to transfer
        foreach ($fields['quantities'] as $itemId => $qty) {
            if ($qty > 0) {
                $item = InventoryItem::find($itemId);
                if (!$item || $item->store_quantity < $qty) {
                    if ($request->wantsJson() || $request->ajax()) {
                        return response()->json(['success' => false, 'message' => __('messages.insufficient_store_stock')], 422);
                    }
                    return redirect()->route('store.index')->with('error', __('messages.insufficient_store_stock'));
                }
            }
        }

        $processedCount = 0;
        $slipItems = [];
        foreach ($fields['quantities'] as $itemId => $qty) {
            if ($qty > 0) {
                $item = InventoryItem::find($itemId);
                if ($item) {
                    $item->decrement('store_quantity', $qty);
                    $item->increment('lit_quantity', $qty);
                    InventoryTransaction::create([
                        'inventory_item_id' => $item->id,
                        'user_id' => Auth::id(),
                        'type' => 'transfer_to_lit',
                        'source_location' => 'store',
                        'quantity' => $qty,
                        'notes' => $fields['notes'] ?? null,
                        'status' => 'active',
                    ]);
                    $slipItems[] = [
                        'inventory_item_id' => $item->id,
                        'quantity' => $qty,
                        'unit_price' => $item->selling_price,
                    ];
                    $processedCount++;
                }
            }
        }

        if ($processedCount === 0) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => __('messages.no_items_selected')], 422);
            }
            return redirect()->route('store.index')->with('error', __('messages.no_items_selected'));
        }

        $ledgerService = app(InventoryLedgerService::class);
        if (!empty($slipItems)) {
            $ledgerService->createTransferSlip($slipItems, Auth::id(), $fields['notes'] ?? null);
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('messages.bulk_operation_success'),
                'live_state' => $ledgerService->getLiveInventoryState(),
            ]);
        }

        return redirect()->route('store.index')->with('success', __('messages.bulk_operation_success'));
    }

    public function bulkReturn(Request $request)
    {
        $fields = $request->validate([
            'quantities' => 'required|array',
            'quantities.*' => 'nullable|integer|min:0',
            'notes' => 'nullable|string|max:255',
        ]);

        // Validate lit balance
        foreach ($fields['quantities'] as $itemId => $qty) {
            if ($qty > 0) {
                $item = InventoryItem::find($itemId);
                if (!$item || $item->lit_quantity < $qty) {
                    if ($request->wantsJson() || $request->ajax()) {
                        return response()->json(['success' => false, 'message' => __('messages.insufficient_lit_stock')], 422);
                    }
                    return redirect()->route('store.index')->with('error', __('messages.insufficient_lit_stock'));
                }
            }
        }

        $processedCount = 0;
        $slipItems = [];
        foreach ($fields['quantities'] as $itemId => $qty) {
            if ($qty > 0) {
                $item = InventoryItem::find($itemId);
                if ($item) {
                    $item->decrement('lit_quantity', $qty);
                    $item->increment('store_quantity', $qty);
                    InventoryTransaction::create([
                        'inventory_item_id' => $item->id,
                        'user_id' => Auth::id(),
                        'type' => 'return_from_lit',
                        'source_location' => 'lit',
                        'quantity' => $qty,
                        'notes' => $fields['notes'] ?? null,
                        'status' => 'active',
                    ]);
                    $slipItems[] = [
                        'inventory_item_id' => $item->id,
                        'quantity' => $qty,
                        'unit_price' => $item->selling_price,
                    ];
                    $processedCount++;
                }
            }
        }

        if ($processedCount === 0) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => __('messages.no_items_selected')], 422);
            }
            return redirect()->route('store.index')->with('error', __('messages.no_items_selected'));
        }

        $ledgerService = app(InventoryLedgerService::class);
        if (!empty($slipItems)) {
            $ledgerService->createReturnSlip($slipItems, Auth::id(), $fields['notes'] ?? null);
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('messages.bulk_operation_success'),
                'live_state' => $ledgerService->getLiveInventoryState(),
            ]);
        }

        return redirect()->route('store.index')->with('success', __('messages.bulk_operation_success'));
    }

    public function bulkDestroy(Request $request)
    {
        $fields = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:inventory_items,id',
        ]);

        InventoryItem::whereIn('id', $fields['ids'])->delete();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('messages.item_deleted_success'),
                'deleted_ids' => $fields['ids'],
                'live_state' => app(InventoryLedgerService::class)->getLiveInventoryState(),
            ]);
        }

        return redirect()->route('store.index')->with('success', __('messages.item_deleted_success'));
    }

    public function batchMovement(Request $request)
    {
        $fields = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|exists:inventory_items,id',
            'items.*.type' => 'required|string|in:receive,transfer_to_lit,return_from_lit,damaged',
            'items.*.quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string|max:255',
        ]);

        $batchNotes = $fields['notes'] ?? null;
        $processedCount = 0;
        $transferSlipItems = [];
        $returnSlipItems = [];

        \DB::beginTransaction();
        try {
            foreach ($fields['items'] as $row) {
                $item = InventoryItem::find($row['id']);
                if (!$item) continue;

                $qty = (int) $row['quantity'];
                $type = $row['type'];

                if ($type === 'receive') {
                    $item->increment('store_quantity', $qty);
                } elseif ($type === 'transfer_to_lit') {
                    if ($item->store_quantity < $qty) {
                        \DB::rollBack();
                        $errMsg = __('messages.insufficient_store_stock_item', ['name' => $item->store_display_name]);
                        if ($request->wantsJson() || $request->ajax()) {
                            return response()->json(['success' => false, 'message' => $errMsg], 422);
                        }
                        return redirect()->route('store.index')->with('error', $errMsg);
                    }
                    $item->decrement('store_quantity', $qty);
                    $item->increment('lit_quantity', $qty);
                    $transferSlipItems[] = [
                        'inventory_item_id' => $item->id,
                        'quantity' => $qty,
                        'unit_price' => $item->selling_price,
                    ];
                } elseif ($type === 'return_from_lit') {
                    if ($item->lit_quantity < $qty) {
                        \DB::rollBack();
                        $errMsg = __('messages.insufficient_lit_stock_item', ['name' => $item->store_display_name]);
                        if ($request->wantsJson() || $request->ajax()) {
                            return response()->json(['success' => false, 'message' => $errMsg], 422);
                        }
                        return redirect()->route('store.index')->with('error', $errMsg);
                    }
                    $item->decrement('lit_quantity', $qty);
                    $item->increment('store_quantity', $qty);
                    $returnSlipItems[] = [
                        'inventory_item_id' => $item->id,
                        'quantity' => $qty,
                        'unit_price' => $item->selling_price,
                    ];
                } elseif ($type === 'damaged') {
                    if ($item->store_quantity < $qty) {
                        \DB::rollBack();
                        $errMsg = __('messages.insufficient_store_stock_item', ['name' => $item->store_display_name]);
                        if ($request->wantsJson() || $request->ajax()) {
                            return response()->json(['success' => false, 'message' => $errMsg], 422);
                        }
                        return redirect()->route('store.index')->with('error', $errMsg);
                    }
                    $item->decrement('store_quantity', $qty);
                    $item->increment('damaged_quantity', $qty);
                }

                InventoryTransaction::create([
                    'inventory_item_id' => $item->id,
                    'user_id' => Auth::id(),
                    'type' => $type,
                    'source_location' => $type === 'return_from_lit' ? 'lit' : 'store',
                    'quantity' => $qty,
                    'notes' => $batchNotes ?: __('messages.batch_movement_transaction'),
                    'status' => 'active',
                ]);

                $processedCount++;
            }

            $ledgerService = app(InventoryLedgerService::class);
            if (!empty($transferSlipItems)) {
                $ledgerService->createTransferSlip($transferSlipItems, Auth::id(), $batchNotes);
            }
            if (!empty($returnSlipItems)) {
                $ledgerService->createReturnSlip($returnSlipItems, Auth::id(), $batchNotes);
            }

            \DB::commit();
        } catch (\Exception $e) {
            \DB::rollBack();
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return redirect()->route('store.index')->with('error', $e->getMessage());
        }

        $successMsg = __('messages.batch_movement_success', ['count' => $processedCount]);
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $successMsg,
                'live_state' => app(InventoryLedgerService::class)->getLiveInventoryState(),
            ]);
        }

        return redirect()->route('store.index')->with('success', $successMsg);
    }
}
