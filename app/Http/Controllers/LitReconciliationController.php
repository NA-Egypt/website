<?php

namespace App\Http\Controllers;

use App\Models\InventoryItem;
use App\Models\InventoryTransaction;
use App\Services\InventoryLedgerService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class LitReconciliationController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('auth'),
        ];
    }

    public function index(Request $request, InventoryLedgerService $ledgerService)
    {
        $user = Auth::user();
        if (!$user->can('view lit inventory') && !$user->hasRole('super admin') && !$user->hasRole('Lit User') && !$user->hasRole('Committees')) {
            abort(403, 'Unauthorized');
        }

        $monthStr = $request->input('month', Carbon::now()->format('Y-m'));
        $month = Carbon::parse($monthStr . '-01')->startOfMonth();

        $data = $ledgerService->getReconciliationData($month);

        // Historical months list
        $monthsList = [];
        $current = Carbon::now()->startOfMonth();
        for ($i = 0; $i < 12; $i++) {
            $m = $current->copy()->subMonths($i);
            $monthsList[] = [
                'value' => $m->format('Y-m'),
                'label' => \App\Services\DateNumberHelper::translatedFormat($m, 'F Y'),
            ];
        }

        return view('lit.reconciliation', array_merge($data, [
            'selectedMonth' => $monthStr,
            'monthsList' => $monthsList,
        ]));
    }

    public function processReturn(Request $request, InventoryLedgerService $ledgerService)
    {
        $user = Auth::user();
        if (!$user->can('view lit inventory') && !$user->hasRole('super admin') && !$user->hasRole('Lit User') && !$user->hasRole('Committees')) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'returns' => 'required|array|min:1',
            'returns.*' => 'nullable|integer|min:0',
            'notes' => 'nullable|string|max:255',
        ]);

        $returns = $request->input('returns', []);
        $notes = $request->input('notes') ?: __('messages.reconciliation_return_notes');

        // Validate stock available in Lit
        foreach ($returns as $itemId => $qty) {
            $qty = (int) $qty;
            if ($qty > 0) {
                $item = InventoryItem::find($itemId);
                if (!$item || $item->lit_quantity < $qty) {
                    return redirect()->back()->with('error', __('messages.insufficient_lit_stock_item', [
                        'name' => $item ? $item->store_display_name : "#{$itemId}"
                    ]));
                }
            }
        }

        $slipItems = [];
        $processedCount = 0;

        DB::beginTransaction();
        try {
            foreach ($returns as $itemId => $qty) {
                $qty = (int) $qty;
                if ($qty <= 0) continue;

                $item = InventoryItem::find($itemId);
                if (!$item) continue;

                $item->decrement('lit_quantity', $qty);
                $item->increment('store_quantity', $qty);

                InventoryTransaction::create([
                    'inventory_item_id' => $item->id,
                    'user_id' => $user->id,
                    'type' => 'return_from_lit',
                    'quantity' => $qty,
                    'notes' => $notes,
                ]);

                $slipItems[] = [
                    'inventory_item_id' => $item->id,
                    'quantity' => $qty,
                    'unit_price' => $item->selling_price,
                ];

                $processedCount++;
            }

            if ($processedCount === 0) {
                DB::rollBack();
                return redirect()->back()->with('error', __('messages.no_items_selected_for_return'));
            }

            $slip = $ledgerService->createReturnSlip($slipItems, $user->id, $notes);

            DB::commit();

            return redirect()->route('slips.index')
                ->with('success', __('messages.reconciliation_return_success', [
                    'count' => $processedCount,
                    'slip' => $slip ? $slip->slip_number : ''
                ]));
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
