<?php

namespace App\Http\Controllers;

use App\Models\InventorySlip;
use App\Services\MpdfService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class InventorySlipController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('auth'),
        ];
    }

    private function authorizeViewSlips(): void
    {
        $user = Auth::user();
        if (!$user || (!$user->can('view inventory slips') && !$user->hasRole('Lit User') && !$user->hasRole('Store Manager') && !$user->hasRole('rsc') && !$user->hasRole('super admin'))) {
            abort(403, 'Unauthorized');
        }
    }

    /**
     * Slip Archive & Listing
     */
    public function index(Request $request)
    {
        $this->authorizeViewSlips();
        $user = Auth::user();

        $query = InventorySlip::with(['issuer', 'receiver', 'items.item']);

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('slip_number', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%")
                  ->orWhereHas('issuer', function($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('month')) {
            $query->where('created_at', 'like', $request->month . '%');
        }

        $slips = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        $canAcknowledge = $user->can('acknowledge inventory slips') || $user->hasRole('super admin') || $user->hasRole('Lit User');

        $stats = [
            'total_slips' => InventorySlip::count(),
            'transferred' => InventorySlip::where('type', 'transfer_to_lit')->count(),
            'returned' => InventorySlip::where('type', 'return_to_store')->count(),
            'pending_acknowledgment' => InventorySlip::where('type', 'transfer_to_lit')->where('status', 'transferred')->count(),
            'completed' => InventorySlip::whereIn('status', ['received', 'completed'])->count(),
        ];

        return view('slips.index', compact('slips', 'canAcknowledge', 'stats'));
    }

    /**
     * Show Slip details
     */
    public function show(InventorySlip $slip)
    {
        $this->authorizeViewSlips();
        $user = Auth::user();

        $slip->load(['issuer', 'receiver', 'items.item']);

        $canAcknowledge = $user->can('acknowledge inventory slips') || $user->hasRole('super admin') || $user->hasRole('Lit User');

        return view('slips.show', compact('slip', 'canAcknowledge'));
    }

    /**
     * Literature Committee acknowledges receipt of transferred items
     */
    public function acknowledgeReceipt(Request $request, InventorySlip $slip)
    {
        $user = Auth::user();
        if (!$user || (!$user->can('acknowledge inventory slips') && !$user->hasRole('super admin') && !$user->hasRole('Lit User'))) {
            abort(403, 'Unauthorized');
        }

        if ($slip->status === 'received' || $slip->status === 'completed') {
            return redirect()->back()->with('error', __('messages.slip_already_acknowledged'));
        }

        $slip->update([
            'status' => 'received',
            'received_by' => $user->id,
            'received_at' => now(),
        ]);

        return redirect()->back()->with('success', __('messages.slip_receipt_acknowledged_success'));
    }

    /**
     * Printable PDF Slip
     */
    public function exportPdf(InventorySlip $slip)
    {
        $this->authorizeViewSlips();

        $slip->load(['issuer', 'receiver', 'items.item']);

        $mpdf = MpdfService::create();
        $html = view('slips.pdf', compact('slip'))->render();
        $mpdf->WriteHTML($html);

        $filename = "slip_{$slip->slip_number}.pdf";
        return response($mpdf->Output($filename, 'S'), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }
}
