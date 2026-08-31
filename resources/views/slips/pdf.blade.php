<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <title>{{ $slip->slip_number }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', 'sans-serif';
            font-size: 11px;
            color: #333;
            line-height: 1.5;
        }
        .header {
            margin-bottom: 20px;
            border-bottom: 2px solid #2563eb;
            padding-bottom: 10px;
        }
        .title {
            font-size: 18px;
            font-weight: bold;
            color: #2563eb;
        }
        .meta {
            font-size: 10px;
            color: #555;
            margin-top: 5px;
        }
        .summary-box {
            margin-bottom: 20px;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 12px;
            border-radius: 5px;
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
            margin-top: 15px;
            margin-bottom: 25px;
        }
        table.data-table th, table.data-table td {
            border: 1px solid #cbd5e1;
            padding: 8px;
            text-align: {{ app()->getLocale() === 'ar' ? 'right' : 'left' }};
        }
        table.data-table th {
            background-color: #f1f5f9;
            font-weight: bold;
        }
        .text-center { text-align: center !important; }
        .text-end { text-align: {{ app()->getLocale() === 'ar' ? 'left' : 'right' }} !important; }
        .text-start { text-align: {{ app()->getLocale() === 'ar' ? 'right' : 'left' }} !important; }
        .signatures {
            width: 100%;
            margin-top: 35px;
        }
        .signatures td {
            width: 50%;
            padding: 15px;
            vertical-align: top;
        }
        .signature-line {
            border-top: 1px dashed #94a3b8;
            margin-top: 45px;
            text-align: center;
            font-size: 10px;
            color: #64748b;
            padding-top: 5px;
        }
    </style>
</head>
<body>

    <div class="header">
        <table style="width: 100%;">
            <tr>
                <td style="width: 70%;">
                    <div class="title">
                        @if($slip->type === 'transfer_to_lit')
                            {{ __('messages.transfer_slip_title') }} (Litstore &rarr; Literature Committee)
                        @else
                            {{ __('messages.return_slip_title') }} (Literature Committee &rarr; Litstore)
                        @endif
                    </div>
                    <div class="meta">
                        {{ __('messages.slip_number') }}: <strong>{{ $slip->slip_number }}</strong> | 
                        {{ __('messages.Date') }}: {{ $slip->created_at->format('Y-m-d H:i') }}
                    </div>
                </td>
                <td style="width: 30%; text-align: {{ app()->getLocale() === 'ar' ? 'left' : 'right' }};">
                    <span style="font-size: 12px; font-weight: bold; color: #64748b;">
                        {{ config('app.name', 'NA Egypt') }}
                    </span>
                </td>
            </tr>
        </table>
    </div>

    <div class="summary-box">
        <table class="summary-grid">
            <tr>
                <td style="width: 25%;"><strong>{{ __('messages.Type') }}:</strong></td>
                <td style="width: 25%;">{{ $slip->type === 'transfer_to_lit' ? __('messages.slip_type_transfer') : __('messages.slip_type_return') }}</td>
                <td style="width: 25%;"><strong>{{ __('messages.Status') }}:</strong></td>
                <td style="width: 25%;">{{ $slip->status === 'received' || $slip->status === 'completed' ? __('messages.status_received') : __('messages.status_transferred') }}</td>
            </tr>
            <tr>
                <td><strong>{{ __('messages.issued_by') }}:</strong></td>
                <td>{{ $slip->issuer->name ?? '-' }}</td>
                <td><strong>{{ __('messages.received_by') }}:</strong></td>
                <td>{{ $slip->receiver->name ?? __('messages.not_yet_acknowledged') }}</td>
            </tr>
            @if($slip->received_at)
            <tr>
                <td><strong>{{ __('messages.received_date') }}:</strong></td>
                <td colspan="3">{{ $slip->received_at->format('Y-m-d H:i') }}</td>
            </tr>
            @endif
            @if($slip->notes)
            <tr>
                <td><strong>{{ __('messages.Notes') }}:</strong></td>
                <td colspan="3">{{ $slip->notes }}</td>
            </tr>
            @endif
        </table>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%;">#</th>
                <th style="width: 45%;">{{ __('messages.item_name') }}</th>
                <th style="width: 15%;" class="text-center">{{ __('messages.Quantity') }}</th>
                <th style="width: 15%;" class="text-end">{{ __('messages.unit_price') }}</th>
                <th style="width: 20%;" class="text-end">{{ __('messages.Total') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($slip->items as $idx => $sItem)
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td>{{ $sItem->item->store_display_name ?? 'Deleted Item' }}</td>
                    <td class="text-center" style="font-weight: bold;">{{ $sItem->quantity }}</td>
                    <td class="text-end">EGP {{ number_format($sItem->unit_price, 2) }}</td>
                    <td class="text-end" style="font-weight: bold;">EGP {{ number_format($sItem->total_price, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="background-color: #f8fafc; font-weight: bold;">
                <td colspan="2" class="text-end">{{ __('messages.Total') }}:</td>
                <td class="text-center">{{ $slip->total_items_count }}</td>
                <td></td>
                <td class="text-end" style="color: #15803d;">EGP {{ number_format($slip->total_value, 2) }}</td>
            </tr>
        </tfoot>
    </table>

    <table class="signatures">
        <tr>
            <td>
                <div><strong>{{ __('messages.delivered_by_signature') }}:</strong> {{ $slip->issuer->name ?? '-' }}</div>
                <div class="signature-line">{{ __('messages.signature_and_date') }}</div>
            </td>
            <td>
                <div><strong>{{ __('messages.received_by_signature') }}:</strong> {{ $slip->receiver->name ?? '.......................' }}</div>
                <div class="signature-line">{{ __('messages.signature_and_date') }}</div>
            </td>
        </tr>
    </table>

</body>
</html>
