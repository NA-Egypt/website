<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <title>{{ $slip->slip_number }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', 'sans-serif';
            font-size: 11px;
            color: #1e293b;
            line-height: 1.5;
        }
        .header {
            margin-bottom: 20px;
            border-bottom: 2px solid {{ $slip->type === 'issue_to_committee' ? '#0d9488' : '#e11d48' }};
            padding-bottom: 12px;
        }
        .title {
            font-size: 17px;
            font-weight: bold;
            color: {{ $slip->type === 'issue_to_committee' ? '#0f766e' : '#be123c' }};
        }
        .subtitle {
            font-size: 12px;
            color: #64748b;
            margin-top: 3px;
        }
        .meta {
            font-size: 10px;
            color: #475569;
            margin-top: 6px;
        }
        .badge-no-invoice {
            display: inline-block;
            background-color: #f1f5f9;
            border: 1px solid #cbd5e1;
            color: #475569;
            font-size: 10px;
            font-weight: bold;
            padding: 4px 10px;
            border-radius: 4px;
            margin-top: 5px;
        }
        .summary-box {
            margin-bottom: 20px;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 12px;
            border-radius: 6px;
        }
        .summary-grid {
            width: 100%;
        }
        .summary-grid td {
            padding: 4px 8px;
            vertical-align: top;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            margin-bottom: 25px;
        }
        table.data-table th, table.data-table td {
            border: 1px solid #cbd5e1;
            padding: 8px 10px;
            text-align: {{ app()->getLocale() === 'ar' ? 'right' : 'left' }};
        }
        table.data-table th {
            background-color: #f1f5f9;
            font-weight: bold;
            color: #334155;
        }
        .text-center { text-align: center !important; }
        .text-end { text-align: {{ app()->getLocale() === 'ar' ? 'left' : 'right' }} !important; }
        .text-start { text-align: {{ app()->getLocale() === 'ar' ? 'right' : 'left' }} !important; }
        .signatures {
            width: 100%;
            margin-top: 40px;
        }
        .signatures td {
            width: 50%;
            padding: 12px;
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
                        @if($slip->type === 'issue_to_committee')
                            {{ __('messages.committee_issue_slip_title') ?? 'Literature Committee Delivery Slip' }}
                            <br><small style="font-size: 13px;">(إذن تسليم مطبوعات للجنة خدمية)</small>
                        @else
                            {{ __('messages.committee_return_slip_title') ?? 'Committee Remains Return Slip' }}
                            <br><small style="font-size: 13px;">(إذن ارتجاع متبقي مطبوعات من لجنة خدمية)</small>
                        @endif
                    </div>
                    <div class="subtitle">
                        {{ $slip->type === 'issue_to_committee' ? 'Literature Committee &rarr; ' . ($slip->serviceCommittee?->ar_name ?: 'Service Committee') : ($slip->serviceCommittee?->ar_name ?: 'Service Committee') . ' &rarr; Literature Committee' }}
                    </div>
                    <div class="badge-no-invoice">
                        {{ __('messages.operational_transfer_no_invoice') ?? 'Non-Billable Operational Transfer / تسليم تشغيلي بدون فاتورة' }}
                    </div>
                </td>
                <td style="width: 30%; text-align: {{ app()->getLocale() === 'ar' ? 'left' : 'right' }};">
                    <span style="font-size: 13px; font-weight: bold; color: #475569;">
                        {{ config('app.name', 'NA Egypt') }}
                    </span>
                    <div class="meta">
                        {{ __('messages.slip_number') }}: <strong>{{ $slip->slip_number }}</strong><br>
                        {{ __('messages.Date') }}: {{ $slip->created_at->format('Y-m-d H:i') }}
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <div class="summary-box">
        <table class="summary-grid">
            <tr>
                <td style="width: 50%;">
                    <strong>{{ __('messages.service_committee') ?? 'Service Committee' }}:</strong>
                    {{ $slip->serviceCommittee?->ar_name }} ({{ $slip->serviceCommittee?->en_name }})
                </td>
                <td style="width: 50%;">
                    <strong>{{ __('messages.total_items_count') }}:</strong>
                    <span style="font-size: 13px; font-weight: bold;">{{ $slip->total_items_count }}</span>
                </td>
            </tr>
            <tr>
                <td>
                    <strong>{{ __('messages.issued_by') }}:</strong>
                    {{ $slip->issuer?->name ?? 'System' }}
                </td>
                <td>
                    <strong>{{ __('messages.received_by') }}:</strong>
                    {{ $slip->receiver?->name ?? ($slip->status === 'received' ? 'Acknowledged' : __('messages.pending_receipt') ?? 'Pending Receipt') }}
                </td>
            </tr>
            @if($slip->notes)
            <tr>
                <td colspan="2" style="border-top: 1px dotted #e2e8f0; padding-top: 6px;">
                    <strong>{{ __('messages.notes') }}:</strong> {{ $slip->notes }}
                </td>
            </tr>
            @endif
        </table>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th class="text-center" style="width: 8%;">#</th>
                <th>{{ __('messages.item_name') }}</th>
                <th>{{ __('messages.item_name_en') }}</th>
                <th style="width: 20%;">{{ __('messages.category') }}</th>
                <th class="text-center" style="width: 15%;">{{ __('messages.quantity') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($slip->items as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td><strong>{{ $item->item->name }}</strong></td>
                    <td style="color: #64748b;">{{ $item->item->name_en ?: '-' }}</td>
                    <td>{{ $item->item->category ?: '-' }}</td>
                    <td class="text-center" style="font-size: 13px; font-weight: bold;">{{ $item->quantity }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="background-color: #f8fafc; font-weight: bold;">
                <td colspan="4" class="text-end">{{ __('messages.total_items_count') }}:</td>
                <td class="text-center" style="font-size: 14px;">{{ $slip->total_items_count }}</td>
            </tr>
        </tfoot>
    </table>

    <table class="signatures">
        <tr>
            <td>
                <div><strong>{{ __('messages.handed_over_by') ?? 'Handed Over By / تم التسليم بواسطة' }}:</strong></div>
                <div style="font-size: 10px; color: #64748b; margin-top: 3px;">
                    {{ __('messages.literature_committee_rep') ?? 'Literature Committee Representative / مندوب لجنة المطبوعات' }}
                </div>
                <div class="signature-line">
                    {{ $slip->issuer?->name ?? 'Name & Signature' }}
                </div>
            </td>
            <td>
                <div><strong>{{ __('messages.received_by') ?? 'Received By / تم الاستلام بواسطة' }}:</strong></div>
                <div style="font-size: 10px; color: #64748b; margin-top: 3px;">
                    {{ $slip->serviceCommittee?->ar_name ?: 'Committee Representative' }}
                </div>
                <div class="signature-line">
                    {{ $slip->receiver?->name ?? ($slip->status === 'received' ? 'Acknowledged' : 'Name & Signature') }}
                </div>
            </td>
        </tr>
    </table>

</body>
</html>
