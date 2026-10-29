<?php

namespace App\Http\Controllers;

use App\Models\InventoryItem;
use App\Models\InventoryTransaction;
use App\Services\InventoryLedgerService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class LitController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:view lit inventory'),
        ];
    }

    public function index(Request $request)
    {
        $query = InventoryItem::with(['damagedTransactions.user']);

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

        $items = $query->orderBy('name')->get();
        $ledgerService = app(InventoryLedgerService::class);
        $salesByItem = $ledgerService->getMonthlySalesByItem(Carbon::now());
        $totalSoldThisMonth = array_sum($salesByItem);

        return view('lit.index', compact('items', 'salesByItem', 'totalSoldThisMonth'));
    }

    public function markDamaged(Request $request, InventoryItem $item)
    {
        $fields = $request->validate([
            'quantity' => 'required|integer|min:1',
            'notes' => 'required|string|max:500',
        ]);

        $qty = (int) $fields['quantity'];

        if ($item->lit_quantity < $qty) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => __('messages.insufficient_lit_stock'),
                ], 422);
            }
            return redirect()->route('lit.index')->with('error', __('messages.insufficient_lit_stock'));
        }

        $item->decrement('lit_quantity', $qty);
        $item->increment('damaged_quantity', $qty);

        InventoryTransaction::create([
            'inventory_item_id' => $item->id,
            'user_id' => Auth::id(),
            'type' => 'damaged',
            'source_location' => 'lit',
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

        return redirect()->route('lit.index')->with('success', __('messages.damaged_recorded_success'));
    }
}
