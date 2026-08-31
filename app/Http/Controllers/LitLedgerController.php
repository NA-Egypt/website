<?php

namespace App\Http\Controllers;

use App\Services\InventoryLedgerService;
use App\Services\MpdfService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class LitLedgerController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('auth'),
        ];
    }

    private function authorizeAccess()
    {
        $user = Auth::user();
        if (!$user->can('view lit inventory') && !$user->can('manage store') && !$user->hasRole('rsc') && !$user->hasRole('super admin') && !$user->hasRole('Lit User') && !$user->hasRole('Committees')) {
            abort(403, 'Unauthorized');
        }
    }

    public function index(Request $request, InventoryLedgerService $ledgerService)
    {
        $this->authorizeAccess();

        $monthStr = $request->input('month', Carbon::now()->format('Y-m'));
        $month = Carbon::parse($monthStr . '-01')->startOfMonth();

        $ledgerData = $ledgerService->getMonthlyLedger($month);

        return view('lit.ledger', array_merge($ledgerData, [
            'selectedMonth' => $monthStr,
        ]));
    }

    public function exportPdf(Request $request, InventoryLedgerService $ledgerService)
    {
        $this->authorizeAccess();

        $monthStr = $request->input('month', Carbon::now()->format('Y-m'));
        $month = Carbon::parse($monthStr . '-01')->startOfMonth();

        $ledgerData = $ledgerService->getMonthlyLedger($month);

        $mpdf = MpdfService::create();
        $html = view('lit.ledger_pdf', array_merge($ledgerData, [
            'selectedMonth' => $monthStr,
        ]))->render();

        $mpdf->WriteHTML($html);

        $filename = "inventory_ledger_{$monthStr}.pdf";
        return response($mpdf->Output($filename, 'S'), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    public function exportCsv(Request $request, InventoryLedgerService $ledgerService)
    {
        $this->authorizeAccess();

        $monthStr = $request->input('month', Carbon::now()->format('Y-m'));
        $month = Carbon::parse($monthStr . '-01')->startOfMonth();

        $ledgerData = $ledgerService->getMonthlyLedger($month);

        $fileName = "inventory_ledger_{$monthStr}.csv";

        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename={$fileName}",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = [
            __('messages.item_name'),
            __('messages.Category'),
            __('messages.selling_price'),
            // Litstore
            __('messages.store_received'),
            __('messages.store_transferred'),
            __('messages.store_returned'),
            __('messages.store_remains'),
            __('messages.store_valuation'),
            // Lit Committee
            __('messages.lit_received'),
            __('messages.lit_sold'),
            __('messages.lit_returned'),
            __('messages.lit_remains'),
            __('messages.lit_sales_value'),
            __('messages.lit_valuation'),
            // Total
            __('messages.total_stock'),
            __('messages.total_valuation'),
        ];

        $callback = function () use ($ledgerData, $columns) {
            $file = fopen('php://output', 'w');
            fputs($file, "\xEF\xBB\xBF"); // UTF-8 BOM

            fputcsv($file, $columns);

            foreach ($ledgerData['items'] as $item) {
                fputcsv($file, [
                    $item['display_name'],
                    $item['category'],
                    number_format($item['selling_price'], 2, '.', ''),
                    $item['store_received'],
                    $item['store_transferred'],
                    $item['store_returned'],
                    $item['store_remains'],
                    number_format($item['store_valuation'], 2, '.', ''),
                    $item['lit_received'],
                    $item['lit_sold'],
                    $item['lit_returned'],
                    $item['lit_remains'],
                    number_format($item['lit_sales_value'], 2, '.', ''),
                    number_format($item['lit_valuation'], 2, '.', ''),
                    $item['total_remains'],
                    number_format($item['total_valuation'], 2, '.', ''),
                ]);
            }

            // Totals Row
            fputcsv($file, [
                __('messages.Total'),
                '',
                '',
                $ledgerData['store_summary']['received'],
                $ledgerData['store_summary']['transferred'],
                $ledgerData['store_summary']['returned'],
                $ledgerData['store_summary']['remains'],
                number_format($ledgerData['store_summary']['valuation'], 2, '.', ''),
                $ledgerData['lit_summary']['received'],
                $ledgerData['lit_summary']['sold'],
                $ledgerData['lit_summary']['returned'],
                $ledgerData['lit_summary']['remains'],
                number_format($ledgerData['lit_summary']['sales_valuation'], 2, '.', ''),
                number_format($ledgerData['lit_summary']['stock_valuation'], 2, '.', ''),
                $ledgerData['grand_totals']['total_stock'],
                number_format($ledgerData['grand_totals']['total_valuation'], 2, '.', ''),
            ]);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
