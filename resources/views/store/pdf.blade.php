<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <title>Inventory Report</title>
    <style>
        body {
            font-family: 'DejaVu Sans', 'sans-serif';
            font-size: 12px;
            color: #333;
            line-height: 1.5;
        }
        .header {
            margin-bottom: 30px;
            border-bottom: 2px solid #0d6efd;
            padding-bottom: 10px;
        }
        .title {
            font-size: 24px;
            font-weight: bold;
            color: #0d6efd;
        }
        .meta {
            font-size: 10px;
            color: #666;
            margin-top: 5px;
        }
        .summary-box {
            margin-bottom: 30px;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            padding: 15px;
            border-radius: 5px;
        }
        .summary-title {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #495057;
        }
        .summary-grid {
            width: 100%;
        }
        .summary-grid td {
            padding: 5px 10px;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        table.data-table th, table.data-table td {
            border: 1px solid #dee2e6;
            padding: 8px;
            text-align: left;
        }
        table.data-table th {
            background-color: #e9ecef;
            font-weight: bold;
        }
        .text-center { text-align: center !important; }
        .text-right { text-align: right !important; }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">{{ __('messages.inventory_transaction_reports') }}</div>
        <div class="meta">{{ __('messages.Date') }}: {{ now()->format('Y-m-d H:i:s') }}</div>
    </div>

    <div class="summary-box">
        <div class="summary-title">{{ __('messages.current_inventory_summary') }}</div>
        <table class="summary-grid">
            <tr>
                <td><strong>{{ __('messages.total_unique_items') }}:</strong> {{ $items->count() }}</td>
                <td><strong>{{ __('messages.total_store_stock') }}:</strong> {{ $items->sum('store_quantity') }}</td>
            </tr>
            <tr>
                <td><strong>{{ __('messages.total_lit_stock') }}:</strong> {{ $items->sum('lit_quantity') }}</td>
                <td><strong>{{ __('messages.total_valuation') }}</strong> EGP {{ number_format($totalValuation, 2) }}</td>
            </tr>
        </table>
    </div>

    <h3>{{ __('messages.transaction_history') }}</h3>
    <table class="data-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>{{ __('messages.date_time') }}</th>
                <th>{{ __('messages.item_name') }}</th>
                <th>{{ __('messages.Type') }}</th>
                <th class="text-center">{{ __('messages.Capacity') }}</th>
                <th>{{ __('messages.operator') }}</th>
                <th>{{ __('messages.Notes') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($transactions as $t)
                <tr>
                    <td>#{{ $t->id }}</td>
                    <td>{{ $t->created_at->format('Y-m-d H:i:s') }}</td>
                    <td><strong>{{ $t->item->name ?? 'Deleted Item' }}</strong></td>
                    <td>
                        @if ($t->type === 'receive')
                            {{ __('messages.receive') }}
                        @elseif ($t->type === 'transfer_to_lit')
                            {{ __('messages.transfer_to_lit') }}
                        @elseif ($t->type === 'return_from_lit')
                            {{ __('messages.return_from_lit') }}
                        @endif
                    </td>
                    <td class="text-center"><strong>{{ $t->quantity }}</strong></td>
                    <td>{{ $t->user->name ?? 'System' }}</td>
                    <td>{{ $t->notes ?: '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center">{{ __('messages.no_transactions_recorded') }}</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
