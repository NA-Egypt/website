<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <title>{{ __('messages.stocktaking_comparison_report') }} #{{ $session->id }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', 'sans-serif';
            font-size: 10px;
            color: #333;
            line-height: 1.4;
        }
        .header {
            margin-bottom: 20px;
            border-bottom: 2px solid #0d6efd;
            padding-bottom: 10px;
        }
        .title {
            font-size: 18px;
            font-weight: bold;
            color: #0d6efd;
        }
        .meta {
            font-size: 9px;
            color: #666;
            margin-top: 4px;
        }
        .summary-box {
            margin-bottom: 20px;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            padding: 10px;
            border-radius: 4px;
        }
        .summary-grid {
            width: 100%;
        }
        .summary-grid td {
            padding: 3px 6px;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        table.data-table th, table.data-table td {
            border: 1px solid #dee2e6;
            padding: 5px;
            text-align: left;
        }
        table.data-table th {
            background-color: #e9ecef;
            font-weight: bold;
        }
        .text-center { text-align: center !important; }
        .text-right { text-align: right !important; }
        .text-success { color: #198754; }
        .text-danger { color: #dc3545; }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">{{ __('messages.stocktaking_comparison_report') }} #{{ $session->id }}</div>
        <div class="meta">
            {{ __('messages.started_by') }}: {{ $session->user->name ?? 'System' }} &bull; 
            {{ __('messages.started_at') }}: {{ $session->started_at->format('Y-m-d H:i') }} &bull; 
            {{ __('messages.status') }}: {{ strtoupper($session->status) }}
        </div>
    </div>

    <div class="summary-box">
        <table class="summary-grid">
            <tr>
                <td><strong>{{ __('messages.total_expected_system_qty') }}:</strong> {{ $totalSystemQty }}</td>
                <td><strong>{{ __('messages.total_physical_counted_qty') }}:</strong> {{ $totalCountedQty }}</td>
            </tr>
            <tr>
                <td><strong>{{ __('messages.net_qty_variance') }}:</strong> {{ $totalVarianceQty > 0 ? "+{$totalVarianceQty}" : $totalVarianceQty }}</td>
                <td><strong>{{ __('messages.net_financial_variance_value') }}:</strong> {{ __('messages.EGP') }} {{ number_format($totalVarianceValue, 2) }}</td>
            </tr>
        </table>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th>#</th>
                <th>{{ __('messages.item_name') }}</th>
                <th>{{ __('messages.Category') }}</th>
                <th class="text-center">Sys Store</th>
                <th class="text-center">Cnt Store</th>
                <th class="text-center">Store Var</th>
                <th class="text-center">Sys Lit</th>
                <th class="text-center">Cnt Lit</th>
                <th class="text-center">Lit Var</th>
                <th class="text-right">Unit Price</th>
                <th class="text-right">Valuation Var</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($items as $item)
                @php
                    $cntStore = $item->counted_store_qty ?? $item->system_store_qty;
                    $cntLit = $item->counted_lit_qty ?? $item->system_lit_qty;
                @endphp
                <tr>
                    <td>{{ $item->inventory_item_id }}</td>
                    <td><strong>{{ $item->inventoryItem->store_display_name ?? 'Item #' . $item->inventory_item_id }}</strong></td>
                    <td>{{ __('messages.cat_' . Str::snake(str_replace(' ', '_', $item->inventoryItem->category ?? 'Others'))) }}</td>
                    <td class="text-center">{{ $item->system_store_qty }}</td>
                    <td class="text-center">{{ $cntStore }}</td>
                    <td class="text-center {{ $item->store_variance != 0 ? ($item->store_variance > 0 ? 'text-success' : 'text-danger') : '' }}">
                        {{ $item->store_variance > 0 ? "+{$item->store_variance}" : $item->store_variance }}
                    </td>
                    <td class="text-center">{{ $item->system_lit_qty }}</td>
                    <td class="text-center">{{ $cntLit }}</td>
                    <td class="text-center {{ $item->lit_variance != 0 ? ($item->lit_variance > 0 ? 'text-success' : 'text-danger') : '' }}">
                        {{ $item->lit_variance > 0 ? "+{$item->lit_variance}" : $item->lit_variance }}
                    </td>
                    <td class="text-right">{{ number_format($item->unit_price, 2) }}</td>
                    <td class="text-right {{ $item->variance_value != 0 ? ($item->variance_value > 0 ? 'text-success' : 'text-danger') : '' }}">
                        {{ number_format($item->variance_value, 2) }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
