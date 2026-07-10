<?php

namespace App\Http\Controllers;

use App\Models\InventoryItem;
use App\Models\InventoryTransaction;
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
        $query = InventoryItem::query();
        
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

        $items = $query->orderBy('name')->paginate(15)->withQueryString();

        return view('store.index', compact('items'));
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
            'category' => $fields['category'],
        ]);

        if (($fields['initial_store_quantity'] ?? 0) > 0) {
            InventoryTransaction::create([
                'inventory_item_id' => $item->id,
                'user_id' => Auth::id(),
                'type' => 'receive',
                'quantity' => $fields['initial_store_quantity'],
                'notes' => __('messages.initial_stock_creation'),
            ]);
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

        return redirect()->route('store.index')->with('success', __('messages.item_updated_success'));
    }

    public function destroy(InventoryItem $item)
    {
        $item->delete();
        return redirect()->route('store.index')->with('success', __('messages.item_deleted_success'));
    }

    public function receive(Request $request, InventoryItem $item)
    {
        $fields = $request->validate([
            'quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string|max:255',
        ]);

        $item->increment('store_quantity', $fields['quantity']);

        InventoryTransaction::create([
            'inventory_item_id' => $item->id,
            'user_id' => Auth::id(),
            'type' => 'receive',
            'quantity' => $fields['quantity'],
            'notes' => $fields['notes'] ?? null,
        ]);

        return redirect()->route('store.index')->with('success', __('messages.stock_received_success'));
    }

    public function transfer(Request $request, InventoryItem $item)
    {
        $fields = $request->validate([
            'quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string|max:255',
        ]);

        if ($item->store_quantity < $fields['quantity']) {
            return redirect()->route('store.index')->with('error', __('messages.insufficient_store_stock'));
        }

        $item->decrement('store_quantity', $fields['quantity']);
        $item->increment('lit_quantity', $fields['quantity']);

        InventoryTransaction::create([
            'inventory_item_id' => $item->id,
            'user_id' => Auth::id(),
            'type' => 'transfer_to_lit',
            'quantity' => $fields['quantity'],
            'notes' => $fields['notes'] ?? null,
        ]);

        return redirect()->route('store.index')->with('success', __('messages.transfer_success'));
    }

    public function returnStock(Request $request, InventoryItem $item)
    {
        $fields = $request->validate([
            'quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string|max:255',
        ]);

        if ($item->lit_quantity < $fields['quantity']) {
            return redirect()->route('store.index')->with('error', __('messages.insufficient_lit_stock'));
        }

        $item->decrement('lit_quantity', $fields['quantity']);
        $item->increment('store_quantity', $fields['quantity']);

        InventoryTransaction::create([
            'inventory_item_id' => $item->id,
            'user_id' => Auth::id(),
            'type' => 'return_from_lit',
            'quantity' => $fields['quantity'],
            'notes' => $fields['notes'] ?? null,
        ]);

        return redirect()->route('store.index')->with('success', __('messages.return_success'));
    }

    public function reports(Request $request)
    {
        $query = InventoryTransaction::with(['item', 'user']);

        if ($request->filled('type')) {
            $query->where('type', $request->type);
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
        $items = $itemsQuery->orderBy('name')->get();

        // Calculate summary metrics
        $totalItems = $items->count();
        $totalStoreStock = $items->sum('store_quantity');
        $totalLitStock = $items->sum('lit_quantity');
        $totalValuation = $items->sum(function ($item) {
            return ($item->store_quantity + $item->lit_quantity) * $item->selling_price;
        });

        return view('store.reports', compact('transactions', 'items', 'totalItems', 'totalStoreStock', 'totalLitStock', 'totalValuation'));
    }

    public function exportPdf(Request $request)
    {
        $query = InventoryTransaction::with(['item', 'user']);

        if ($request->filled('type')) {
            $query->where('type', $request->type);
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
            $query->where('type', $request->type);
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
                        'quantity' => $qty,
                        'notes' => $fields['notes'] ?? null,
                    ]);
                    $processedCount++;
                }
            }
        }

        if ($processedCount === 0) {
            return redirect()->route('store.index')->with('error', __('messages.no_items_selected'));
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
                    return redirect()->route('store.index')->with('error', __('messages.insufficient_store_stock'));
                }
            }
        }

        $processedCount = 0;
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
                        'quantity' => $qty,
                        'notes' => $fields['notes'] ?? null,
                    ]);
                    $processedCount++;
                }
            }
        }

        if ($processedCount === 0) {
            return redirect()->route('store.index')->with('error', __('messages.no_items_selected'));
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
                    return redirect()->route('store.index')->with('error', __('messages.insufficient_lit_stock'));
                }
            }
        }

        $processedCount = 0;
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
                        'quantity' => $qty,
                        'notes' => $fields['notes'] ?? null,
                    ]);
                    $processedCount++;
                }
            }
        }

        if ($processedCount === 0) {
            return redirect()->route('store.index')->with('error', __('messages.no_items_selected'));
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

        return redirect()->route('store.index')->with('success', __('messages.item_deleted_success'));
    }
}
