<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Form Submission</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            line-height: 1.6;
            color: #1e293b;
            background-color: #f8fafc;
            margin: 0;
            padding: 24px;
        }
        .email-wrapper {
            max-width: 650px;
            margin: 0 auto;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }
        .header {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            color: #ffffff;
            padding: 24px 28px;
            text-align: left;
        }
        .header h1 {
            margin: 0 0 6px 0;
            font-size: 20px;
            font-weight: 700;
            color: #ffffff;
            letter-spacing: -0.02em;
        }
        .header p {
            margin: 0;
            font-size: 14px;
            color: #94a3b8;
        }
        .meta-bar {
            background: #f1f5f9;
            padding: 12px 28px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 13px;
            color: #475569;
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 10px;
        }
        .content {
            padding: 28px;
        }
        .section-divider {
            margin: 24px 0 16px 0;
            padding-bottom: 8px;
            border-bottom: 2px solid #e2e8f0;
            font-size: 15px;
            font-weight: 700;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .field-group {
            margin-bottom: 18px;
            padding-bottom: 14px;
            border-bottom: 1px solid #f1f5f9;
        }
        .field-group:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }
        .field-label {
            font-weight: 600;
            color: #475569;
            font-size: 13px;
            margin-bottom: 5px;
        }
        .field-value {
            color: #0f172a;
            font-size: 14px;
            word-break: break-word;
        }
        .badge {
            display: inline-block;
            background: #e0f2fe;
            color: #0369a1;
            padding: 3px 10px;
            border-radius: 9999px;
            font-size: 12px;
            font-weight: 500;
            margin: 2px 4px 2px 0;
        }
        .badge-yes {
            background: #dcfce7;
            color: #15803d;
        }
        .badge-no {
            background: #fee2e2;
            color: #b91c1c;
        }
        .details-box {
            margin-top: 6px;
            padding: 8px 12px;
            background: #f8fafc;
            border-left: 3px solid #cbd5e1;
            border-radius: 4px;
            font-size: 13px;
            color: #334155;
        }
        .table-responsive {
            width: 100%;
            overflow-x: auto;
            margin-top: 8px;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
            text-align: left;
        }
        .data-table th {
            background: #f1f5f9;
            color: #334155;
            font-weight: 600;
            padding: 8px 12px;
            border: 1px solid #e2e8f0;
        }
        .data-table td {
            padding: 8px 12px;
            border: 1px solid #e2e8f0;
            color: #1e293b;
        }
        .data-table tr:nth-child(even) {
            background-color: #fafbfc;
        }
        .footer {
            background: #f8fafc;
            padding: 16px 28px;
            text-align: center;
            border-top: 1px solid #e2e8f0;
            font-size: 12px;
            color: #94a3b8;
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="header">
            <h1>New Form Submission</h1>
            <p>{{ $form->title }}@if(!empty($form->settings['subtitle'])) &mdash; {{ $form->settings['subtitle'] }}@endif</p>
        </div>
        
        <div class="meta-bar">
            <div>
                <strong>Submitted At:</strong> {{ $submission->created_at ? $submission->created_at->format('Y-m-d H:i:s') : now()->format('Y-m-d H:i:s') }}
            </div>
            <div>
                <strong>Submitted By:</strong> {{ $submission->user ? $submission->user->name : 'Guest' }}
            </div>
        </div>

        <div class="content">
            @foreach ($form->fields as $field)
                @if ($field->type === 'section_header')
                    <div class="section-divider">{{ $field->label }}</div>
                @elseif ($field->type !== 'static_text')
                    @php
                        $val = $submission->data[$field->id] ?? null;
                    @endphp
                    <div class="field-group">
                        <div class="field-label">{{ $field->label }}</div>
                        <div class="field-value">
                            @if ($field->type === 'table')
                                @php
                                    $rawCols = $field->options['columns'] ?? [];
                                    $columns = is_array($rawCols) ? $rawCols : (is_string($rawCols) && !empty($rawCols) ? array_filter(array_map('trim', explode(',', $rawCols))) : []);
                                @endphp
                                @if (!empty($val) && is_array($val))
                                    <div class="table-responsive">
                                        <table class="data-table">
                                            @if (!empty($columns))
                                                <thead>
                                                    <tr>
                                                        @foreach ($columns as $col)
                                                            <th>{{ $col }}</th>
                                                        @endforeach
                                                    </tr>
                                                </thead>
                                            @endif
                                            <tbody>
                                                @foreach ($val as $row)
                                                    <tr>
                                                        @if (is_array($row))
                                                            @foreach ($row as $cell)
                                                                <td>{{ is_scalar($cell) ? $cell : json_encode($cell) }}</td>
                                                            @endforeach
                                                        @else
                                                            <td colspan="{{ !empty($columns) ? count($columns) : 1 }}">{{ $row }}</td>
                                                        @endif
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <span style="color: #94a3b8; font-style: italic;">No rows provided</span>
                                @endif

                            @elseif ($field->type === 'yes_no_textbox')
                                @if (is_array($val))
                                    @php
                                        $ans = strtolower(trim($val['answer'] ?? ''));
                                        $details = trim($val['details'] ?? '');
                                    @endphp
                                    @if ($ans === 'yes')
                                        <span class="badge badge-yes">Yes</span>
                                    @elseif ($ans === 'no')
                                        <span class="badge badge-no">No</span>
                                    @else
                                        <span class="badge">{{ $ans ?: '-' }}</span>
                                    @endif

                                    @if (!empty($details))
                                        <div class="details-box">
                                            <strong>Details:</strong> {{ $details }}
                                        </div>
                                    @endif
                                @elseif (is_scalar($val) && !empty($val))
                                    @php $ans = strtolower(trim($val)); @endphp
                                    @if ($ans === 'yes')
                                        <span class="badge badge-yes">Yes</span>
                                    @elseif ($ans === 'no')
                                        <span class="badge badge-no">No</span>
                                    @else
                                        <span class="badge">{{ $val }}</span>
                                    @endif
                                @else
                                    <span style="color: #94a3b8;">-</span>
                                @endif

                            @elseif (in_array($field->type, ['groups', 'cities', 'neighborhoods', 'committees', 'servicebodies']))
                                @php
                                    $entityItem = isset($entities[$field->type][$val]) ? $entities[$field->type][$val] : null;
                                    $entityName = $entityItem ? ($entityItem->name ?? $entityItem->title ?? $val) : $val;
                                @endphp
                                {{ $entityName ?: '-' }}

                            @elseif (is_array($val))
                                @foreach ($val as $item)
                                    @if (is_scalar($item))
                                        <span class="badge">{{ $item }}</span>
                                    @else
                                        <span class="badge">{{ json_encode($item) }}</span>
                                    @endif
                                @endforeach

                            @else
                                {!! !empty($val) ? nl2br(e($val)) : '<span style="color: #94a3b8;">-</span>' !!}
                            @endif
                        </div>
                    </div>
                @endif
            @endforeach
        </div>

        <div class="footer">
            This notification was automatically sent by NA Egypt Website Form Builder.
        </div>
    </div>
</body>
</html>
