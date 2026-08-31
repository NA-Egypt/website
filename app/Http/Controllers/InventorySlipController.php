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

    /**
     * Slip Archive & Listing
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user->can('manage store') && !$user->can('view lit inventory') && !$user->hasRole('rsc') && !$user->hasRole('super admin')) {
            abort(403, 'Unauthorized');
        }

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

        $canAcknowledge = $user->can('view lit inventory') || $user->hasRole('super admin') || $user->hasRole('Lit User');

        return view('slips.index', compact('slips', 'canAcknowledge'));
    }

    /**
     * Show Slip details
     */
    public function show(InventorySlip $slip)
    {
        $user = Auth::user();
        if (!$user->can('manage store') && !$user->can('view lit inventory') && !$user->hasRole('rsc') && !$user->hasRole('super admin')) {
            abort(403, 'Unauthorized');
        }

        $slip->load(['issuer', 'receiver', 'items.item']);

        return view('slips.show', compact('slip'));
    }

    /**
     * Literature Committee acknowledges receipt of transferred items
     */
    public function acknowledgeReceipt(Request $request, InventorySlip $slip)
    {
        $user = Auth::user();
        if (!$user->can('view lit inventory') && !$user->hasRole('super admin') && !$user->hasRole('Lit User')) {
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
        $user = Auth::user();
        if (!$user->can('manage store') && !$user->can('view lit inventory') && !$user->hasRole('rsc') && !$user->hasRole('super admin')) {
            abort(403, 'Unauthorized');
        }

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
