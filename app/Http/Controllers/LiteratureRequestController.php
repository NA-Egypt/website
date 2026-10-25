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
     * Helper to get group of current user or selected group for admin/treasurer
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

        if ($user->hasRole('Treasurer') || $user->hasRole('ServiceBody')) {
            $serviceBodyId = $user->service_body_id;
            $groupId = $request ? ($request->input('group_id') ?? $request->input('selected_group_id')) : request('group_id');
            if ($groupId) {
                $group = Group::find($groupId);
                if ($group && $group->service_body_id == $serviceBodyId) {
                    return $group;
                }
            }
            return Group::where('service_body_id', $serviceBodyId)->first();
        }

        return Group::where('user_id', $user->id)->first();
    }

    /**
     * Check if group request editing is locked
     */
    private function isRequestLocked(Group $group, Carbon $month): bool
    {
        $user = Auth::user();
        $isTreasurerOrAdmin = $user->hasRole('super admin') || $user->hasRole('Treasurer') || $user->hasRole('ServiceBody');

        $accumulated = LiteratureRequest::where('service_body_id', $group->service_body_id)
            ->where('month', $month)
            ->where('type', 'servicebody')
            ->first();

        // If accumulated invoice has been sent to committee or beyond, editing is locked
        if ($accumulated && in_array($accumulated->status, ['sent_to_committee', 'returned_by_committee', 'approved'])) {
            return true;
        }

        // Treasurers and Super Admins can edit past 19th as long as accumulated invoice is not sent
        if ($isTreasurerOrAdmin) {
            return false;
        }

        // For GSR, locked after day 19
        return Carbon::now()->day > 19;
    }

    /**
     * Re-compile accumulated invoice from submitted group requests if draft exists or when requested
     */
    private function syncAccumulatedInvoice($serviceBodyId, Carbon $month)
    {
        $accumulated = LiteratureRequest::where('service_body_id', $serviceBodyId)
            ->where('month', $month)
            ->where('type', 'servicebody')
            ->first();

        // If it exists but is already sent to committee or beyond, do not alter
        if ($accumulated && $accumulated->status !== 'draft') {
            return $accumulated;
        }

        $groupRequests = LiteratureRequest::where('service_body_id', $serviceBodyId)
            ->where('month', $month)
            ->where('type', 'group')
            ->where('status', 'submitted')
            ->get();

        if ($groupRequests->isEmpty()) {
            if ($accumulated) {
                $accumulated->items()->delete();
                $accumulated->update([
                    'total_items_count' => 0,
                    'total_price' => 0.00
                ]);
            }
            return $accumulated;
        }

        if (!$accumulated) {
            $accumulated = LiteratureRequest::create([
                'service_body_id' => $serviceBodyId,
                'month' => $month,
                'type' => 'servicebody',
                'status' => 'draft',
                'total_items_count' => 0,
                'total_price' => 0.00
            ]);
        }

        // Reset items
        $accumulated->items()->delete();

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

        return $accumulated;
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

        $user = Auth::user();
        $month = Carbon::now()->startOfMonth();
        $isLocked = $this->isRequestLocked($group, $month);

        // Check if there is an existing request for this month
        $requestData = LiteratureRequest::where('group_id', $group->id)
            ->where('month', $month)
            ->first();

        // Get available items from store or literature (quantity > 0)
        $items = InventoryItem::where(function($q) {
            $q->where('store_quantity', '>', 0)
              ->orWhere('lit_quantity', '>', 0);
        })->orderBy('name')->get();

        if ($user->hasRole('super admin')) {
            $allGroups = Group::orderBy('ar_name')->get();
        } elseif ($user->hasRole('Treasurer') || $user->hasRole('ServiceBody')) {
            $allGroups = Group::where('service_body_id', $user->service_body_id)->orderBy('ar_name')->get();
        } else {
            $allGroups = collect();
        }

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

        $month = Carbon::now()->startOfMonth();

        if ($this->isRequestLocked($group, $month)) {
            return redirect()->back()->with('error', __('messages.locked_after_19th'));
        }

        $action = $request->input('action', 'draft');
        $newStatus = ($action === 'submit') ? 'submitted' : 'draft';

        // Find or create request
        $litRequest = LiteratureRequest::firstOrCreate([
            'group_id' => $group->id,
            'service_body_id' => $group->service_body_id,
            'month' => $month,
            'type' => 'group',
        ], [
            'status' => $newStatus,
            'total_items_count' => 0,
            'total_price' => 0.00
        ]);

        // Reset items
        $litRequest->items()->delete();
        $litRequest->status = $newStatus;
        $litRequest->save();

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

        // Auto-sync accumulated invoice if draft exists
        $this->syncAccumulatedInvoice($group->service_body_id, $month);

        $successMsg = ($newStatus === 'submitted')
            ? __('messages.request_submitted_success')
            : __('messages.item_updated_success');

        return redirect()->route('literature-requests.cart', ['group_id' => $group->id])->with('success', $successMsg);
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

        $month = Carbon::now()->startOfMonth();

        if ($this->isRequestLocked($group, $month)) {
            return redirect()->back()->with('error', __('messages.locked_after_19th'));
        }

        $litRequest = LiteratureRequest::where('group_id', $group->id)
            ->where('month', $month)
            ->where('type', 'group')
            ->first();

        if (!$litRequest || $litRequest->items()->count() === 0) {
            return redirect()->back()->with('error', 'Your cart is empty.');
        }

        $isOverride = $litRequest->status === 'submitted';

        $litRequest->update(['status' => 'submitted']);

        // Auto-sync accumulated invoice if draft exists
        $this->syncAccumulatedInvoice($group->service_body_id, $month);

        return redirect()->route('literature-requests.cart', ['group_id' => $group->id])->with('success', 
            $isOverride ? __('messages.request_overridden_success') : __('messages.request_submitted_success')
        );
    }

    /**
     * Toggle group request between draft and submitted (Treasurer / Admin)
     */
    public function toggleGroupRequestStatus(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user->hasRole('super admin') && !$user->hasRole('Treasurer') && !$user->hasRole('ServiceBody')) {
            abort(403);
        }

        $litRequest = LiteratureRequest::where('type', 'group')->findOrFail($id);

        if (!$user->hasRole('super admin') && $litRequest->service_body_id !== $user->service_body_id) {
            abort(403);
        }

        $month = $litRequest->month;
        $group = $litRequest->group;

        if ($this->isRequestLocked($group, $month)) {
            return redirect()->back()->with('error', __('messages.locked_after_19th'));
        }

        $newStatus = ($litRequest->status === 'submitted') ? 'draft' : 'submitted';
        $litRequest->update(['status' => $newStatus]);

        // Auto-sync accumulated invoice
        $this->syncAccumulatedInvoice($litRequest->service_body_id, $month);

        $msg = ($newStatus === 'submitted')
            ? __('messages.request_submitted_success')
            : __('messages.request_moved_to_draft_success');

        return redirect()->back()->with('success', $msg);
    }

    /**
     * Treasurer Dashboard
     */
    public function treasurerDashboard(Request $request)
    {
        $user = Auth::user();
        if (!$user->hasRole('Treasurer') && !$user->hasRole('ServiceBody') && !$user->hasRole('super admin')) {
            abort(403, 'Unauthorized');
        }

        $serviceBodyId = $user->service_body_id;
        if ($user->hasRole('super admin') || !$serviceBodyId) {
            // Admin or user without fixed service_body_id can select service body
            $serviceBodyId = $request->input('service_body_id', $serviceBodyId ?: (ServiceBody::first()->id ?? null));
        }

        if (!$serviceBodyId) {
            return redirect()->route('dashboard')->with('error', 'No Service Body configured.');
        }

        $serviceBody = ServiceBody::find($serviceBodyId);
        $month = Carbon::now()->startOfMonth();

        // 1. Auto-compile accumulated invoice if day >= 19
        if (Carbon::now()->day >= 19) {
            $this->syncAccumulatedInvoice($serviceBodyId, $month);
        }

        // 2. Fetch Group requests (draft, submitted, approved) and Service Body accumulated requests
        $groupRequests = LiteratureRequest::where('service_body_id', $serviceBodyId)
            ->where('month', $month)
            ->where('type', 'group')
            ->whereIn('status', ['draft', 'submitted', 'approved'])
            ->with(['group', 'items'])
            ->get();

        $accumulatedRequest = LiteratureRequest::where('service_body_id', $serviceBodyId)
            ->where('month', $month)
            ->where('type', 'servicebody')
            ->first();

        $allServiceBodies = ServiceBody::orderBy('ar_name')->get();
        $serviceBodyGroups = Group::where('service_body_id', $serviceBodyId)->orderBy('ar_name')->get();

        $isAccumulatedSent = $accumulatedRequest && in_array($accumulatedRequest->status, ['sent_to_committee', 'returned_by_committee', 'approved']);

        return view('literature_requests.treasurer_dashboard', compact(
            'serviceBody', 'groupRequests', 'accumulatedRequest', 'month', 'allServiceBodies', 'serviceBodyId', 'serviceBodyGroups', 'isAccumulatedSent'
        ));
    }

    /**
     * Approve & Send accumulated invoice to Literature Committee
     */
    public function approveAndSend($id)
    {
        $litRequest = LiteratureRequest::findOrFail($id);
        
        $user = Auth::user();
        if (!$user->hasRole('super admin')) {
            if (!$user->hasRole('Treasurer') && !$user->hasRole('ServiceBody')) {
                abort(403);
            }
            if ($litRequest->service_body_id !== $user->service_body_id) {
                abort(403);
            }
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
            if (!$user->service_body_id) {
                $query->where('id', 0);
            } else {
                $query->where('service_body_id', $user->service_body_id);
                if ($targetGroupId) {
                    $targetGroup = Group::find($targetGroupId);
                    if ($targetGroup && $targetGroup->service_body_id == $user->service_body_id) {
                        $query->where('group_id', $targetGroupId);
                    } else {
                        $query->where('id', 0); // Empty results for unauthorized access
                    }
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
            if ($user->hasRole('Treasurer') || $user->hasRole('ServiceBody')) {
                if ($litRequest->service_body_id !== $user->service_body_id) {
                    abort(403);
                }
            } elseif ($user->hasRole('gsr')) {
                $group = $this->getCurrentGroup();
                if (!$group || $litRequest->group_id !== $group->id) {
                    abort(403);
                }
            } else {
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
