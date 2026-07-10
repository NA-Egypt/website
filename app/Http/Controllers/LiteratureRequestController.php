<?php

namespace App\Http\Controllers;

use App\Models\InventoryItem;
use App\Models\LiteratureRequest;
use App\Models\LiteratureRequestItem;
use App\Models\Group;
use App\Models\ServiceBody;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\MpdfService;

use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class LiteratureRequestController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('auth'),
        ];
    }

    /**
     * Helper to get group of current user
     */
    private function getCurrentGroup(Request $request = null)
    {
        $user = Auth::user();
        if ($user->hasRole('super admin')) {
            $groupId = $request ? ($request->input('group_id') ?? $request->input('selected_group_id')) : request('group_id');
            if ($groupId) {
                return Group::find($groupId);
            }
            return Group::first();
        }
        return Group::where('user_id', $user->id)->first();
    }

    /**
     * GSR/Group Cart view
     */
    public function cart(Request $request)
    {
        $group = $this->getCurrentGroup($request);
        if (!$group) {
            return redirect()->route('dashboard')->with('error', 'You must be associated with a group to request literature.');
        }

        $month = Carbon::now()->startOfMonth();
        $isLocked = Carbon::now()->day > 19;

        // Check if there is an existing request for this month
        $requestData = LiteratureRequest::where('group_id', $group->id)
            ->where('month', $month)
            ->first();

        // Get available items from store or literature (quantity > 0)
        $items = InventoryItem::where(function($q) {
            $q->where('store_quantity', '>', 0)
              ->orWhere('lit_quantity', '>', 0);
        })->orderBy('name')->get();

        $allGroups = Auth::user()->hasRole('super admin') ? Group::orderBy('ar_name')->get() : collect();

        return view('literature_requests.cart', [
            'group' => $group,
            'request' => $requestData,
            'items' => $items,
            'isLocked' => $isLocked,
            'month' => $month,
            'allGroups' => $allGroups
        ]);
    }

    /**
     * Add / Update items in group cart
     */
    public function updateCart(Request $request)
    {
        $group = $this->getCurrentGroup($request);
        if (!$group) {
            return redirect()->back()->with('error', 'No group associated.');
        }

        if (Carbon::now()->day > 19) {
            return redirect()->back()->with('error', __('messages.locked_after_19th'));
        }

        $month = Carbon::now()->startOfMonth();

        // Find or create draft request
        $litRequest = LiteratureRequest::firstOrCreate([
            'group_id' => $group->id,
            'service_body_id' => $group->service_body_id,
            'month' => $month,
            'type' => 'group',
        ], [
            'status' => 'draft',
            'total_items_count' => 0,
            'total_price' => 0.00
        ]);

        // If previously submitted, overriding is allowed before 19th
        if ($litRequest->status === 'submitted') {
            // Overriding is allowed, reset totals and items
            $litRequest->items()->delete();
            $litRequest->status = 'draft';
            $litRequest->save();
        }

        $quantities = $request->input('quantities', []); // [item_id => qty]
        
        foreach ($quantities as $itemId => $qty) {
            $qty = intval($qty);
            if ($qty <= 0) {
                continue;
            }

            $item = InventoryItem::find($itemId);
            if (!$item) continue;

            LiteratureRequestItem::create([
                'literature_request_id' => $litRequest->id,
                'inventory_item_id' => $item->id,
                'quantity' => $qty,
                'price' => $item->selling_price,
                'total' => $qty * $item->selling_price,
            ]);
        }

        // Recalculate totals
        $totalItems = $litRequest->items()->sum('quantity');
        $totalPrice = $litRequest->items()->sum('total');

        $litRequest->update([
            'total_items_count' => $totalItems,
            'total_price' => $totalPrice,
        ]);

        return redirect()->route('literature-requests.cart', ['group_id' => $group->id])->with('success', __('messages.item_updated_success'));
    }

    /**
     * Submit group literature request
     */
    public function submitRequest(Request $request)
    {
        $group = $this->getCurrentGroup($request);
        if (!$group) {
            return redirect()->back()->with('error', 'Group not found.');
        }

        if (Carbon::now()->day > 19) {
            return redirect()->back()->with('error', __('messages.locked_after_19th'));
        }

        $month = Carbon::now()->startOfMonth();

        $litRequest = LiteratureRequest::where('group_id', $group->id)
            ->where('month', $month)
            ->where('type', 'group')
            ->first();

        if (!$litRequest || $litRequest->items()->count() === 0) {
            return redirect()->back()->with('error', 'Your cart is empty.');
        }

        $isOverride = $litRequest->status === 'submitted';

        $litRequest->update(['status' => 'submitted']);

        return redirect()->route('literature-requests.cart', ['group_id' => $group->id])->with('success', 
            $isOverride ? __('messages.request_overridden_success') : __('messages.request_submitted_success')
        );
    }

    /**
     * Treasurer Dashboard
     */
    public function treasurerDashboard(Request $request)
    {
        $user = Auth::user();
        if (!$user->hasRole('Treasurer') && !$user->hasRole('super admin') && !$user->hasRole('Store Manager') && !$user->hasRole('Lit User')) {
            abort(403, 'Unauthorized');
        }

        $serviceBodyId = $user->service_body_id;
        if ($user->hasRole('super admin') || !$serviceBodyId) {
            // Default to first service body if admin has no service_body_id
            $serviceBodyId = $request->input('service_body_id', ServiceBody::first()->id ?? null);
        }

        if (!$serviceBodyId) {
            return redirect()->route('dashboard')->with('error', 'No Service Body configured.');
        }

        $serviceBody = ServiceBody::find($serviceBodyId);
        $month = Carbon::now()->startOfMonth();

        // 1. Auto-compile accumulated invoice if day >= 19
        if (Carbon::now()->day >= 19) {
            $accumulated = LiteratureRequest::where('service_body_id', $serviceBodyId)
                ->where('month', $month)
                ->where('type', 'servicebody')
                ->first();

            if (!$accumulated) {
                // Compile from all group requests of this service body for this month with status 'submitted'
                $groupRequests = LiteratureRequest::where('service_body_id', $serviceBodyId)
                    ->where('month', $month)
                    ->where('type', 'group')
                    ->where('status', 'submitted')
                    ->get();

                if ($groupRequests->count() > 0) {
                    $accumulated = LiteratureRequest::create([
                        'service_body_id' => $serviceBodyId,
                        'month' => $month,
                        'type' => 'servicebody',
                        'status' => 'draft',
                        'total_items_count' => 0,
                        'total_price' => 0.00
                    ]);

                    // Accumulate items
                    $compiledItems = [];
                    foreach ($groupRequests as $gReq) {
                        foreach ($gReq->items as $item) {
                            if (isset($compiledItems[$item->inventory_item_id])) {
                                $compiledItems[$item->inventory_item_id]['qty'] += $item->quantity;
                            } else {
                                $compiledItems[$item->inventory_item_id] = [
                                    'qty' => $item->quantity,
                                    'price' => $item->price
                                ];
                            }
                        }
                    }

                    foreach ($compiledItems as $itemId => $data) {
                        LiteratureRequestItem::create([
                            'literature_request_id' => $accumulated->id,
                            'inventory_item_id' => $itemId,
                            'quantity' => $data['qty'],
                            'price' => $data['price'],
                            'total' => $data['qty'] * $data['price']
                        ]);
                    }

                    // Update totals
                    $accumulated->update([
                        'total_items_count' => $accumulated->items()->sum('quantity'),
                        'total_price' => $accumulated->items()->sum('total'),
                    ]);
                }
            }
        }

        // 2. Fetch Group requests and Service Body accumulated requests
        $groupRequests = LiteratureRequest::where('service_body_id', $serviceBodyId)
            ->where('month', $month)
            ->where('type', 'group')
            ->whereIn('status', ['submitted', 'approved'])
            ->get();

        $accumulatedRequest = LiteratureRequest::where('service_body_id', $serviceBodyId)
            ->where('month', $month)
            ->where('type', 'servicebody')
            ->first();

        $allServiceBodies = ServiceBody::orderBy('ar_name')->get();

        return view('literature_requests.treasurer_dashboard', compact(
            'serviceBody', 'groupRequests', 'accumulatedRequest', 'month', 'allServiceBodies', 'serviceBodyId'
        ));
    }

    /**
     * Approve & Send accumulated invoice to Literature Committee
     */
    public function approveAndSend($id)
    {
        $litRequest = LiteratureRequest::findOrFail($id);
        
        $user = Auth::user();
        if (!$user->hasRole('Treasurer') && !$user->hasRole('super admin')) {
            abort(403);
        }

        $litRequest->update([
            'status' => 'sent_to_committee'
        ]);

        return redirect()->back()->with('success', 'Accumulated request approved and sent to Literature Committee.');
    }

    /**
     * Literature Committee Index view
     */
    public function committeeDashboard()
    {
        $user = Auth::user();
        if (!$user->hasRole('super admin') && !$user->hasRole('Lit User') && !$user->hasRole('Store Manager')) {
            abort(403);
        }

        $month = Carbon::now()->startOfMonth();

        // Get all service body requests sent to committee
        $requests = LiteratureRequest::where('type', 'servicebody')
            ->whereIn('status', ['sent_to_committee', 'returned_by_committee'])
            ->orderBy('month', 'desc')
            ->get();

        return view('literature_requests.committee_dashboard', compact('requests', 'month'));
    }

    /**
     * Edit accumulated invoice (Literature Committee)
     */
    public function committeeEdit($id)
    {
        $litRequest = LiteratureRequest::findOrFail($id);

        $user = Auth::user();
        if (!$user->hasRole('super admin') && !$user->hasRole('Lit User') && !$user->hasRole('Store Manager')) {
            abort(403);
        }

        $allItems = InventoryItem::orderBy('name')->get();

        return view('literature_requests.committee_edit', compact('litRequest', 'allItems'));
    }

    /**
     * Update and Return accumulated invoice to Service Body
     */
    public function committeeUpdate(Request $request, $id)
    {
        $litRequest = LiteratureRequest::findOrFail($id);

        $user = Auth::user();
        if (!$user->hasRole('super admin') && !$user->hasRole('Lit User') && !$user->hasRole('Store Manager')) {
            abort(403);
        }

        $litRequest->items()->delete();

        $quantities = $request->input('quantities', []);

        foreach ($quantities as $itemId => $qty) {
            $qty = intval($qty);
            if ($qty <= 0) continue;

            $item = InventoryItem::find($itemId);
            if (!$item) continue;

            LiteratureRequestItem::create([
                'literature_request_id' => $litRequest->id,
                'inventory_item_id' => $item->id,
                'quantity' => $qty,
                'price' => $item->selling_price,
                'total' => $qty * $item->selling_price
            ]);
        }

        $litRequest->update([
            'status' => 'returned_by_committee',
            'total_items_count' => $litRequest->items()->sum('quantity'),
            'total_price' => $litRequest->items()->sum('total'),
        ]);

        return redirect()->route('literature-requests.committee')->with('success', 'Invoice updated and returned to Service Body successfully.');
    }

    /**
     * Archive with Accordion Hierarchy
     */
    public function archive(Request $request)
    {
        $user = Auth::user();
        
        $query = LiteratureRequest::with(['group', 'serviceBody', 'items.item']);

        $targetGroupId = $request->input('group_id');

        // Restrictions on who can view what
        if ($user->hasRole('super admin') || $user->hasRole('Lit User') || $user->hasRole('Store Manager')) {
            // Super admins and Literature Committee roles see all
            if ($targetGroupId) {
                $query->where('group_id', $targetGroupId);
            }
        } elseif ($user->hasRole('Treasurer') || $user->hasRole('ServiceBody')) {
            // Service Body roles see requests within their Service Body only
            $query->where('service_body_id', $user->service_body_id);
            if ($targetGroupId) {
                $targetGroup = Group::find($targetGroupId);
                if ($targetGroup && $targetGroup->service_body_id == $user->service_body_id) {
                    $query->where('group_id', $targetGroupId);
                } else {
                    $query->where('id', 0); // Empty results for unauthorized access
                }
            }
        } elseif ($user->hasRole('gsr')) {
            // GSR sees only their group requests
            $group = $this->getCurrentGroup();
            if ($group) {
                $query->where('group_id', $group->id);
            } else {
                $query->where('id', 0); // Empty results
            }
        } else {
            abort(403);
        }

        $requests = $query->orderBy('month', 'desc')->get();

        // Group by Year, then Month
        $archiveData = [];
        foreach ($requests as $req) {
            $year = $req->month->format('Y');
            $monthName = \App\Services\DateNumberHelper::translatedFormat($req->month, 'F');
            $monthNum = $req->month->format('m');
            
            $archiveData[$year][$monthNum]['name'] = $monthName;
            $archiveData[$year][$monthNum]['requests'][] = $req;
        }

        return view('literature_requests.archive', compact('archiveData'));
    }

    /**
     * Export Literature Request to PDF (Invoice format)
     */
    public function exportPdf($id)
    {
        $litRequest = LiteratureRequest::with(['group', 'serviceBody', 'items.item'])->findOrFail($id);

        $user = Auth::user();
        // Permission check
        if (!$user->hasRole('super admin') && !$user->hasRole('Lit User') && !$user->hasRole('Store Manager')) {
            if ($litRequest->service_body_id !== $user->service_body_id) {
                abort(403);
            }
        }

        $mpdf = MpdfService::create();
        $html = view('literature_requests.invoice_pdf', compact('litRequest'))->render();
        $mpdf->WriteHTML($html);

        $filename = 'literature_request_' . $litRequest->id . '_' . $litRequest->month->format('Y_m') . '.pdf';
        return response($mpdf->Output($filename, 'S'), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }
}
