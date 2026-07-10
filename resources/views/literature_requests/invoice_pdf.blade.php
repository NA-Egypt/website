<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <title>Literature Request Invoice</title>
    <style>
        body {
            font-family: 'DejaVu Sans', 'sans-serif';
            font-size: 11px;
            color: #333;
            line-height: 1.5;
        }
        .header {
            margin-bottom: 25px;
            border-bottom: 2px solid #0d6efd;
            padding-bottom: 10px;
        }
        .title {
            font-size: 20px;
            font-weight: bold;
            color: #0d6efd;
        }
        .meta {
            font-size: 10px;
            color: #555;
            margin-top: 5px;
        }
        .summary-box {
            margin-bottom: 25px;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            padding: 12px;
            border-radius: 5px;
        }
        .summary-title {
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 8px;
            color: #495057;
        }
        .summary-grid {
            width: 100%;
        }
        .summary-grid td {
            padding: 4px 8px;
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
        <div class="title">
            @if($litRequest->type === 'group')
                {{ __('messages.Literature Request') }}
            @else
                {{ __('messages.accumulated_invoice') }}
            @endif
        </div>
        <div class="meta">
            <strong>{{ __('messages.Date') }}:</strong> {{ now()->format('Y-m-d H:i:s') }}<br>
            <strong>{{ __('messages.Month/Year') }}:</strong> {{ \App\Services\DateNumberHelper::translatedFormat($litRequest->month, 'F Y') }}<br>
            <strong>Type:</strong> {{ strtoupper($litRequest->type) }}
        </div>
    </div>

    <div class="summary-box">
        <div class="summary-title">Request Details</div>
        <table class="summary-grid">
            <tr>
                <td>
                    <strong>
                        @if($litRequest->type === 'group')
                            {{ __('messages.Group') ?? 'Group' }}:
                        @else
                            {{ __('messages.Service Committee') ?? 'Service Body' }}:
                        @endif
                    </strong>
                    @if($litRequest->type === 'group')
                        {{ $litRequest->group->{app()->getLocale() . '_name'} ?? $litRequest->group->en_name }}
                    @else
                        {{ $litRequest->serviceBody->{app()->getLocale() . '_name'} ?? $litRequest->serviceBody->en_name }}
                    @endif
                </td>
                 <td><strong>{{ __('messages.status') ?? 'Status' }}:</strong> {{ __('messages.' . $litRequest->status) }}</td>
            </tr>
            <tr>
                <td><strong>{{ __('messages.total_unique_items') ?? 'Total Items count' }}:</strong> {{ $litRequest->total_items_count }}</td>
                <td><strong>{{ __('messages.total_valuation') ?? 'Total Price' }}:</strong> {{ number_format($litRequest->total_price, 2) }} {{ __('messages.EGP') }}</td>
            </tr>
        </table>
    </div>

    <h3>Items List</h3>
    <table class="data-table">
        <thead>
            <tr>
                <th>{{ __('messages.item_name') }}</th>
                <th>{{ __('messages.Category') }}</th>
                <th class="text-center">{{ __('messages.quantity') }}</th>
                <th class="text-right">{{ __('messages.price') }}</th>
                <th class="text-right">{{ __('messages.total') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($litRequest->items as $item)
                <tr>
                    <td><strong>{{ $item->item->name }}</strong></td>
                    <td>{{ __('messages.cat_' . Str::snake(str_replace(' ', '_', $item->item->category))) }}</td>
                    <td class="text-center">{{ $item->quantity }}</td>
                    <td class="text-right">{{ number_format($item->price, 2) }} {{ __('messages.EGP') }}</td>
                    <td class="text-right">{{ number_format($item->total, 2) }} {{ __('messages.EGP') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
