<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <title>Monthly Inventory Ledger - {{ $selectedMonth }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', 'sans-serif';
            font-size: 9px;
            color: #333;
            line-height: 1.4;
        }
        .header {
            margin-bottom: 15px;
            border-bottom: 2px solid #2563eb;
            padding-bottom: 8px;
        }
        .title {
            font-size: 16px;
            font-weight: bold;
            color: #2563eb;
        }
        .meta {
            font-size: 9px;
            color: #555;
            margin-top: 3px;
        }
        .kpi-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .kpi-table td {
            width: 33.33%;
            border: 1px solid #cbd5e1;
            padding: 8px;
            vertical-align: top;
            background-color: #f8fafc;
        }
        .kpi-title {
            font-size: 11px;
            font-weight: bold;
            color: #1e40af;
            margin-bottom: 5px;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 3px;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        table.data-table th, table.data-table td {
            border: 1px solid #cbd5e1;
            padding: 5px 6px;
            text-align: {{ app()->getLocale() === 'ar' ? 'right' : 'left' }};
        }
        table.data-table th {
            background-color: #f1f5f9;
            font-weight: bold;
        }
        .text-center { text-align: center !important; }
        .text-end { text-align: {{ app()->getLocale() === 'ar' ? 'left' : 'right' }} !important; }
    </style>
</head>
<body>

    <div class="header">
        <table style="width: 100%;">
            <tr>
                <td style="width: 70%;">
                    <div class="title">
                        {{ __('messages.monthly_ledger_title') }} - {{ \App\Services\DateNumberHelper::translatedFormat($month, 'F Y') }}
                    </div>
                    <div class="meta">
                        {{ __('messages.generated_at') }}: {{ now()->format('Y-m-d H:i') }} | {{ config('app.name', 'NA Egypt') }}
                    </div>
                </td>
                <td style="width: 30%; text-align: {{ app()->getLocale() === 'ar' ? 'left' : 'right' }};">
                    <span style="font-size: 12px; font-weight: bold; color: #64748b;">
                        {{ __('messages.lit_and_store_ledger') }}
                    </span>
                </td>
            </tr>
        </table>
    </div>

    {{-- KPI Summary Boxes --}}
    <table class="kpi-table">
        <tr>
            <td>
                <div class="kpi-title">{{ __('messages.litstore_monthly_summary') }}</div>
                <div>{{ __('messages.store_received') }}: <strong>+{{ $store_summary['received'] }}</strong></div>
                <div>{{ __('messages.store_transferred_to_lit') }}: <strong>-{{ $store_summary['transferred'] }}</strong></div>
                <div>{{ __('messages.store_returned_from_lit') }}: <strong>+{{ $store_summary['returned'] }}</strong></div>
                <div>{{ __('messages.store_ending_remains') }}: <strong>{{ $store_summary['remains'] }}</strong></div>
                <div>{{ __('messages.store_stock_valuation') }}: <strong>EGP {{ number_format($store_summary['valuation'], 2) }}</strong></div>
            </td>
            <td>
                <div class="kpi-title">{{ __('messages.lit_comm_monthly_summary') }}</div>
                <div>{{ __('messages.lit_received_from_store') }}: <strong>+{{ $lit_summary['received'] }}</strong></div>
                <div>{{ __('messages.lit_sold_to_groups') }}: <strong>-{{ $lit_summary['sold'] }}</strong></div>
                <div>{{ __('messages.lit_returned_to_store') }}: <strong>-{{ $lit_summary['returned'] }}</strong></div>
                <div>{{ __('messages.lit_ending_remains') }}: <strong>{{ $lit_summary['remains'] }}</strong></div>
                <div>{{ __('messages.total_monthly_sales_value') }}: <strong>EGP {{ number_format($lit_summary['sales_valuation'], 2) }}</strong></div>
            </td>
            <td>
                <div class="kpi-title">{{ __('messages.combined_monthly_overview') }}</div>
                <div>{{ __('messages.total_combined_stock') }}: <strong>{{ $grand_totals['total_stock'] }}</strong></div>
                <div>{{ __('messages.total_combined_inventory_valuation') }}: <strong>EGP {{ number_format($grand_totals['total_valuation'], 2) }}</strong></div>
                <div>{{ __('messages.total_month_sales_revenue') }}: <strong>EGP {{ number_format($grand_totals['total_sales_value'], 2) }}</strong></div>
            </td>
        </tr>
    </table>

    {{-- Category Breakdown Table --}}
    <div style="font-weight: bold; font-size: 11px; margin-top: 10px; color: #1e40af;">{{ __('messages.category_breakdown') }}</div>
    <table class="data-table">
        <thead>
            <tr>
                <th rowspan="2">{{ __('messages.Category') }}</th>
                <th colspan="4" class="text-center">{{ __('messages.litstore') }}</th>
                <th colspan="4" class="text-center">{{ __('messages.lit_committee') }}</th>
                <th colspan="2" class="text-center">{{ __('messages.total') }}</th>
            </tr>
            <tr>
                <th class="text-center">{{ __('messages.received_short') }}</th>
                <th class="text-center">{{ __('messages.transferred_short') }}</th>
                <th class="text-center">{{ __('messages.returned_short') }}</th>
                <th class="text-center">{{ __('messages.remains_short') }}</th>
                <th class="text-center">{{ __('messages.received_short') }}</th>
                <th class="text-center">{{ __('messages.sold_short') }}</th>
                <th class="text-center">{{ __('messages.returned_short') }}</th>
                <th class="text-center">{{ __('messages.remains_short') }}</th>
                <th class="text-center">{{ __('messages.remains_short') }}</th>
                <th class="text-end">{{ __('messages.Valuation') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($categories as $cat)
                <tr>
                    <td><strong>{{ $cat['category'] }}</strong></td>
                    <td class="text-center">+{{ $cat['store_received'] }}</td>
                    <td class="text-center">-{{ $cat['store_transferred'] }}</td>
                    <td class="text-center">+{{ $cat['store_returned'] }}</td>
                    <td class="text-center">{{ $cat['store_remains'] }}</td>
                    <td class="text-center">+{{ $cat['lit_received'] }}</td>
                    <td class="text-center">-{{ $cat['lit_sold'] }}</td>
                    <td class="text-center">-{{ $cat['lit_returned'] }}</td>
                    <td class="text-center">{{ $cat['lit_remains'] }}</td>
                    <td class="text-center"><strong>{{ $cat['total_remains'] }}</strong></td>
                    <td class="text-end">EGP {{ number_format($cat['total_valuation'], 2) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="background-color: #f1f5f9; font-weight: bold;">
                <td>{{ __('messages.Total') }}</td>
                <td class="text-center">+{{ $store_summary['received'] }}</td>
                <td class="text-center">-{{ $store_summary['transferred'] }}</td>
                <td class="text-center">+{{ $store_summary['returned'] }}</td>
                <td class="text-center">{{ $store_summary['remains'] }}</td>
                <td class="text-center">+{{ $lit_summary['received'] }}</td>
                <td class="text-center">-{{ $lit_summary['sold'] }}</td>
                <td class="text-center">-{{ $lit_summary['returned'] }}</td>
                <td class="text-center">{{ $lit_summary['remains'] }}</td>
                <td class="text-center">{{ $grand_totals['total_stock'] }}</td>
                <td class="text-end">EGP {{ number_format($grand_totals['total_valuation'], 2) }}</td>
            </tr>
        </tfoot>
    </table>

</body>
</html>
