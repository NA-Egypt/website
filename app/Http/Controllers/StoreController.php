<?php

namespace App\Http\Controllers;

use App\Models\InventoryItem;
use App\Models\InventoryTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

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
            'description' => 'nullable|string',
            'selling_price' => 'required|numeric|min:0',
            'initial_store_quantity' => 'nullable|integer|min:0',
            'category' => 'required|string|in:' . implode(',', InventoryItem::CATEGORIES),
        ]);

        $item = InventoryItem::create([
            'name' => $fields['name'],
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

        $pdf = Pdf::loadView('store.pdf', compact('transactions', 'items', 'totalValuation'));
        return $pdf->download('inventory_report_' . date('Ymd_His') . '.pdf');
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

        $columns = ['ID', 'Date', 'Item Name', 'Transaction Type', 'Quantity', 'User', 'Notes'];

        $callback = function() use($transactions, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($transactions as $t) {
                fputcsv($file, [
                    $t->id,
                    $t->created_at->format('Y-m-d H:i:s'),
                    $t->item->name ?? 'Deleted Item',
                    ucwords(str_replace('_', ' ', $t->type)),
                    $t->quantity,
                    $t->user->name ?? 'System',
                    $t->notes ?? ''
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
