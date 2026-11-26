<?php

namespace App\Http\Controllers;

use App\Exceptions\UserFacingException;
use App\Models\InventoryItem;
use App\Models\InventorySlip;
use App\Models\LiteratureRequest;
use App\Models\LiteratureRequestItem;
use App\Models\ServiceCommittee;
use App\Services\InventoryLedgerService;
use App\Services\MpdfService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CommitteeLiteratureController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('auth'),
        ];
    }

    protected InventoryLedgerService $ledgerService;

    public function __construct(InventoryLedgerService $ledgerService)
    {
        $this->ledgerService = $ledgerService;
    }

    /**
     * Resolve the current committee for the authenticated user
     */
    private function getCurrentCommittee(?Request $request = null): ?ServiceCommittee
    {
        $user = Auth::user();
        if ($user->hasRole('super admin') || $user->hasRole('Lit User')) {
            $committeeId = $request ? ($request->input('committee_id') ?? $request->input('service_committee_id')) : request('committee_id');
            if ($committeeId) {
                return ServiceCommittee::find($committeeId);
            }

            return ServiceCommittee::committeesOnly()->first();
        }

        return ServiceCommittee::committeesOnly()->where('user_id', $user->id)->first();
    }

    /**
     * Committee Portal: Dashboard & Request History
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $isLitUser = $user->hasRole('super admin') || $user->hasRole('Lit User');

        $committee = $this->getCurrentCommittee($request);
        if (! $committee && ! $isLitUser) {
            return redirect()->route('dashboard')->with('error', __('messages.flash.committee_literature.committee_required'));
        }

        $allCommittees = $isLitUser ? ServiceCommittee::orderBy('ar_name')->get() : collect();

        $query = LiteratureRequest::with(['serviceCommittee', 'items.item', 'slips'])
            ->where('type', 'committee');

        if ($committee && ! $request->boolean('all')) {
            $query->where('service_committee_id', $committee->id);
        }

        $requests = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        return view('committee_literature.index', compact('requests', 'committee', 'allCommittees', 'isLitUser'));
    }

    /**
     * Committee Portal: Request Literature Form
     */
    public function create(Request $request)
    {
        $committee = $this->getCurrentCommittee($request);
        if (! $committee) {
            return redirect()->route('committee-literature.index')->with('error', __('messages.flash.committee_literature.no_committee_selected'));
        }

        // Available items strictly from Literature Committee inventory (lit_quantity > 0)
        $items = InventoryItem::where('lit_quantity', '>', 0)
            ->orderBy('name')
            ->get();

        $allCommittees = (Auth::user()->hasRole('super admin') || Auth::user()->hasRole('Lit User'))
            ? ServiceCommittee::orderBy('ar_name')->get()
            : collect();

        return view('committee_literature.create', compact('committee', 'items', 'allCommittees'));
    }

    /**
     * Committee Portal: Store Literature Request (On-demand)
     */
    public function store(Request $request)
    {
        $committee = $this->getCurrentCommittee($request);
        if (! $committee) {
            return redirect()->back()->with('error', __('messages.flash.committee_literature.committee_not_found'));
        }

        $validated = $request->validate([
            'quantities' => 'required|array',
            'quantities.*' => 'nullable|integer|min:0',
            'notes' => 'nullable|string|max:1000',
        ]);

        $quantities = array_filter($validated['quantities'], fn ($qty) => (int) $qty > 0);

        if (empty($quantities)) {
            return redirect()->back()->with('error', __('messages.flash.committee_literature.select_positive_quantity'));
        }

        DB::beginTransaction();
        try {
            $litRequest = LiteratureRequest::create([
                'service_committee_id' => $committee->id,
                'month' => Carbon::now()->startOfMonth(),
                'status' => 'submitted',
                'type' => 'committee',
                'total_items_count' => 0,
                'total_price' => 0.00, // Non-billable / No invoice
            ]);

            $totalItems = 0;
            foreach ($quantities as $itemId => $qty) {
                $item = InventoryItem::find($itemId);
                if (! $item) {
                    continue;
                }

                $qty = (int) $qty;
                LiteratureRequestItem::create([
                    'literature_request_id' => $litRequest->id,
                    'inventory_item_id' => $item->id,
                    'quantity' => $qty,
                    'price' => 0.00,
                    'total' => 0.00,
                ]);

                $totalItems += $qty;
            }

            $litRequest->update([
                'total_items_count' => $totalItems,
            ]);

            DB::commit();

            return redirect()->route('committee-literature.show', $litRequest->id)
                ->with('success', __('messages.flash.committee_literature.request_submitted'));
        } catch (UserFacingException $e) {
            DB::rollBack();

            return redirect()->back()->with('error', $e->getMessage());
        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);

            return redirect()->back()->with('error', __('errors.generic'));
        }
    }

    /**
     * View Request Details and Slips
     */
    public function show($id)
    {
        $litRequest = LiteratureRequest::with(['serviceCommittee', 'items.item', 'slips.items.item', 'slips.issuer', 'slips.receiver'])
            ->where('type', 'committee')
            ->findOrFail($id);

        $user = Auth::user();
        if (! $user->hasRole('super admin') && ! $user->hasRole('Lit User')) {
            $userCommittee = ServiceCommittee::where('user_id', $user->id)->first();
            if (! $userCommittee || $userCommittee->id !== $litRequest->service_committee_id) {
                abort(403, 'Unauthorized');
            }
        }

        $deliverySlip = $litRequest->slips->where('type', 'issue_to_committee')->sortByDesc('id')->first();
        $returnSlips = $litRequest->slips->where('type', 'return_from_committee')->sortByDesc('id');

        // Calculate net consumed items
        $summaryItems = [];
        foreach ($litRequest->items as $reqItem) {
            $summaryItems[$reqItem->inventory_item_id] = [
                'item' => $reqItem->item,
                'requested' => $reqItem->quantity,
                'delivered' => 0,
                'returned' => 0,
                'consumed' => 0,
            ];
        }

        if ($deliverySlip) {
            foreach ($deliverySlip->items as $sItem) {
                if (isset($summaryItems[$sItem->inventory_item_id])) {
                    $summaryItems[$sItem->inventory_item_id]['delivered'] += $sItem->quantity;
                }
            }
        }

        foreach ($returnSlips as $rSlip) {
            foreach ($rSlip->items as $rsItem) {
                if (isset($summaryItems[$rsItem->inventory_item_id])) {
                    $summaryItems[$rsItem->inventory_item_id]['returned'] += $rsItem->quantity;
                }
            }
        }

        foreach ($summaryItems as $itemId => $data) {
            $summaryItems[$itemId]['consumed'] = max(0, $data['delivered'] - $data['returned']);
        }

        $canAcknowledge = false;
        if ($deliverySlip && $deliverySlip->status === 'transferred') {
            if ($user->hasRole('super admin') || ($user->hasRole('Committees') && $litRequest->service_committee_id === $user->service_committee_id)) {
                $canAcknowledge = true;
            }
        }

        $canManageFulfillment = $user->hasRole('super admin') || $user->hasRole('Lit User');

        return view('committee_literature.show', compact('litRequest', 'deliverySlip', 'returnSlips', 'summaryItems', 'canAcknowledge', 'canManageFulfillment'));
    }

    /**
     * Literature Committee: Approve & Issue Quantity-Only Delivery Slip
     */
    public function issueSlip(Request $request, $id)
    {
        $user = Auth::user();
        if (! $user->hasRole('super admin') && ! $user->hasRole('Lit User')) {
            abort(403, 'Unauthorized');
        }

        $litRequest = LiteratureRequest::with(['items.item', 'serviceCommittee'])
            ->where('type', 'committee')
            ->findOrFail($id);

        $validated = $request->validate([
            'quantities' => 'required|array',
            'quantities.*' => 'nullable|integer|min:0',
            'notes' => 'nullable|string|max:1000',
        ]);

        $itemsData = [];
        foreach ($validated['quantities'] as $itemId => $qty) {
            $qty = (int) $qty;
            if ($qty <= 0) {
                continue;
            }

            $item = InventoryItem::find($itemId);
            if (! $item) {
                continue;
            }

            if ($item->lit_quantity < $qty) {
                return redirect()->back()->with('error', __('messages.flash.committee_literature.insufficient_stock', [
                    'name' => $item->name,
                    'available' => $item->lit_quantity,
                ]));
            }

            $itemsData[] = [
                'inventory_item_id' => $item->id,
                'quantity' => $qty,
            ];
        }

        if (empty($itemsData)) {
            return redirect()->back()->with('error', __('messages.flash.committee_literature.cannot_issue_empty_slip'));
        }

        DB::beginTransaction();
        try {
            $slip = $this->ledgerService->createCommitteeIssueSlip(
                $litRequest,
                $itemsData,
                $user->id,
                $validated['notes'] ?? null
            );

            // Update request status to dispatched
            $litRequest->update([
                'status' => 'dispatched',
            ]);

            DB::commit();

            return redirect()->route('committee-literature.show', $litRequest->id)
                ->with('success', __('messages.flash.committee_literature.slip_created', ['slip' => $slip->slip_number]));
        } catch (UserFacingException $e) {
            DB::rollBack();

            return redirect()->back()->with('error', $e->getMessage());
        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);

            return redirect()->back()->with('error', __('errors.generic'));
        }
    }

    /**
     * Committee or Lit User: Acknowledge Receipt of Delivery Slip
     */
    public function acknowledgeSlip($slipId)
    {
        $slip = InventorySlip::with('literatureRequest')->findOrFail($slipId);

        $user = Auth::user();
        if (! $user->hasRole('super admin') && ! $user->hasRole('Lit User')) {
            $userCommittee = ServiceCommittee::where('user_id', $user->id)->first();
            if (! $userCommittee || $userCommittee->id !== $slip->service_committee_id) {
                abort(403, 'Unauthorized');
            }
        }

        if ($slip->status === 'received' || $slip->status === 'completed') {
            return redirect()->back()->with('error', __('messages.flash.committee_literature.slip_already_acknowledged'));
        }

        $slip->update([
            'status' => 'received',
            'received_by' => $user->id,
            'received_at' => now(),
        ]);

        if ($slip->literatureRequest) {
            $slip->literatureRequest->update(['status' => 'received']);
        }

        return redirect()->back()->with('success', __('messages.flash.committee_literature.slip_acknowledged', ['slip' => $slip->slip_number]));
    }

    /**
     * Form to Return Remaining Literature
     */
    public function returnForm($id)
    {
        $litRequest = LiteratureRequest::with(['serviceCommittee', 'items.item', 'slips.items.item'])
            ->where('type', 'committee')
            ->findOrFail($id);

        $deliverySlip = $litRequest->slips->where('type', 'issue_to_committee')->sortByDesc('id')->first();
        if (! $deliverySlip) {
            return redirect()->back()->with('error', __('messages.flash.committee_literature.cannot_return_before_delivery'));
        }

        // Calculate available returnable quantities (Delivered - Already Returned)
        $returnSlips = $litRequest->slips->where('type', 'return_from_committee');
        $alreadyReturnedMap = [];
        foreach ($returnSlips as $rSlip) {
            foreach ($rSlip->items as $rItem) {
                $alreadyReturnedMap[$rItem->inventory_item_id] = ($alreadyReturnedMap[$rItem->inventory_item_id] ?? 0) + $rItem->quantity;
            }
        }

        $returnableItems = [];
        foreach ($deliverySlip->items as $dItem) {
            $returnedSoFar = $alreadyReturnedMap[$dItem->inventory_item_id] ?? 0;
            $remaining = max(0, $dItem->quantity - $returnedSoFar);
            if ($remaining > 0) {
                $returnableItems[] = [
                    'item' => $dItem->item,
                    'delivered' => $dItem->quantity,
                    'already_returned' => $returnedSoFar,
                    'max_returnable' => $remaining,
                ];
            }
        }

        return view('committee_literature.return', compact('litRequest', 'deliverySlip', 'returnableItems'));
    }

    /**
     * Process Return of Remaining Literature (Restores lit_quantity)
     */
    public function processReturn(Request $request, $id)
    {
        $litRequest = LiteratureRequest::with(['serviceCommittee', 'slips.items'])
            ->where('type', 'committee')
            ->findOrFail($id);

        $user = Auth::user();

        $validated = $request->validate([
            'quantities' => 'required|array',
            'quantities.*' => 'nullable|integer|min:0',
            'notes' => 'nullable|string|max:1000',
        ]);

        $deliverySlip = $litRequest->slips->where('type', 'issue_to_committee')->sortByDesc('id')->first();
        if (! $deliverySlip) {
            return redirect()->back()->with('error', __('messages.flash.committee_literature.no_delivery_slip_found'));
        }

        // Calculate currently returnable caps
        $returnSlips = $litRequest->slips->where('type', 'return_from_committee');
        $alreadyReturnedMap = [];
        foreach ($returnSlips as $rSlip) {
            foreach ($rSlip->items as $rItem) {
                $alreadyReturnedMap[$rItem->inventory_item_id] = ($alreadyReturnedMap[$rItem->inventory_item_id] ?? 0) + $rItem->quantity;
            }
        }

        $deliveredMap = [];
        foreach ($deliverySlip->items as $dItem) {
            $deliveredMap[$dItem->inventory_item_id] = $dItem->quantity;
        }

        $itemsData = [];
        foreach ($validated['quantities'] as $itemId => $qty) {
            $qty = (int) $qty;
            if ($qty <= 0) {
                continue;
            }

            $delivered = $deliveredMap[$itemId] ?? 0;
            $returnedSoFar = $alreadyReturnedMap[$itemId] ?? 0;
            $maxReturnable = max(0, $delivered - $returnedSoFar);

            if ($qty > $maxReturnable) {
                $item = InventoryItem::find($itemId);

                return redirect()->back()->with('error', __('messages.flash.committee_literature.return_exceeds_remaining', [
                    'name' => $item->name,
                    'max' => $maxReturnable,
                ]));
            }

            $itemsData[] = [
                'inventory_item_id' => $itemId,
                'quantity' => $qty,
            ];
        }

        if (empty($itemsData)) {
            return redirect()->back()->with('error', __('messages.flash.committee_literature.specify_return_quantity'));
        }

        DB::beginTransaction();
        try {
            $slip = $this->ledgerService->createCommitteeReturnSlip(
                $litRequest,
                $itemsData,
                $user->id,
                $validated['notes'] ?? null
            );

            // Check if all items are fully returned or accounted for
            $litRequest->update([
                'status' => 'completed',
            ]);

            DB::commit();

            return redirect()->route('committee-literature.show', $litRequest->id)
                ->with('success', __('messages.flash.committee_literature.return_slip_generated', ['slip' => $slip->slip_number]));
        } catch (UserFacingException $e) {
            DB::rollBack();

            return redirect()->back()->with('error', $e->getMessage());
        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);

            return redirect()->back()->with('error', __('errors.generic'));
        }
    }

    /**
     * Export Quantity-Only Slip PDF (No Invoice / No Prices)
     */
    public function exportSlipPdf($slipId)
    {
        $slip = InventorySlip::with(['serviceCommittee', 'literatureRequest', 'issuer', 'receiver', 'items.item'])
            ->findOrFail($slipId);

        $user = Auth::user();
        if (! $user->hasRole('super admin') && ! $user->hasRole('Lit User')) {
            $userCommittee = ServiceCommittee::where('user_id', $user->id)->first();
            if (! $userCommittee || $userCommittee->id !== $slip->service_committee_id) {
                abort(403, 'Unauthorized');
            }
        }

        $mpdf = MpdfService::create();
        $html = view('committee_literature.slip_pdf', compact('slip'))->render();
        $mpdf->WriteHTML($html);

        $filename = "slip_{$slip->slip_number}.pdf";

        return response($mpdf->Output($filename, 'S'), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="'.$filename.'"');
    }
}
